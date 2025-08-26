<!-- application/views/MaintenanceTruncate.php -->
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$allowed = $allowed ?? [];
$counts  = $counts  ?? [];
$summary = $this->session->flashdata('summary');
?>
<style>
  /* Tidak membuat :root baru. Ambil dari UserTemplate:
     --blue-light, --blue-dark, --blue-pastel */

  .mt-wrap{ margin-top: 8px; } /* padding top sudah di UserTemplate.page-wrap */
  .mt-card{
    background:#fff; border:1px solid #e6eefc; border-radius:18px;
    box-shadow:0 18px 30px rgba(0,0,0,.08); padding:16px;
  }
  .mt-title{
    margin:0 0 8px; font-weight:800; color:#0e204a; font-size:clamp(18px,2.4vw,24px);
  }
  .mt-sub{ margin:0 0 8px; font-size:13px; color:#546a96 }
  .mt-sub em{ color:#7a8eb8 }

  .tbl{ width:100%; border-collapse:collapse; border-radius:12px; overflow:hidden; }
  .tbl thead th{
    background:linear-gradient(135deg, var(--blue-pastel, #B1F0F7), #e9f6ff);
    color:#0e204a; font-weight:700; font-size:13px; border-bottom:1px solid #dfeaff; padding:10px 12px;
  }
  .tbl td{ border-bottom:1px solid #eef3ff; padding:10px 12px; }
  .tbl tr:hover{ background:#fbfdff; }

  .note{ font-size:12px; color:#6b7a99; }

  .badge{
    display:inline-block; padding:6px 10px; border-radius:999px;
    border:1px solid #dfeaff; background:#fff; color:#0e2b68; font-size:12px;
  }
  .badge.badge-error{ border-color:#ffd7d7; background:#fff4f4; color:#9a1a1a; }
  .badge.badge-ok{ border-color:#c9f6d1; background:#ecffef; color:#155724; }

  .mt-actions{ margin-top:14px; display:flex; justify-content:flex-end; gap:10px; }

  .btn-danger-soft{
    appearance:none; border:0; cursor:pointer;
    border-radius:12px; padding:12px 16px; font-weight:700; color:#fff;
    background: linear-gradient(135deg, #e45757, #d23535);
    box-shadow:0 10px 20px rgba(210,53,53,.25);
    transition: transform .06s ease, box-shadow .2s ease, filter .2s ease;
  }
  .btn-danger-soft:hover{ box-shadow:0 14px 28px rgba(210,53,53,.28); filter: brightness(1.02); }
  .btn-danger-soft:active{ transform: translateY(1px); }
  .btn-danger-soft:disabled{ opacity:.6; cursor:not-allowed; }

  .mt-grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:10px; }
  @media (max-width: 720px){ .mt-grid{ grid-template-columns:1fr; } }
</style>

<div class="mt-wrap">
  <div class="mt-card">
    <h3 class="mt-title">Data Maintenance — Truncate Tables</h3>
    <p class="mt-sub">
      Aksi ini <strong>menghapus permanen</strong> data pada tabel yang dipilih.
      Pastikan Anda <em>sudah melakukan backup</em>.
    </p>

    <?php if ($msg = $this->session->flashdata('error')): ?>
      <div class="badge badge-error" style="margin-bottom:8px;"><?= html_escape($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = $this->session->flashdata('success')): ?>
      <div class="badge badge-ok" style="margin-bottom:8px;"><?= html_escape($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($summary)): ?>
      <div class="badge" style="margin:6px 0;">Ringkasan eksekusi</div>
      <pre style="white-space:pre-wrap;background:#f8fbff;border:1px solid #e6eefc;border-radius:12px;padding:10px;margin-top:6px;font-size:12px">
<?= html_escape(print_r($summary, true)) ?>
      </pre>
    <?php endif; ?>

    <form action="<?= base_url('maintenance/truncateRun') ?>" method="post" id="truncateForm">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
             value="<?= $this->security->get_csrf_hash(); ?>">

      <table class="tbl" aria-describedby="dryRun">
        <thead>
          <tr>
            <th style="width:42px">Pilih</th>
            <th>Nama Tabel</th>
            <th>Deskripsi</th>
            <th style="width:160px;text-align:right">Jumlah Baris</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allowed as $tbl => $label): $cnt = $counts[$tbl] ?? null; ?>
            <tr>
              <td><input type="checkbox" class="js-tbl" name="tables[]" value="<?= $tbl ?>"></td>
              <td><code style="color:#d23535"><?= $tbl ?></code></td>
              <td><?= html_escape($label) ?></td>
              <td style="text-align:right"><?= $cnt === null ? '<em>n/a</em>' : number_format($cnt) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="note" id="selInfo" style="margin-top:6px;">0 tabel dipilih.</div>

      <div class="mt-grid">
        <div>
          <label class="note" style="display:flex;gap:8px;align-items:flex-start;margin:6px 0;">
            <input type="checkbox" id="ack" name="ack" style="margin-top:3px;">
            <span>Saya memahami aksi ini <strong>menghapus permanen</strong> data.</span>
          </label>

          <div class="note">Ketik <code>TRUNCATE</code> untuk konfirmasi:</div>
          <input type="text" name="confirm_text" id="confirmText" class="form-control" placeholder="TRUNCATE">
        </div>

        <div>
          <div class="note">Masukkan ulang password Administrator:</div>
          <input type="password" name="admin_password" id="adminPassword" class="form-control" placeholder="Password admin">
        </div>
      </div>

      <div class="mt-actions">
        <button type="submit" class="btn-danger-soft" id="btnExec" disabled>
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
  var checks = [].slice.call(document.querySelectorAll('.js-tbl'));

  function updateState(){
    var selected = checks.filter(c => c.checked).length;
    if (info) info.textContent = selected + ' tabel dipilih.';
    var ok = selected > 0 && ack.checked && (ctext.value.trim() === 'TRUNCATE') && (pwd.value.trim() !== '');
    btn.disabled = !ok;
  }

  checks.forEach(c => c.addEventListener('change', updateState));
  [ack, ctext, pwd].forEach(el => el.addEventListener('input', updateState));
  updateState();

  form.addEventListener('submit', function(e){
    var selected = checks.filter(c => c.checked).length;
    if (selected === 0) { e.preventDefault(); alert('Pilih minimal satu tabel.'); return; }
    if (!confirm('Yakin mengosongkan ' + selected + ' tabel? Aksi tidak bisa dibatalkan.')) { e.preventDefault(); return; }
    btn.disabled = true;
    var sp = btn.querySelector('.spinner-border'); if (sp) sp.classList.remove('d-none');
  });
})();
</script>
