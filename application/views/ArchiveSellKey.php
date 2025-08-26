<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
  $key = $this->input->get('key');
  $labels = [
    'lm'           => 'LM',
    'material-au'  => 'MATERIAL AU',
    'material-ag'  => 'MATERIAL AG',
    'material-ubs' => 'UBS',
  ];
  $label = $labels[$key] ?? ucwords(str_replace('-',' ',$key));
?>
<style>
  /* ===== Scoped agar tak bentrok template lain ===== */
  .archive-sell-edit-scope .wrap{ margin-top: calc(var(--topbar-h, 98px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .archive-sell-edit-scope .container-max{ max-width: 920px; margin-inline:auto; }

  /* Breadcrumb pil */
  .archive-sell-edit-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .archive-sell-edit-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .archive-sell-edit-scope .crumbs a:hover{ color:var(--blue-light, #4a7dff); text-decoration:underline; }
  .archive-sell-edit-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .archive-sell-edit-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .archive-sell-edit-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Card */
  .archive-sell-edit-scope .card{
    border-radius:16px; border:1px solid #e6eefc; overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.08); background:#fff;
  }
  .archive-sell-edit-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #e9faff);
    border-bottom:1px solid #dfeaff;
  }
  .archive-sell-edit-scope .card-header h6{ margin:0; color:#0e204a; font-weight:800; }

  /* Tabel rapi & responsif */
  .archive-sell-edit-scope table{
    width:100%; border-collapse:separate; border-spacing:0;
    border:1px solid #e6eefc; border-radius:12px; overflow:visible;
    background:#fff;
  }
  .archive-sell-edit-scope thead th{
    background:#f5fbff; color:#0e204a; font-weight:800; text-align:center;
  }
  .archive-sell-edit-scope th, .archive-sell-edit-scope td{
    border:1px solid #e2e8f3; padding:10px 12px; vertical-align:middle;
  }
  .archive-sell-edit-scope td:first-child{ width:50%; }

  /* Input angka (sesuai class lama .input-box) */
  .archive-sell-edit-scope .input-box{
    width:100%; height:42px; margin:0;
    padding:8px 10px; background:#fff;
    border:1px solid #e3e6ef; border-radius:8px;
    font-weight:700; text-align:center; outline:0; box-shadow:none;
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  .archive-sell-edit-scope .input-box:focus{
    border-color:#b9d6ff; box-shadow:0 0 0 3px rgba(51,136,255,.15);
  }

  /* Input single (material-au/ag/ubs) */
  .archive-sell-edit-scope .input-big{
    height:60px; font-size:22px; text-align:center; font-weight:800; letter-spacing:.5px;
  }

  /* Hilangkan spinner number (visual) */
  .archive-sell-edit-scope input[type=number]::-webkit-outer-spin-button,
  .archive-sell-edit-scope input[type=number]::-webkit-inner-spin-button{ -webkit-appearance:none; margin:0; }
  .archive-sell-edit-scope input[type=number]{ -moz-appearance:textfield; }

  /* Aksi bawah */
  .archive-sell-edit-scope .actions{ display:flex; gap:12px; flex-wrap:wrap; justify-content:center; }

  /* Pastikan keyboard virtual tampil di atas (tanpa ubah tampilannya) */
  .ui-keyboard{ z-index:1300 !important; }

  /* ===== Overlay Loading saat submit ===== */
  .saving-overlay{
    position: fixed; inset: 0;
    background: rgba(15,23,42,.35);
    backdrop-filter: saturate(110%) blur(1px);
    display:flex; align-items:center; justify-content:center;
    z-index: 5000;
    opacity:0; visibility:hidden; pointer-events:none;
    transition: opacity .2s ease;
  }
  .saving-overlay.show{ opacity:1; visibility:visible; pointer-events:auto; }
  .saving-box{
    display:flex; flex-direction:column; align-items:center; gap:14px;
    background: rgba(255,255,255,.95);
    border:1px solid #e6eefc; border-radius:16px;
    padding:18px 22px; box-shadow:0 12px 30px rgba(0,0,0,.18);
    min-width:220px;
  }
  .saving-text{ font-weight:800; color:#0e204a; letter-spacing:.3px; }
  .saving-box .fa-spin{ font-size:30px; color:#2563eb; }
  .saving-fallback{
    width:34px; height:34px; border-radius:50%;
    border:3px solid #dbeafe; border-top-color:#2563eb;
    animation: spin 1s linear infinite; display:none;
  }
  @keyframes spin{ to{ transform: rotate(360deg); } }
  @media (prefers-reduced-motion: reduce){
    .saving-box .fa-spin{ animation: none !important; }
    .saving-fallback{ animation: none !important; }
  }
</style>

<div class="archive-sell-edit-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive') ?>">Archive</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive/sell') ?>">Sell</a>
        <span class="sep">/</span>
        <span><?= html_escape($label) ?></span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Archive / Sell / <?= html_escape($label) ?></h3>
        <p>Perbarui nilai sesuai kategori penjualan.</p>
      </header>

      <!-- Card Form -->
      <section class="card mt-3">
        <div class="card-header py-3">
          <h6 class="m-0">Input Nilai: <?= html_escape($label) ?></h6>
        </div>
        <div class="card-body">
          <form action="<?= base_url('archive/sell/save/') ?>" id="myForm" method="get">
            <input type="hidden" name="key" value="<?= html_escape($key) ?>">
            <?php if (isset($this->security)) : ?>
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                     value="<?= $this->security->get_csrf_hash(); ?>">
            <?php endif; ?>

            <div class="form-group text-center mb-3">
              <label class="font-weight-bold mb-2" style="color:#0e204a;"><?= html_escape($label) ?></label>

              <?php if ($key === 'lm'): ?>
                <div class="table-responsive">
                  <table>
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Value</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td>0.5 Gr</td>   <td><input class="input-box" type="number" step="any" name="f_nol5"      required value="<?= html_escape($f_nol5) ?>"></td></tr>
                      <tr><td>1 Gr</td>     <td><input class="input-box" type="number" step="any" name="f_1"         required value="<?= html_escape($f_1) ?>"></td></tr>
                      <tr><td>2 Gr</td>     <td><input class="input-box" type="number" step="any" name="f_2"         required value="<?= html_escape($f_2) ?>"></td></tr>
                      <tr><td>2.5 Gr</td>   <td><input class="input-box" type="number" step="any" name="f_2_coma_5"  required value="<?= html_escape($f_2_coma_5) ?>"></td></tr>
                      <tr><td>3 Gr</td>     <td><input class="input-box" type="number" step="any" name="f_3"         required value="<?= html_escape($f_3) ?>"></td></tr>
                      <tr><td>5 Gr</td>     <td><input class="input-box" type="number" step="any" name="f_5"         required value="<?= html_escape($f_5) ?>"></td></tr>
                      <tr><td>10 Gr</td>    <td><input class="input-box" type="number" step="any" name="f_10"        required value="<?= html_escape($f_10) ?>"></td></tr>
                      <tr><td>25 Gr</td>    <td><input class="input-box" type="number" step="any" name="f_25"        required value="<?= html_escape($f_25) ?>"></td></tr>
                      <tr><td>50 Gr</td>    <td><input class="input-box" type="number" step="any" name="f_50"        required value="<?= html_escape($f_50) ?>"></td></tr>
                      <tr><td>100 Gr</td>   <td><input class="input-box" type="number" step="any" name="f_100"       required value="<?= html_escape($f_100) ?>"></td></tr>
                      <tr><td>250 Gr</td>   <td><input class="input-box" type="number" step="any" name="f_250"       required value="<?= html_escape($f_250) ?>"></td></tr>
                      <tr><td>500 Gr</td>   <td><input class="input-box" type="number" step="any" name="f_500"       required value="<?= html_escape($f_500) ?>"></td></tr>
                      <tr><td>1000 Gr</td>  <td><input class="input-box" type="number" step="any" name="f_1000"      required value="<?= html_escape($f_1000) ?>"></td></tr>
                    </tbody>
                  </table>
                </div>

              <?php else: ?>
                <!-- single value -->
                <input
                  type="number"
                  step="any"
                  name="value"
                  required
                  class="form-control input-box input-big"
                  value="<?= isset($value) ? html_escape($value) : '' ?>"
                  placeholder="0">
                <small class="form-text text-muted mt-1">Gunakan titik (.) untuk desimal.</small>
              <?php endif; ?>
            </div>

            <div class="actions mt-3 mb-1">
              <a href="<?= base_url('archive/sell') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
              </a>
              <a href="#" onclick="document.getElementById('myForm').submit();" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Simpan
              </a>
              <a href="<?= base_url('archive/sell/?key=' . urlencode($key) . '&type=change') ?>" class="btn btn-warning text-white">
                <i class="fas fa-exchange-alt mr-1"></i> Ganti Potongan
              </a>
            </div>

          </form>
        </div>
      </section>

    </div>
  </div>
</div>

<!-- Overlay Loading -->
<div class="saving-overlay" id="savingOverlay" aria-hidden="true" aria-label="Sedang menyimpan" role="status">
  <div class="saving-box" aria-live="polite">
    <!-- Pakai Font Awesome jika tersedia -->
    <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>
    <!-- Fallback spinner CSS bila FA tidak tersedia -->
    <div class="saving-fallback" aria-hidden="true"></div>
    <div class="saving-text">Menyimpan…</div>
  </div>
</div>

<script>
  (function(){
    function onReady(fn){ document.readyState!=='loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }
    onReady(function(){
      // NumPad dari jQuery Keyboard (kalau plugin tersedia)
      if (window.jQuery && jQuery.fn && jQuery.fn.keyboard) {
        jQuery('.input-box').keyboard({
          layout: 'num',
          restrictInput: true,
          preventPaste: true,
          autoAccept: true
        });
      }

      // Overlay loading saat submit
      var form    = document.getElementById('myForm');
      var overlay = document.getElementById('savingOverlay');
      var faIcon  = overlay ? overlay.querySelector('.fa-circle-notch') : null;
      var cssSpin = overlay ? overlay.querySelector('.saving-fallback') : null;

      // Jika Font Awesome tidak tersedia, munculkan spinner CSS
      try {
        var faLoaded = window.getComputedStyle(faIcon, '::before').getPropertyValue('content');
        if (!faLoaded || faLoaded === 'none' || faLoaded === 'normal' || faLoaded === '""') {
          cssSpin && (cssSpin.style.display = 'block');
        }
      } catch(e){ cssSpin && (cssSpin.style.display = 'block'); }

      if (form && overlay) {
        form.addEventListener('submit', function(){
          if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;
          overlay.classList.add('show');
        });
      }
    });
  })();
</script>
