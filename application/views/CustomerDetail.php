<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php foreach ($detail as $d) { break; } // ambil 1 baris ?>
<style>
  /* ===== scoped to this page only ===== */
  .cd-wrap{ margin-top: calc(var(--topbar-h,72px) + 12px); padding: clamp(12px,2vw,20px); }
  .cd-container{ max-width: 920px; margin-inline:auto; }

  /* breadcrumb pastel (seragam) */
  .cd-crumbs{
    display:flex; gap:10px; align-items:center; flex-wrap:wrap;
    padding:10px 14px; border-radius:12px;
    background: linear-gradient(135deg, var(--blue-pastel,#B1F0F7), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a; box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .cd-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .cd-crumbs a:hover{ color:var(--blue-dark,#001A6E); text-decoration:underline; }
  .cd-crumbs .sep{ color:#7a8eb8; }

  /* header */
  .cd-head h1{ margin:6px 0; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .cd-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* card */
  .cd-card{
    margin-top:12px; background:#fff; border:1px solid #e6eefc; border-radius:16px;
    box-shadow:0 12px 24px rgba(0,0,0,.08); overflow:hidden;
  }
  .cd-card-hd{
    padding:14px 16px; display:flex; align-items:center; justify-content:space-between;
    background:linear-gradient(135deg,#f7fbff,#ffffff); border-bottom:1px solid #eaf0ff;
  }
  .cd-card-hd h2{ margin:0; font-size:16px; color:#0e204a; font-weight:800; }
  .cd-card-bd{ padding:16px; }

  /* form */
  .cd-form .row{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .cd-form .row-1{ grid-template-columns:1fr; }
  @media (max-width:720px){ .cd-form .row{ grid-template-columns:1fr; } }

  .cd-field label{ font-weight:700; color:#0e204a; font-size:13px; margin-bottom:6px; }
  .cd-input, .cd-textarea{
    width:100%; border:1px solid #dfeaff; border-radius:12px; padding:10px 12px;
    background:#fff; color:#0e204a; box-shadow:0 6px 12px rgba(0,0,0,.04);
  }
  .cd-textarea{ min-height:92px; resize:vertical; }
  .cd-input:focus, .cd-textarea:focus{ outline:none; border-color:#badcff; box-shadow:0 0 0 3px rgba(186,220,255,.5); }
  .cd-note{ font-size:12px; color:#667; margin-top:4px; }

  /* actions */
  .cd-actions{ display:flex; gap:10px; flex-wrap:wrap; margin-top:8px; }
  .btn-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:700;
    padding:10px 14px; border-radius:12px; text-decoration:none;
    border:1px solid #dfeaff; background:#fff; color:#0e204a; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-soft:hover{ border-color:#cfe0ff; }
  .btn-primary-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:800;
    padding:12px 18px; border-radius:12px; border:0; cursor:pointer;
    background:linear-gradient(135deg, var(--blue-light,#074799), var(--blue-dark,#001A6E));
    color:#fff; box-shadow:0 12px 22px rgba(0,26,110,.25);
  }
  .btn-primary-soft:hover{ filter:brightness(1.05); }
</style>

<div class="cd-wrap">
  <div class="cd-container">

    <!-- breadcrumbs -->
    <nav class="cd-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master') ?>">Master</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master/customer') ?>">Master Customer</a>
      <span class="sep">/</span>
      <span>Detail Customer</span>
    </nav>

    <!-- head -->
    <header class="cd-head">
      <h1>Detail Customer</h1>
      <p>Perbarui data pelanggan dengan rapi dan aman.</p>
    </header>

    <!-- flash -->
    <?php if($this->session->userdata('status')==='success'): ?>
      <div style="margin-top:10px;">
        <span style="display:inline-block;padding:6px 10px;border-radius:999px;border:1px solid #c9f6d1;background:#ecffef;color:#155724;font-size:12px;">
          <?= html_escape($this->session->userdata('message')) ?>
        </span>
      </div>
      <?php $this->session->set_userdata(['status'=>'','message'=>'']); ?>
    <?php endif; ?>

    <!-- card -->
    <section class="cd-card" aria-label="Form detail customer">
      <div class="cd-card-hd">
        <h2>Detail Customer</h2>
      </div>
      <div class="cd-card-bd">
        <form action="<?= base_url('master/edit-customer-process') ?>" method="post" class="cd-form" id="customerForm">
          <input type="hidden" name="idCustomer" value="<?= (int)$d->c_id ?>">
          <!-- CSRF (jaga-jaga bila CI CSRF aktif) -->
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="row row-1">
            <div class="cd-field">
              <label for="u_name">Name</label>
              <input type="text" id="u_name" name="name" required class="cd-input" value="<?= html_escape($d->c_name) ?>">
            </div>
          </div>

          <div class="row">
            <div class="cd-field">
              <label for="c_id_number">ID Number (KTP)</label>
              <input type="text" id="c_id_number" name="idNumber" required class="cd-input" value="<?= html_escape($d->c_id_number) ?>">
            </div>
            <div class="cd-field">
              <label for="u_no_order">No Order</label>
              <input type="text" id="u_no_order" name="noOrder" required class="cd-input" value="<?= html_escape($d->c_no_order) ?>">
            </div>
          </div>

          <div class="row">
            <div class="cd-field">
              <label for="u_address">Address</label>
              <textarea id="u_address" name="address" required class="cd-textarea"><?= html_escape($d->c_address) ?></textarea>
            </div>
            <div class="cd-field">
              <label for="u_resident_address">Resident Address</label>
              <textarea id="u_resident_address" name="resident_address" required class="cd-textarea"><?= html_escape($d->c_resident_address) ?></textarea>
            </div>
          </div>

          <div class="row row-1">
            <div class="cd-field">
              <label for="u_phone">Phone</label>
              <input type="text" id="u_phone" name="phone" required class="cd-input" value="<?= html_escape($d->c_phone) ?>">
              <div class="cd-note">Gunakan angka saja, contoh: 08123456789</div>
            </div>
          </div>

          <div class="row row-1">
            <div class="cd-field">
              <label for="u_email">Email</label>
              <input type="email" id="u_email" name="email" required class="cd-input" 
                    value="<?= html_escape($d->c_email ?? '') ?>">
              <div class="cd-note">Masukkan alamat email yang valid</div>
            </div>
          </div>

          <div class="cd-actions">
            <button type="submit" class="btn-primary-soft">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="<?= base_url('master/customer') ?>" class="btn-soft">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </form>
      </div>
    </section>

  </div>
</div>

<script>
  // Select2 (kalau ada)
  if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
    jQuery('.select2').select2();
  }

  // jqKeyboard (hanya jika plugin tersedia)
  (function($){
    if (!window.jQuery || !$.keyboard) return;

    $('#u_name').keyboard({ layout:'qwerty' });
    $('#u_address').keyboard({ layout:'qwerty' });
    $('#u_resident_address').keyboard({ layout:'qwerty' });
    $('#u_phone').keyboard({ layout:'qwerty' });
    $('#u_no_order').keyboard({ layout:'qwerty' });
    $('#c_id_number').keyboard({ layout:'qwerty' });

    // contoh keyaction undo/redo (optional)
    $.keyboard.keyaction.undo = function (base) { base.execCommand('undo'); return false; };
    $.keyboard.keyaction.redo = function (base) { base.execCommand('redo'); return false; };
  })(jQuery);
</script>
