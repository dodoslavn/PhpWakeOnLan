<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/layout.php';

require_admin();

$me    = current_user();
$users = get_all_users();

render_head('Users');
render_sidebar('users');
?>

<div class="page-header">
  <div>
    <div class="page-title">Users</div>
    <div class="page-subtitle"><?= count($users) ?> account<?= count($users) !== 1 ? 's' : '' ?></div>
  </div>
  <div class="page-actions">
    <button class="btn btn-primary" onclick="openModal('add-user-modal')">&#43; Add user</button>
  </div>
</div>

<div class="card">
  <div class="card-header">Accounts</div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Username</th>
          <th>Role</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="user-list">
        <?php foreach ($users as $u): ?>
        <tr id="urow-<?= e($u['id']) ?>">
          <td>
            <strong><?= e($u['username']) ?></strong>
            <?php if ($u['id'] === $me['id']): ?>
              <span style="font-size:.72rem;color:var(--muted);margin-left:.4rem">(you)</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge badge-<?= e($u['role']) ?>"><?= e($u['role']) ?></span>
          </td>
          <td style="color:var(--muted);font-size:.82rem">
            <?= isset($u['created_at']) ? date('Y-m-d', strtotime($u['created_at'])) : '—' ?>
          </td>
          <td>
            <div style="display:flex;gap:.4rem">
              <button class="btn btn-ghost btn-sm"
                      onclick="editUser(<?= json_encode([
                          'id'       => $u['id'],
                          'username' => $u['username'],
                          'role'     => $u['role'],
                      ]) ?>)">
                &#9998; Edit
              </button>
              <?php if ($u['id'] !== $me['id']): ?>
              <button class="btn btn-danger btn-sm"
                      onclick="deleteUser('<?= e($u['id']) ?>', '<?= e($u['username']) ?>')">
                &#128465; Delete
              </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add user modal -->
<div id="add-user-modal" class="overlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add user</span>
      <button class="close-btn" onclick="closeModal('add-user-modal')">&times;</button>
    </div>
    <form id="add-user-form">
      <div class="form-group">
        <label>Username *</label>
        <input type="text" name="username" autocomplete="off" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password *</label>
          <input type="password" name="password" autocomplete="new-password" required>
          <div class="hint">Min. 8 characters</div>
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create user</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal('add-user-modal')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit user modal -->
<div id="edit-user-modal" class="overlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit user</span>
      <button class="close-btn" onclick="closeModal('edit-user-modal')">&times;</button>
    </div>
    <form id="edit-user-form">
      <input type="hidden" name="id">
      <div class="form-group">
        <label>Username *</label>
        <input type="text" name="username" autocomplete="off" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>New password</label>
          <input type="password" name="password" autocomplete="new-password">
          <div class="hint">Leave blank to keep current</div>
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal('edit-user-modal')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php render_footer(); ?>
<script>
// ── Add user ─────────────────────────────────────────────────────
document.getElementById('add-user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);
    data.append('action', 'add_user');
    const r = await fetch('api.php', { method: 'POST', body: new URLSearchParams(data) });
    const d = await r.json();
    if (d.success) {
        closeModal('add-user-modal');
        toast('User created.');
        setTimeout(() => location.reload(), 400);
    } else {
        toast(d.message, 'error');
    }
});

// ── Edit user ────────────────────────────────────────────────────
function editUser(u) {
    const f = document.getElementById('edit-user-form');
    f.id.value       = u.id;
    f.username.value = u.username;
    f.password.value = '';
    f.role.value     = u.role;
    openModal('edit-user-modal');
}

document.getElementById('edit-user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);
    data.append('action', 'edit_user');
    const r = await fetch('api.php', { method: 'POST', body: new URLSearchParams(data) });
    const d = await r.json();
    if (d.success) {
        closeModal('edit-user-modal');
        toast('User updated.');
        setTimeout(() => location.reload(), 400);
    } else {
        toast(d.message, 'error');
    }
});

// ── Delete user ──────────────────────────────────────────────────
function deleteUser(id, name) {
    confirm_action('Delete user "' + name + '"?', async () => {
        const r = await fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=delete_user&id=' + encodeURIComponent(id),
        });
        const d = await r.json();
        if (d.success) {
            document.getElementById('urow-' + id)?.remove();
            toast('User deleted.');
        } else {
            toast(d.message, 'error');
        }
    });
}
</script>
