<?php
require_once __DIR__ . '/config.php';

function wake_host(string $mac, string $broadcast = ''): array {
    $config  = get_config();
    $binary  = $config['wol_binary'];

    if (!file_exists($binary) || !is_executable($binary)) {
        return ['success' => false, 'message' => "WoL binary not found or not executable: $binary"];
    }

    if (!preg_match('/^([0-9A-Fa-f]{2}[:\-]){5}[0-9A-Fa-f]{2}$/', $mac)) {
        return ['success' => false, 'message' => 'Invalid MAC address format'];
    }

    $args = [escapeshellarg($binary)];
    if ($broadcast !== '') {
        $args[] = '-i';
        $args[] = escapeshellarg($broadcast);
    }
    $args[] = escapeshellarg($mac);

    $cmd    = implode(' ', $args) . ' 2>&1';
    exec($cmd, $output, $code);

    return [
        'success' => $code === 0,
        'message' => implode("\n", $output) ?: ($code === 0 ? 'Magic packet sent.' : 'Command failed.'),
    ];
}

function get_all_hosts(): array {
    return load_json(HOSTS_FILE);
}

function find_host_by_id(string $id): ?array {
    foreach (get_all_hosts() as $h) {
        if ($h['id'] === $id) return $h;
    }
    return null;
}

function save_host(array $host): bool {
    $hosts = get_all_hosts();
    foreach ($hosts as &$h) {
        if ($h['id'] === $host['id']) {
            $h = $host;
            return save_json(HOSTS_FILE, $hosts);
        }
    }
    $hosts[] = $host;
    return save_json(HOSTS_FILE, $hosts);
}

function delete_host(string $id): bool {
    $hosts = array_values(array_filter(get_all_hosts(), fn($h) => $h['id'] !== $id));
    return save_json(HOSTS_FILE, $hosts);
}
