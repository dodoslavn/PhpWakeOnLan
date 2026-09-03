<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/wol.php';
require_once 'includes/layout.php';

require_login();

$user  = current_user();
$hosts = get_all_hosts();

render_head('Hosts');
render_sidebar('hosts');
?>

<div class="page-header">
  <div>
    <div class="page-title">Hosts</div>
    <div class="page-subtitle"><?= count($hosts) ?> host<?= count($hosts) !== 1 ? 's' : '' ?> configured</div>
  </div>
  <div class="page-actions">
    <button class="btn btn-primary" onclick="openModal('add-modal')">&#43; Add host</button>
  </div>
</div>

<div class="stats">
  <div class="stat-tile">
    <div class="stat-label">Total hosts</div>
    <div class="stat-value"><?= count($hosts) ?></div>
  </div>
</div>

<div class="card">
  <div class="card-header">Host list</div>
  <?php if (empty($hosts)): ?>
    <div class="empty">
      <div class="empty-icon">&#128187;</div>
      <p>No hosts configured yet.</p>
      <button class="btn btn-primary" onclick="openModal('add-modal')">Add your first host</button>
    </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>MAC address</th>
          <th>Broadcast IP</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="host-list">
        <?php foreach ($hosts as $h): ?>
        <tr id="row-<?= e($h['id']) ?>">
          <td><strong><?= e($h['name']) ?></strong></td>
          <td><span class="mono"><?= e($h['mac']) ?></span></td>
          <td><?= $h['broadcast'] ? e($h['broadcast']) : '<span style="color:var(--muted)">—</span>' ?></td>
          <td style="color:var(--muted)"><?= e($h['description'] ?? '') ?></td>
          <td>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap">
              <button class="btn btn-success btn-sm"
                      onclick="wake('<?= e($h['id']) ?>', '<?= e($h['name']) ?>', this)">
                &#9889; Wake
              </button>
              <button class="btn btn-ghost btn-sm"
                      onclick="editHost(<?= json_encode($h) ?>)">
                &#9998; Edit
              </button>
              <button class="btn btn-danger btn-sm"
                      onclick="deleteHost('<?= e($h['id']) ?>', '<?= e($h['name']) ?>')">
                &#128465; Delete
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Add modal -->
<div id="add-modal" class="overlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add host</span>
      <button class="close-btn" onclick="closeModal('add-modal')">&times;</button>
    </div>
    <form id="add-form">
      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" placeholder="e.g. Gaming PC" required>
      </div>
      <div class="form-group">
        <label>MAC address *</label>
        <input type="text" name="mac" placeholder="AA:BB:CC:DD:EE:FF" required class="mono">
        <div class="hint">Format: AA:BB:CC:DD:EE:FF or AA-BB-CC-DD-EE-FF</div>
      </div>
      <div class="form-group">
        <label>Broadcast IP <span style="font-weight:400">(optional)</span></label>
        <input type="text" name="broadcast" placeholder="192.168.1.255">
        <div class="hint">Leave empty to use default broadcast (255.255.255.255)</div>
      </div>
      <div class="form-group">
        <label>Description <span style="font-weight:400">(optional)</span></label>
        <input type="text" name="description" placeholder="e.g. Living room desktop">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save host</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal('add-modal')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit modal -->
<div id="edit-modal" class="overlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit host</span>
      <button class="close-btn" onclick="closeModal('edit-modal')">&times;</button>
    </div>
    <form id="edit-form">
      <input type="hidden" name="id">
      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" required>
      </div>
      <div class="form-group">
        <label>MAC address *</label>
        <input type="text" name="mac" required class="mono">
      </div>
      <div class="form-group">
        <label>Broadcast IP <span style="font-weight:400">(optional)</span></label>
        <input type="text" name="broadcast">
      </div>
      <div class="form-group">
        <label>Description <span style="font-weight:400">(optional)</span></label>
        <input type="text" name="description">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal('edit-modal')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php
render_footer();
?>
<script>
// ── Wake ─────────────────────────────────────────────────────────
async function wake(id, name, btn) {
    btn.disabled = true;
    btn.textContent = '…';
    btn.classList.add('flashing');
    try {
        const r  = await fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=wake&id=' + encodeURIComponent(id),
        });
        const d = await r.json();
        toast(d.success ? '⚡ Packet sent to ' + name : '✗ ' + d.message, d.success ? 'success' : 'error');
    } catch(e) {
        toast('Request failed', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = '⚡ Wake';
        setTimeout(() => btn.classList.remove('flashing'), 800);
    }
}

// ── Add ──────────────────────────────────────────────────────────
document.getElementById('add-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);
    data.append('action', 'add_host');
    const r = await fetch('api.php', { method: 'POST', body: new URLSearchParams(data) });
    const d = await r.json();
    if (d.success) {
        closeModal('add-modal');
        toast('Host added.');
        setTimeout(() => location.reload(), 400);
    } else {
        toast(d.message, 'error');
    }
});

// ── Edit ─────────────────────────────────────────────────────────
function editHost(h) {
    const f = document.getElementById('edit-form');
    f.id.value          = h.id;
    f.name.value        = h.name;
    f.mac.value         = h.mac;
    f.broadcast.value   = h.broadcast || '';
    f.description.value = h.description || '';
    openModal('edit-modal');
}

document.getElementById('edit-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);
    data.append('action', 'edit_host');
    const r = await fetch('api.php', { method: 'POST', body: new URLSearchParams(data) });
    const d = await r.json();
    if (d.success) {
        closeModal('edit-modal');
        toast('Host updated.');
        setTimeout(() => location.reload(), 400);
    } else {
        toast(d.message, 'error');
    }
});

// ── Delete ───────────────────────────────────────────────────────
function deleteHost(id, name) {
    confirm_action('Delete host "' + name + '"?', async () => {
        const r = await fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=delete_host&id=' + encodeURIComponent(id),
        });
        const d = await r.json();
        if (d.success) {
            document.getElementById('row-' + id)?.remove();
            toast('Host deleted.');
        } else {
            toast(d.message, 'error');
        }
    });
}
</script>
