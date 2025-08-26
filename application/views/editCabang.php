<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
  // fallback kalau field urutan masih bernama "urutan" di DB lama
  $urutan_val = isset($cabang->urutan_cabang) ? $cabang->urutan_cabang : (isset($cabang->urutan) ? $cabang->urutan : '');
?>
<style>
  /* ===== Scoped for this page ===== */
  .mc-wrap{ margin-top: calc(var(--topbar-h, 72px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .mc-container{ max-width: 860px; margin-inline:auto; }

  /* Breadcrumb chip */
  .mc-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 14px; border-radius:12px;
    background: linear-gradient(135deg, var(--blue-pastel, #B1F0F7), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a; box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .mc-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .mc-crumbs a:hover{ color:var(--blue-dark, #001A6E); text-decoration:underline; }
  .mc-crumbs .sep{ color:#7a8eb8; }

  /* Header */
  .mc-head h1{ margin:6px 0; color:#fff; font-weight:800; letter-spacing:.2px; font-size: clamp(20px, 3.2vw, 28px); }
  .mc-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Card + form */
  .mc-card{ margin-top:12px; background:#fff; border:1px solid #e6eefc; border-radius:16px; box-shadow:0 12px 24px rgba(0,0,0,.08); overflow:hidden; }
  .mc-card-hd{ padding:14px 16px; display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg, #f7fbff, #ffffff); border-bottom:1px solid #eaf0ff; }
  .mc-card-hd h2{ margin:0; font-size:16px; color:#0e204a; font-weight:800; }
  .mc-body{ padding:16px; }
  .mc-actions{ margin-top:16px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between; }

  .btn-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:700;
    padding:10px 14px; border-radius:12px; text-decoration:none;
    border:1px solid #dfeaff; background:#fff; color:#0e204a; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-soft:hover{ border-color:#cfe0ff; }
  .btn-primary-soft{
    display:inline-flex; align-items:center; gap:10px; font-weight:800;
    padding:12px 18px; border-radius:14px; text-decoration:none;
    background: linear-gradient(135deg, var(--blue-light, #074799), var(--blue-dark, #001A6E));
    color:#fff; box-shadow:0 12px 22px rgba(0,26,110,.25);
  }
  .btn-primary-soft:hover{ filter:brightness(1.05); transform:translateY(-1px); }

  .form-label{ font-weight:700; color:#0e204a; }
  .form-control{
    border:1px solid #dfeaff; border-radius:12px; padding:10px 12px;
    box-shadow:0 6px 12px rgba(0,0,0,.04);
  }
  textarea.form-control{ min-height:110px; resize:vertical; }
</style>

<div class="mc-wrap">
  <div class="mc-container">

    <!-- Breadcrumb -->
    <nav class="mc-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master') ?>">Master</a>
      <span class="sep">/</span>
      <span>Edit Cabang</span>
    </nav>

    <!-- Head -->
    <header class="mc-head">
      <h1>Edit Cabang</h1>
      <p>Perbarui data cabang/gerai pada aplikasi <strong>I Love Emas</strong>.</p>
    </header>

    <!-- Flash (opsional) -->
    <?php if($this->session->userdata('status')==='success'): ?>
      <div style="margin-top:10px;">
        <span class="btn-soft" style="background:#ecffef;border-color:#c9f6d1;color:#155724;"> <?= html_escape($this->session->userdata('message')) ?> </span>
      </div>
      <?php $this->session->set_userdata(['status'=>'','message'=>'']); ?>
    <?php endif; ?>

    <!-- Card + Form -->
    <section class="mc-card" aria-label="Form Edit Cabang">
      <div class="mc-card-hd">
        <h2>Form Cabang</h2>
      </div>
      <div class="mc-body">
        <form action="<?= base_url('master/save-update-cabang') ?>" method="post" id="formCabang" autocomplete="off">
          <input type="hidden" name="id" value="<?= (int)$cabang->id ?>">

          <div class="form-group mb-3">
            <label class="form-label">Nama Cabang</label>
            <input type="text" class="form-control" name="dt[nama_cabang]" required
                   value="<?= html_escape($cabang->nama_cabang) ?>">
          </div>

          <div class="form-group mb-3">
            <label class="form-label">Urutan</label>
            <input type="number" class="form-control" name="dt[urutan_cabang]" required min="0"
                   value="<?= html_escape($urutan_val) ?>">
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Alamat</label>
            <textarea class="form-control" name="dt[alamat_cabang]" required><?= html_escape($cabang->alamat_cabang) ?></textarea>
          </div>

          <div class="mc-actions">
            <button type="submit" class="btn-primary-soft">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="<?= base_url('master/cabang') ?>" class="btn-soft">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </form>
      </div>
    </section>

  </div>
</div>

<script>
  // Pastikan jQuery sudah tersedia dari template
  (function wait(){
    if(!window.jQuery){ return setTimeout(wait,50); }
    jQuery(function($){
      // fokus ke nama cabang
      $('[name="dt[nama_cabang]"]').trigger('focus');

      // cegah submit ganda
      $('#formCabang').on('submit', function(){
        $(this).find('button[type="submit"]').prop('disabled', true);
      });
    });
  })();
</script>
