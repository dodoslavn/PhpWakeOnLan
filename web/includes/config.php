<?php
define('DATA_DIR', __DIR__ . '/../data/');
define('HOSTS_FILE', DATA_DIR . 'hosts.json');
define('USERS_FILE', DATA_DIR . 'users.json');
define('CONFIG_FILE', DATA_DIR . 'config.json');

function load_json(string $file): array {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function save_json(string $file, array $data): bool {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function get_config(): array {
    $defaults = ['wol_binary' => '/usr/bin/wakeonlan', 'app_name' => 'Wake On LAN'];
    return array_merge($defaults, load_json(CONFIG_FILE));
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generate_id(): string {
    return bin2hex(random_bytes(8));
}
