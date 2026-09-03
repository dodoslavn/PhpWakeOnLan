<?php
function render_head(string $title, string $extra_head = ''): void {
    $cfg = get_config();
    $app = e($cfg['app_name']);
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$app} — {$title}</title>
<link rel="stylesheet" href="css/style.css">
{$extra_head}
</head>
<body>
HTML;
}

function render_sidebar(string $active): void {
    $user = current_user();
    $name = e($user['username'] ?? '');
    $role = $user['role'] ?? 'user';

    $links = [
        ['href' => 'index.php',    'icon' => '⚡', 'label' => 'Hosts',    'key' => 'hosts'],
        ['href' => 'users.php',    'icon' => '👥', 'label' => 'Users',    'key' => 'users',    'admin' => true],
        ['href' => 'settings.php', 'icon' => '⚙️', 'label' => 'Settings', 'key' => 'settings', 'admin' => true],
    ];

    echo '<div class="layout"><aside class="sidebar">';
    echo '<div class="sidebar-brand"><div class="logo">&#9889; Wake On LAN</div><div class="sub">Remote power manager</div></div>';
    echo '<nav class="sidebar-nav">';
    echo '<div class="nav-section">Navigation</div>';

    foreach ($links as $l) {
        if (!empty($l['admin']) && $role !== 'admin') continue;
        $cls = ($active === $l['key']) ? 'active' : '';
        echo '<div class="nav-item"><a href="' . e($l['href']) . '" class="' . $cls . '">';
        echo '<span>' . $l['icon'] . '</span> ' . e($l['label']);
        echo '</a></div>';
    }

    echo '</nav>';
    echo '<div class="sidebar-footer">';
    echo 'Logged in as <strong>' . $name . '</strong><br>';
    echo '<a href="logout.php">Sign out</a>';
    echo '</div>';
    echo '</aside><main class="main">';
}

function render_footer(string $js = ''): void {
    echo '</main></div>';
    echo '<div id="toast-container"></div>';
    if ($js) echo '<script>' . $js . '</script>';
    echo <<<'JS'
<script>
function toast(msg, type = 'success') {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast toast-' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.overlay.open').forEach(o => {
            o.classList.remove('open');
            document.body.style.overflow = '';
        });
    }
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('overlay')) {
        e.target.classList.remove('open');
        document.body.style.overflow = '';
    }
});

function confirm_action(msg, callback) {
    const overlay = document.getElementById('confirm-overlay');
    document.getElementById('confirm-msg').textContent = msg;
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    const yes = document.getElementById('confirm-yes');
    const no  = document.getElementById('confirm-no');

    const cleanup = () => {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        yes.replaceWith(yes.cloneNode(true));
        no.replaceWith(no.cloneNode(true));
    };

    document.getElementById('confirm-yes').addEventListener('click', () => { cleanup(); callback(); });
    document.getElementById('confirm-no').addEventListener('click', cleanup);
}
</script>
<div id="confirm-overlay" class="overlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Confirm</span>
    </div>
    <p id="confirm-msg" style="margin-bottom:1.25rem;color:var(--muted);font-size:.9rem"></p>
    <div style="display:flex;gap:.5rem;justify-content:flex-end">
      <button id="confirm-no"  class="btn btn-ghost btn-sm">Cancel</button>
      <button id="confirm-yes" class="btn btn-danger btn-sm">Confirm</button>
    </div>
  </div>
</div>
</body></html>
JS;
}
