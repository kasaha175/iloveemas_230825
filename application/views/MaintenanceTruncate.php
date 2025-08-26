<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$allowed = $allowed ?? [];
$counts  = $counts  ?? [];
$totalTables = count($allowed);
$summary = $this->session->flashdata('summary');
?>

<style>
  /* ===== Scoped styles (prefix .mt) agar aman dari bentrok ===== */
  .mt-page { margin-top: 6px; }

  /* Breadcrumb (di luar card) */
  .mt-bc{
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; border:1px solid #e6eefc; border-radius:14px;
    background: linear-gradient(135deg, var(--blue-pastel, #B1F0F7), #f3fbff);
    box-shadow: 0 6px 14px rgba(0,0,0,.05);
    margin-bottom: 12px;
  }
  .mt-bc .trail{ display:flex; align-items:center; flex-wrap:wrap; gap:8px; font-size:12.5px; color:#4b5f86; }
  .mt-bc .trail a{ color: var(--blue-light, #074799); font-weight:600; text-decoration:none; }
  .mt-bc .sep{ opacity:.45; }
  .mt-bc .current{ color:#6b7a99; font-weight:600; }

  /* Card */
  .mt-card{
    background:#fff; border:1px solid #e6eefc; border-radius:18px;
    box-shadow:0 18px 30px rgba(0,0,0,.08); padding:16px;
  }
  .mt-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:8px;
  }
  .mt-title{ margin:2px 0; font-weight:800; color:#0e204a; font-size:clamp(18px,2.4vw,24px); }
  .mt-sub{ margin:0 0 8px; font-size:13px; color:#546a96 }
  .mt-sub em{ color:#7a8eb8 }

  /* Badges & notes */
  .mt-badge{
    display:inline-flex; align-items:center; gap:8px;
    padding:6px 10px; border-radius:999px; border:1px solid #dfeaff; background:#fff; color:#0e2b68; font-size:12px;
  }
  .mt-badge.error{ border-color:#ffd7d7; background:#fff4f4; color:#9a1a1a; }
  .mt-badge.ok   { border-color:#c9f6d1; background:#ecffef; color:#155724; }
  .mt-note{ font-size:12px; color:#6b7a99; }

  /* Table (responsive + sticky header) */
  .mt-twrap{ width:100%; overflow:auto; -webkit-overflow-scrolling:touch; border-radius:12px; }
  .mt-tbl{ width:100%; min-width:760px; border-collapse:collapse; }
  .mt-tbl thead th{
    position:sticky; top:0; z-index:1;
    background:linear-gradient(135deg, var(--blue-pastel, #B1F0F7), #e9f6ff);
    color:#0e204a; font-weight:700; font-size:13px; border-bottom:1px solid #dfeaff; padding:10px 12px;
    text-align:left;
  }
  .mt-tbl td{ border-bottom:1px solid #eef3ff; padding:10px 12px; vertical-align:middle; }
  .mt-tbl tr:nth-child(even){ background:#fcfeff; }
  .mt-tbl tr:hover{ background:#f7fbff; }
  .mt-code{ color:#d23535; font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

  /* Grid inputs */
  .mt-grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:10px; }
  @media (max-width: 768px){ .mt-grid{ grid-template-columns:1fr; } }

  /* Actions */
  .mt-actions{ margin-top:14px; display:flex; gap:10px; align-items:center; justify-content:flex-end; flex-wrap:wrap; }
  .mt-btn{
    appearance:none; border:0; cursor:pointer; border-radius:12px; padding:12px 16px;
    font-weight:700; display:inline-flex; align-items:center; gap:10px; text-decoration:none;
    transition: transform .06s ease, box-shadow .2s ease, filter .2s ease, border-color .2s ease, background .2s ease;
  }
  .mt-btn:active{ transform:translateY(1px); }
  .btn-back{
    border:1px solid rgba(7,71,153,.28); background:#fff; color:#143567;
  }
  .btn-back:hover{ border-color: var(--blue-light, #074799); box-shadow:0 6px 16px rgba(7,71,153,.12); background:#fafdff; }
  .btn-danger{
    color:#fff; background:linear-gradient(135deg, #e45757, #d23535);
    box-shadow:0 10px 20px rgba(210,53,53,.25);
  }
  .btn-danger:hover{ box-shadow:0 14px 28px rgba(210,53,53,.28); filter:brightness(1.02); }
  .btn-danger:disabled{ opacity:.6; cursor:not-allowed; }

  /* Little helpers */
  .mt-chip{
    display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px;
    background:#fff; border:1px solid #e6eefc; font-size:12px; color:#2a3d6b;
  }
  .mt-right{ text-align:right; }
</style>

<div class="mt-page">
  <!-- Breadcrumb -->
  <div class="mt-bc" aria-label="Breadcrumb">
    <div class="trail">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a><span class="sep">/</span>
      <span class="current">Data Maintenance</span><span class="sep">/</span>
      <span class="current">Truncate Tables</span>
    </div>
  </div>

  <div class="mt-card">
    <div class="mt-head">
      <h3 class="mt-title">Data Maintenance — Truncate Tables</h3>
      <div class="mt-chip" id="mtSelected" aria-live="polite">
        <i class="fas fa-check-square"></i><span>0 dari <?= (int)$totalTables ?> dipilih</span>
      </div>
    </div>
    <p class="mt-sub">
      Aksi ini <strong>menghapus permanen</strong> data pada tabel yang dipilih. Pastikan Anda <em>sudah melakukan backup</em>.
    </p>

    <?php if ($msg = $this->session->flashdata('error')): ?>
      <div class="mt-badge error" style="margin-bottom:8px;"><i class="fas fa-exclamation-triangle"></i> <?= html_escape($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = $this->session->flashdata('success')): ?>
      <div class="mt-badge ok" style="margin-bottom:8px;"><i class="fas fa-check-circle"></i> <?= html_escape($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($summary)): ?>
      <div class="mt-badge" style="margin:6px 0;"><i class="fas fa-clipboard-list"></i> Ringkasan eksekusi</div>
      <pre style="white-space:pre-wrap;background:#f8fbff;border:1px solid #e6eefc;border-radius:12px;padding:10px;margin-top:6px;font-size:12px">
<?= html_escape(print_r($summary, true)) ?>
      </pre>
    <?php endif; ?>

    <form action="<?= base_url('maintenance/truncate') ?>" method="post" id="truncateForm">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
             value="<?= $this->security->get_csrf_hash(); ?>">

      <div class="mt-twrap">
        <table class="mt-tbl" aria-describedby="dryRun">
          <thead>
            <tr>
              <th style="width:54px">
                <label class="m-0" title="Pilih semua">
                  <input type="checkbox" id="chkAll"> <span class="sr-only">Pilih semua</span>
                </label>
              </th>
              <th>Nama Tabel</th>
              <th>Deskripsi</th>
              <th class="mt-right" style="width:160px">Jumlah Baris</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($allowed as $tbl => $label): $cnt = $counts[$tbl] ?? null; ?>
            <tr>
              <td><input type="checkbox" class="js-tbl" name="tables[]" value="<?= $tbl ?>"></td>
              <td><code class="mt-code"><?= $tbl ?></code></td>
              <td><?= html_escape($label) ?></td>
              <td class="mt-right"><?= $cnt === null ? '<em>n/a</em>' : number_format($cnt) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-note" id="selInfo" style="margin-top:6px;">0 tabel dipilih.</div>

      <div class="mt-grid">
        <div>
          <label class="mt-note" style="display:flex;gap:8px;align-items:flex-start;margin:6px 0;">
            <input type="checkbox" id="ack" name="ack" style="margin-top:3px;">
            <span>Saya memahami aksi ini <strong>menghapus permanen</strong> data.</span>
          </label>

          <div class="mt-note">Ketik <code>TRUNCATE</code> untuk konfirmasi:</div>
          <input type="text" name="confirm_text" id="confirmText" class="form-control" placeholder="TRUNCATE" autocomplete="off">
        </div>

        <div>
          <div class="mt-note">Masukkan ulang password Administrator:</div>
          <input type="password" name="admin_password" id="adminPassword" class="form-control" placeholder="Password admin" autocomplete="current-password">
        </div>
      </div>

      <div class="mt-actions">
        <a href="<?= base_url('dashboard') ?>" class="mt-btn btn-back">
          <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <button type="submit" class="mt-btn btn-danger" id="btnExec" disabled>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          Eksekusi Truncate
        </button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  var form   = document.getElementById('truncateForm');
  var btn    = document.getElementById('btnExec');
  var ack    = document.getElementById('ack');
  var ctext  = document.getElementById('confirmText');
  var pwd    = document.getElementById('adminPassword');
  var info   = document.getElementById('selInfo');
  var chip   = document.getElementById('mtSelected');
  var chkAll = document.getElementById('chkAll');
  var checks = [].slice.call(document.querySelectorAll('.js-tbl'));
  var total  = <?= (int)$totalTables ?>;

  function selectedCount(){
    return checks.filter(c => c.checked).length;
  }
  function updateCounter(){
    var n = selectedCount();
    if (info) info.textContent = n + ' tabel dipilih.';
    if (chip) chip.querySelector('span').textContent = n + ' dari ' + total + ' dipilih';
  }
  function updateState(){
    updateCounter();
    var ok = selectedCount() > 0 && ack.checked && (ctext.value.trim() === 'TRUNCATE') && (pwd.value.trim() !== '');
    btn.disabled = !ok;
  }

  // Select all
  if (chkAll){
    chkAll.addEventListener('change', function(){
      checks.forEach(c => { c.checked = chkAll.checked; });
      updateState();
    });
  }

  checks.forEach(c => c.addEventListener('change', function(){
    // sinkronkan status select all
    if (chkAll){
      chkAll.checked = selectedCount() === checks.length;
      chkAll.indeterminate = selectedCount() > 0 && selectedCount() < checks.length;
    }
    updateState();
  }));

  [ack, ctext, pwd].forEach(el => el.addEventListener('input', updateState));
  updateState();

  form.addEventListener('submit', function(e){
    var n = selectedCount();
    if (n === 0) { e.preventDefault(); alert('Pilih minimal satu tabel.'); return; }
    if (!confirm('Yakin mengosongkan ' + n + ' tabel? Aksi tidak bisa dibatalkan.')) { e.preventDefault(); return; }
    btn.disabled = true; var sp = btn.querySelector('.spinner-border'); if (sp) sp.classList.remove('d-none');
  });
})();
</script>
