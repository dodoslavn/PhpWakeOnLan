<?php
require_once __DIR__ . '/config.php';

function session_init(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function is_logged_in(): bool {
    session_init();
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        die('Access denied.');
    }
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role'     => $_SESSION['user_role'],
    ];
}

function attempt_login(string $username, string $password): bool {
    $users = load_json(USERS_FILE);
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password_hash'])) {
            session_init();
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
    }
    return false;
}

function do_logout(): void {
    session_init();
    $_SESSION = [];
    session_destroy();
}

function get_all_users(): array {
    return load_json(USERS_FILE);
}

function find_user_by_id(string $id): ?array {
    foreach (get_all_users() as $u) {
        if ($u['id'] === $id) return $u;
    }
    return null;
}

function save_user(array $user): bool {
    $users = get_all_users();
    foreach ($users as &$u) {
        if ($u['id'] === $user['id']) {
            $u = $user;
            return save_json(USERS_FILE, $users);
        }
    }
    $users[] = $user;
    return save_json(USERS_FILE, $users);
}

function delete_user(string $id): bool {
    $users = array_values(array_filter(get_all_users(), fn($u) => $u['id'] !== $id));
    return save_json(USERS_FILE, $users);
}

function username_taken(string $username, string $exclude_id = ''): bool {
    foreach (get_all_users() as $u) {
        if ($u['username'] === $username && $u['id'] !== $exclude_id) return true;
    }
    return false;
}
