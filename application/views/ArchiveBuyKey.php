<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
  $key = $this->input->get('key');
  $labels = [
    'rti-au' => 'RTI AU',
    'rti-pt' => 'RTI PT',
    'rti-ag' => 'RTI AG',
    'rti-lm' => 'RTI LM',
    'rti-ru' => 'RTI RU',
    'rti-ta' => 'RTI TA',
  ];
  $label = $labels[$key] ?? ucwords(str_replace('-',' ',$key));
?>
<style>
  /* ===== Scoped agar tidak bentrok template lain ===== */
  .archive-buy-edit-scope .wrap{ margin-top: calc(var(--topbar-h,72px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .archive-buy-edit-scope .container-max{ max-width: 760px; margin-inline:auto; }

  /* Breadcrumb pil */
  .archive-buy-edit-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel, #e8f5ff), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .archive-buy-edit-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .archive-buy-edit-scope .crumbs a:hover{ color:var(--blue-light, #3a86ff); text-decoration:underline; }
  .archive-buy-edit-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .archive-buy-edit-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .archive-buy-edit-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Kartu form */
  .archive-buy-edit-scope .card{
    border-radius:16px; border:1px solid #e6eefc; overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.08); background:#fff;
  }
  .archive-buy-edit-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel, #e8f5ff), #e9faff);
    border-bottom:1px solid #dfeaff;
  }
  .archive-buy-edit-scope .card-header h6{ margin:0; color:#0e204a; font-weight:800; }

  /* Input besar & rata tengah */
  .archive-buy-edit-scope .input-box{
    height:60px; font-size:22px; text-align:center; font-weight:800;
    letter-spacing:.5px; border-radius:12px; border:1px solid #e3e6ef;
  }
  .archive-buy-edit-scope .input-box:focus{
    border-color:#b9d6ff; box-shadow:0 0 0 3px rgba(51,136,255,.15); outline:0;
  }

  /* Tombol aksi bawah */
  .archive-buy-edit-scope .actions{ display:flex; gap:12px; flex-wrap:wrap; justify-content:center; }
  @media (max-width:576px){
    .archive-buy-edit-scope .actions .btn{ width:100%; }
  }

  /* Pastikan virtual keyboard tampil di atas & bisa diklik (tanpa ubah tampilannya) */
  .ui-keyboard{ z-index: 3000 !important; pointer-events:auto; }
</style>

<div class="archive-buy-edit-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive') ?>">Archive</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive/buy') ?>">Buy</a>
        <span class="sep">/</span>
        <span><?= html_escape($label) ?></span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Archive Buy — <?= html_escape($label) ?></h3>
        <p>Masukkan nilai untuk jenis RTI yang dipilih.</p>
      </header>

      <!-- Form Card -->
      <section class="card mt-3">
        <div class="card-header py-3">
          <h6 class="m-0">Input Nilai: <?= html_escape($label) ?></h6>
        </div>
        <div class="card-body">
          <form id="formRti" action="<?= base_url('archive/buy/save/') ?>" method="get">
            <input type="hidden" name="key" value="<?= html_escape($key) ?>">
            <?php if (isset($this->security)) : ?>
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                     value="<?= $this->security->get_csrf_hash(); ?>">
            <?php endif; ?>

            <div class="form-group text-center">
              <label for="value" class="font-weight-bold mb-2"><?= html_escape($label) ?></label>
              <input
                id="value"
                name="value"
                type="number"
                step="any"
                inputmode="decimal"
                required
                class="form-control input-box"
                value="<?= isset($value) ? html_escape($value) : '' ?>"
                placeholder="0">
              <small class="form-text text-muted">Gunakan titik (.) untuk desimal.</small>
            </div>

            <div class="actions mt-3 mb-1">
              <a href="<?= base_url('archive/buy') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
              </a>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Simpan
              </button>
              <?php if ($key !== 'rti-ta'): ?>
              <a href="<?= base_url('archive/buy/?key=' . urlencode($key) . '&type=change') ?>" class="btn btn-warning text-white">
                <i class="fas fa-exchange-alt mr-1"></i> Ganti Potongan
              </a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </section>

    </div>
  </div>
</div>

<script>
  (function(){
    function onReady(fn){ document.readyState!=='loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }
    onReady(function(){
      // Inisialisasi NumPad (jQuery Keyboard) bila tersedia
      if (window.jQuery && jQuery.fn && jQuery.fn.keyboard) {
        jQuery('.input-box').keyboard({
          layout: 'num',
          restrictInput: true,
          preventPaste: true,
          autoAccept: true
        });
      }
    });
  })();
</script>
