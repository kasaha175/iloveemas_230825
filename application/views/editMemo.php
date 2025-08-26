<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped only for this page ===== */
  .sk-wrap{ margin-top: calc(var(--topbar-h, 72px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .sk-container{ max-width: 900px; margin-inline:auto; }

  /* Breadcrumb chip */
  .sk-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 14px; border-radius:12px;
    background: linear-gradient(135deg, var(--blue-pastel, #B1F0F7), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a; box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .sk-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .sk-crumbs a:hover{ color:var(--blue-dark, #001A6E); text-decoration:underline; }
  .sk-crumbs .sep{ color:#7a8eb8; }

  /* Header */
  .sk-head h1{ margin:6px 0; color:#fff; font-weight:800; letter-spacing:.2px; font-size: clamp(20px, 3.2vw, 28px); }
  .sk-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Card */
  .sk-card{ margin-top:14px; background:#fff; border:1px solid #e6eefc; border-radius:16px; box-shadow:0 12px 24px rgba(0,0,0,.08); overflow:hidden; }
  .sk-card-hd{ padding:14px 16px; background:linear-gradient(135deg, #f7fbff, #ffffff); border-bottom:1px solid #eaf0ff; }
  .sk-card-hd h2{ margin:0; color:#0e204a; font-size:16px; font-weight:800; }
  .sk-card-bd{ padding:16px; }

  /* Form */
  .sk-form .form-group{ margin-bottom:14px; }
  .sk-form label{ font-weight:700; color:#0e204a; }
  .sk-help{ font-size:12px; color:#64748b; }

  /* Actions */
  .sk-actions{ display:flex; gap:10px; flex-wrap:wrap; justify-content:space-between; margin-top:12px; }
  .btn-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:700;
    padding:10px 14px; border-radius:12px; text-decoration:none;
    border:1px solid #dfeaff; background:#fff; color:#0e204a; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-soft:hover{ border-color:#cfe0ff; }
  .btn-primary-soft{
    display:inline-flex; align-items:center; gap:10px; font-weight:800;
    padding:12px 18px; border-radius:14px; text-decoration:none; border:0;
    background: linear-gradient(135deg, var(--blue-light, #074799), var(--blue-dark, #001A6E));
    color:#fff; box-shadow:0 12px 22px rgba(0,26,110,.25);
    transition: transform .08s ease, filter .2s ease;
  }
  .btn-primary-soft:hover{ filter:brightness(1.05); transform:translateY(-1px); }
  .btn-primary-soft[disabled]{ opacity:.7; cursor:not-allowed; transform:none; }
</style>

<div class="sk-wrap">
  <div class="sk-container">

    <!-- Breadcrumb -->
    <nav class="sk-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master') ?>">Master</a>
      <span class="sep">/</span>
      <span>Edit Syarat & Ketentuan</span>
    </nav>

    <!-- Head -->
    <header class="sk-head">
      <h1>Edit Syarat & Ketentuan</h1>
      <p>Perbarui konten S&K yang tampil di aplikasi <strong>I Love Emas</strong>.</p>
    </header>

    <!-- Card -->
    <section class="sk-card" aria-label="Form Edit S&K">
      <div class="sk-card-hd">
        <h2>Form S&K</h2>
      </div>
      <div class="sk-card-bd">
        <form class="sk-form" action="<?= base_url('master/update-memo') ?>" method="post" id="formMemo">
          <input type="hidden" name="id" value="<?= $memo->tm_id ?>">

          <div class="form-group">
            <label for="tm_value">Isi Syarat & Ketentuan</label>
            <textarea id="tm_value" class="form-control summernote" name="dt[tm_value]"><?= $memo->tm_value ?></textarea>
          </div>

          <div class="form-group">
            <label for="tm_priority">Prioritas</label>
            <input type="number" id="tm_priority" class="form-control" name="dt[tm_priority]" required min="0" step="1" value="<?= $memo->tm_priority ?>">
            <div class="sk-help">Angka lebih kecil = prioritas lebih tinggi (muncul lebih dahulu).</div>
          </div>

          <div class="sk-actions">
            <button type="submit" class="btn-primary-soft" id="btnSave">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="<?= base_url('master/memo') ?>" class="btn-soft">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </form>
      </div>
    </section>

  </div>
</div>

<script>
(function waitForDeps(){
  // Pastikan jQuery & Summernote dari UserTemplate sudah siap
  if (!window.jQuery || !jQuery.fn) return setTimeout(waitForDeps, 50);
  if (!jQuery.fn.summernote) return setTimeout(waitForDeps, 50);

  var $ = jQuery;

  // Summernote
  $('.summernote').summernote({
    height: 220,
    toolbar: [
      ['style', ['bold','italic','underline','clear']],
      ['para', ['ul','ol','paragraph']],
      ['insert', ['link','picture','video']],
      ['view', ['fullscreen','codeview','help']]
    ]
  });

  // Keyboard plugin (opsional)
  if ($.keyboard) {
    try { $('#tm_priority').keyboard({ layout: 'qwerty' }); } catch(e){}
  }

  // Prevent double submit
  $('#formMemo').on('submit', function(){
    $('#btnSave').prop('disabled', true).text('Saving...');
  });
})();
</script>
