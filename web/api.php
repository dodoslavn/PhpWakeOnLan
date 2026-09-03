<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/wol.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$action = $_POST['action'] ?? '';
$user   = current_user();

function json_ok(array $extra = []): void {
    echo json_encode(array_merge(['success' => true], $extra));
    exit;
}

function json_err(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

function validate_mac(string $mac): bool {
    return (bool) preg_match('/^([0-9A-Fa-f]{2}[:\-]){5}[0-9A-Fa-f]{2}$/', $mac);
}

function validate_ip(string $ip): bool {
    return $ip === '' || filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

match ($action) {
    'wake' => (function() {
        $id   = trim($_POST['id'] ?? '');
        $host = find_host_by_id($id);
        if (!$host) json_err('Host not found', 404);
        $result = wake_host($host['mac'], $host['broadcast'] ?? '');
        if ($result['success']) json_ok(['message' => $result['message']]);
        json_err($result['message']);
    })(),

    'add_host' => (function() {
        $name      = trim($_POST['name'] ?? '');
        $mac       = strtoupper(trim($_POST['mac'] ?? ''));
        $broadcast = trim($_POST['broadcast'] ?? '');
        $desc      = trim($_POST['description'] ?? '');

        if ($name === '')           json_err('Name is required');
        if (!validate_mac($mac))    json_err('Invalid MAC address format');
        if (!validate_ip($broadcast)) json_err('Invalid broadcast IP');

        $host = [
            'id'          => generate_id(),
            'name'        => $name,
            'mac'         => $mac,
            'broadcast'   => $broadcast,
            'description' => $desc,
            'created_at'  => date('c'),
        ];

        if (!save_host($host)) json_err('Failed to save host');
        json_ok(['host' => $host]);
    })(),

    'edit_host' => (function() {
        $id        = trim($_POST['id'] ?? '');
        $name      = trim($_POST['name'] ?? '');
        $mac       = strtoupper(trim($_POST['mac'] ?? ''));
        $broadcast = trim($_POST['broadcast'] ?? '');
        $desc      = trim($_POST['description'] ?? '');

        $host = find_host_by_id($id);
        if (!$host) json_err('Host not found', 404);

        if ($name === '')           json_err('Name is required');
        if (!validate_mac($mac))    json_err('Invalid MAC address format');
        if (!validate_ip($broadcast)) json_err('Invalid broadcast IP');

        $host['name']        = $name;
        $host['mac']         = $mac;
        $host['broadcast']   = $broadcast;
        $host['description'] = $desc;
        $host['updated_at']  = date('c');

        if (!save_host($host)) json_err('Failed to save host');
        json_ok(['host' => $host]);
    })(),

    'delete_host' => (function() {
        $id = trim($_POST['id'] ?? '');
        if (!find_host_by_id($id)) json_err('Host not found', 404);
        if (!delete_host($id)) json_err('Failed to delete host');
        json_ok();
    })(),

    'add_user' => (function() use ($user) {
        if ($user['role'] !== 'admin') json_err('Forbidden', 403);

        $username  = trim($_POST['username'] ?? '');
        $password  = $_POST['password'] ?? '';
        $role      = $_POST['role'] ?? 'user';

        if ($username === '')         json_err('Username is required');
        if (strlen($password) < 8)   json_err('Password must be at least 8 characters');
        if (!in_array($role, ['admin','user'])) json_err('Invalid role');
        if (username_taken($username)) json_err('Username already taken');

        $new_user = [
            'id'            => generate_id(),
            'username'      => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role,
            'created_at'    => date('c'),
        ];

        if (!save_user($new_user)) json_err('Failed to save user');
        json_ok(['user' => ['id' => $new_user['id'], 'username' => $new_user['username'], 'role' => $new_user['role']]]);
    })(),

    'edit_user' => (function() use ($user) {
        if ($user['role'] !== 'admin') json_err('Forbidden', 403);

        $id       = trim($_POST['id'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $target = find_user_by_id($id);
        if (!$target) json_err('User not found', 404);

        if ($username === '') json_err('Username is required');
        if (!in_array($role, ['admin','user'])) json_err('Invalid role');
        if (username_taken($username, $id)) json_err('Username already taken');

        // Prevent removing last admin
        if ($target['role'] === 'admin' && $role !== 'admin') {
            $admins = array_filter(get_all_users(), fn($u) => $u['role'] === 'admin');
            if (count($admins) <= 1) json_err('Cannot demote the last admin');
        }

        $target['username'] = $username;
        $target['role']     = $role;
        if ($password !== '') {
            if (strlen($password) < 8) json_err('Password must be at least 8 characters');
            $target['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $target['updated_at'] = date('c');

        if (!save_user($target)) json_err('Failed to save user');
        json_ok(['user' => ['id' => $target['id'], 'username' => $target['username'], 'role' => $target['role']]]);
    })(),

    'delete_user' => (function() use ($user) {
        if ($user['role'] !== 'admin') json_err('Forbidden', 403);

        $id = trim($_POST['id'] ?? '');
        if ($id === $user['id']) json_err('Cannot delete your own account');

        $target = find_user_by_id($id);
        if (!$target) json_err('User not found', 404);

        if ($target['role'] === 'admin') {
            $admins = array_filter(get_all_users(), fn($u) => $u['role'] === 'admin');
            if (count($admins) <= 1) json_err('Cannot delete the last admin');
        }

        if (!delete_user($id)) json_err('Failed to delete user');
        json_ok();
    })(),

    default => json_err('Unknown action', 400),
};
