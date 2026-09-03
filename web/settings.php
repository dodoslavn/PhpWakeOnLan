<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/layout.php';

require_admin();

$cfg   = get_config();
$error = '';
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $binary   = trim($_POST['wol_binary'] ?? '');
    $app_name = trim($_POST['app_name'] ?? '');

    if ($binary === '') {
        $error = 'WoL binary path is required.';
    } elseif ($app_name === '') {
        $error = 'App name is required.';
    } else {
        $cfg['wol_binary'] = $binary;
        $cfg['app_name']   = $app_name;
        if (save_json(CONFIG_FILE, $cfg)) {
            $saved = true;
        } else {
            $error = 'Could not write config file. Check permissions on data/.';
        }
    }
}

render_head('Settings');
render_sidebar('settings');
?>

<div class="page-header">
  <div>
    <div class="page-title">Settings</div>
    <div class="page-subtitle">Application configuration</div>
  </div>
</div>

<?php if ($saved): ?>
  <div class="alert alert-success">Settings saved.</div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">General</div>
  <div class="card-body">
    <form method="post">
      <div class="form-group">
        <label for="app_name">Application name</label>
        <input type="text" id="app_name" name="app_name"
               value="<?= e($cfg['app_name']) ?>" required>
        <div class="hint">Shown in the browser title and sidebar.</div>
      </div>
      <div class="form-group">
        <label for="wol_binary">WoL binary path</label>
        <input type="text" id="wol_binary" name="wol_binary"
               value="<?= e($cfg['wol_binary']) ?>" class="mono" required>
        <div class="hint">
          Full path to the <code>wakeonlan</code> binary on the server.
          Common paths: <code>/usr/bin/wakeonlan</code>, <code>/usr/bin/wol</code>.
        </div>
      </div>

      <div class="form-group" style="margin-top:1rem">
        <label style="margin-bottom:.5rem;color:var(--text);font-size:.85rem">Binary status</label>
        <?php
        $bin = $cfg['wol_binary'];
        if (file_exists($bin) && is_executable($bin)):
        ?>
          <div class="alert alert-success" style="margin-bottom:0">
            &#10003; Found and executable: <code><?= e($bin) ?></code>
          </div>
        <?php elseif (file_exists($bin)): ?>
          <div class="alert alert-error" style="margin-bottom:0">
            &#9888; File exists but is not executable: <code><?= e($bin) ?></code>
          </div>
        <?php else: ?>
          <div class="alert alert-error" style="margin-bottom:0">
            &#10005; Not found: <code><?= e($bin) ?></code> &mdash; install with
            <code>apt install wakeonlan</code>
          </div>
        <?php endif; ?>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save settings</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">Data files</div>
  <div class="card-body">
    <table>
      <thead>
        <tr><th>File</th><th>Exists</th><th>Writable</th></tr>
      </thead>
      <tbody>
        <?php foreach ([HOSTS_FILE, USERS_FILE, CONFIG_FILE] as $f): ?>
        <tr>
          <td class="mono" style="font-size:.8rem"><?= e(basename($f)) ?></td>
          <td><?= file_exists($f) ? '<span style="color:var(--success)">&#10003;</span>' : '<span style="color:var(--danger)">&#10005;</span>' ?></td>
          <td><?= is_writable($f) ? '<span style="color:var(--success)">&#10003;</span>' : '<span style="color:var(--danger)">&#10005;</span>' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php render_footer(); ?>
