<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped only for this page ===== */
  .mc-wrap{ margin-top: calc(var(--topbar-h, 72px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .mc-container{ max-width: 960px; margin-inline:auto; }

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

  /* Head */
  .mc-head h1{ margin:6px 0; color:#fff; font-weight:800; letter-spacing:.2px; font-size: clamp(20px, 3.2vw, 28px); }
  .mc-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Actions */
  .mc-actions{ margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between; }
  .btn-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:700;
    padding:10px 14px; border-radius:12px; text-decoration:none;
    border:1px solid #dfeaff; background:#fff; color:#0e204a; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-soft:hover{ border-color:#cfe0ff; }
  .btn-primary-soft{
    color:#fff; border:0;
    background: linear-gradient(135deg, var(--blue-light, #074799), var(--blue-dark, #001A6E));
    padding:12px 18px; border-radius:14px; font-weight:800;
    box-shadow:0 12px 22px rgba(0,26,110,.25);
    transition: transform .08s ease, filter .2s ease;
  }
  .btn-primary-soft:hover{ filter:brightness(1.05); transform:translateY(-1px); }

  /* Card */
  .mc-card{ margin-top:12px; background:#fff; border:1px solid #e6eefc; border-radius:16px; box-shadow:0 12px 24px rgba(0,0,0,.08); overflow:hidden; }
  .mc-card-hd{ padding:14px 16px; display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg, #f7fbff, #ffffff); border-bottom:1px solid #eaf0ff; }
  .mc-card-hd h2{ margin:0; font-size:16px; color:#0e204a; font-weight:800; }
  .mc-card-bd{ padding:16px; }

  /* Form */
  .form-grid{ display:grid; grid-template-columns: 1fr 180px; gap:14px; }
  @media (max-width: 660px){ .form-grid{ grid-template-columns: 1fr; } }

  .form-group{ margin-bottom:12px; }
  .form-label{ font-weight:700; color:#0e204a; font-size:13px; margin-bottom:6px; }
  .form-control{
    border:1px solid #dfeaff; border-radius:12px; padding:10px 12px;
    box-shadow:0 6px 12px rgba(0,0,0,.04); outline:none;
  }
  .form-control:focus{ border-color:#cfe0ff; box-shadow:0 8px 18px rgba(0,0,0,.06); }
  textarea.form-control{ min-height:110px; resize:vertical; }

  .form-actions{ display:flex; gap:10px; flex-wrap:wrap; margin-top:6px; }
  .btn-block-sm{ min-width:160px; text-align:center; }
</style>

<div class="mc-wrap">
  <div class="mc-container">

    <!-- Breadcrumb -->
    <nav class="mc-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master') ?>">Master</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master/cabang') ?>">Cabang</a>
      <span class="sep">/</span>
      <span>Tambah Cabang</span>
    </nav>

    <!-- Head -->
    <header class="mc-head">
      <h1>Tambah Cabang</h1>
      <p>Masukkan informasi cabang/gerai baru.</p>
    </header>

    <!-- Actions -->
    <div class="mc-actions">
      <span></span>
      <a href="<?= base_url('master/cabang') ?>" class="btn-soft">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Cabang
      </a>
    </div>

    <!-- Card + Form -->
    <section class="mc-card" aria-label="Form tambah cabang">
      <div class="mc-card-hd">
        <h2>Form Cabang</h2>
      </div>
      <div class="mc-card-bd">
        <form action="<?= base_url('master/save-cabang') ?>" method="post" id="formCabang" novalidate>
          <!-- CSRF -->
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                 value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="form-grid">
            <div>
              <div class="form-group">
                <label class="form-label" for="namaCabang">Nama Cabang</label>
                <input type="text" id="namaCabang" class="form-control" name="dt[nama_cabang]" required>
              </div>

              <div class="form-group">
                <label class="form-label" for="alamatCabang">Alamat</label>
                <textarea id="alamatCabang" class="form-control" name="dt[alamat_cabang]" required></textarea>
              </div>
            </div>

            <div>
              <div class="form-group">
                <label class="form-label" for="urutanCabang">Urutan</label>
                <input type="number" id="urutanCabang" class="form-control" name="dt[urutan_cabang]" min="0" step="1" required>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary-soft btn-block-sm" id="btnSubmit">
              <i class="fas fa-save"></i>&nbsp; Simpan
            </button>
            <a href="<?= base_url('master/cabang') ?>" class="btn-soft btn-block-sm">
              <i class="fas fa-times"></i>&nbsp; Batal
            </a>
          </div>
        </form>
      </div>
    </section>

  </div>
</div>

<script>
  (function(){
    // optional: keyboard plugin jika tersedia
    if (window.jQuery && jQuery.fn && jQuery.fn.keyboard) {
      try{
        jQuery('#namaCabang, #alamatCabang, #urutanCabang').keyboard({ layout:'qwerty' });
      }catch(e){}
    }

    // UX: disable tombol saat submit
    if (window.jQuery) {
      jQuery('#formCabang').on('submit', function(){
        jQuery('#btnSubmit').prop('disabled', true).text('Menyimpan…');
      });
    }
  })();
</script>
