<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped only for this page ===== */
  .nc-wrap{ margin-top: calc(var(--topbar-h,72px) + 12px); padding: clamp(12px,2vw,20px); }
  .nc-container{ max-width: 920px; margin-inline:auto; }

  /* Breadcrumb chip */
  .nc-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 14px; border-radius:12px;
    background: linear-gradient(135deg, var(--blue-pastel,#B1F0F7), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a; box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .nc-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .nc-crumbs a:hover{ color:var(--blue-dark,#001A6E); text-decoration:underline; }
  .nc-crumbs .sep{ color:#7a8eb8; }

  /* Header */
  .nc-head h1{ margin:6px 0; color:#fff; font-weight:800; letter-spacing:.2px; font-size: clamp(20px,3.2vw,28px); }
  .nc-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Card */
  .nc-card{ margin-top:12px; background:#fff; border:1px solid #e6eefc; border-radius:18px; overflow:hidden;
           box-shadow:0 16px 28px rgba(0,0,0,.10); }
  .nc-card-hd{
    display:flex; align-items:center; gap:10px; justify-content:space-between;
    padding:16px; border-bottom:1px solid #eaf0ff;
    background:linear-gradient(135deg,#f7fbff,#ffffff);
  }
  .nc-card-hd h2{ margin:0; font-size:16px; font-weight:800; color:#0e204a; }
  .nc-card-bd{ padding:18px; }

  /* Form fields */
  .nc-form .form-group{ margin-bottom:14px; }
  .nc-form label{ font-weight:700; color:#0e204a; font-size:13px; margin-bottom:6px; }
  .nc-form .form-control{
    border:1px solid #dfeaff; border-radius:12px; padding:10px 12px;
    box-shadow:0 8px 16px rgba(0,0,0,.04);
  }
  .nc-form .form-control:focus{
    border-color:#bad3ff; box-shadow:0 0 0 4px rgba(7,71,153,.10);
  }
  textarea.form-control{ min-height:90px; resize:vertical; }

  /* Buttons */
  .btn-grad{
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    width:100%; padding:12px 16px; border-radius:12px; border:0; font-weight:800; color:#fff;
    background:linear-gradient(135deg,var(--blue-light,#074799),var(--blue-dark,#001A6E));
    box-shadow:0 12px 22px rgba(0,26,110,.25); transition:transform .08s ease, filter .2s ease;
  }
  .btn-grad:hover{ filter:brightness(1.05); transform:translateY(-1px); }
  .btn-soft{
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    width:100%; padding:12px 16px; border-radius:12px; font-weight:700;
    color:#0e204a; background:#fff; border:1px solid #dfeaff; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-row{ display:grid; grid-template-columns: 1fr; gap:10px; }
  @media (min-width:560px){ .btn-row{ grid-template-columns: 2fr 1fr; } }

  /* Success pill (optional) */
  .nc-badge{
    display:inline-block; padding:8px 12px; border-radius:999px;
    border:1px solid #c9f6d1; background:#ecffef; color:#155724; font-weight:700; font-size:12px;
  }
</style>

<div class="nc-wrap">
  <div class="nc-container">

    <!-- Breadcrumb -->
    <nav class="nc-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('transaction') ?>">Transaction</a>
      <span class="sep">/</span>
      <span>New Customer</span>
    </nav>

    <!-- Head -->
    <header class="nc-head">
      <h1>New Customer</h1>
      <p>Input data pelanggan baru untuk transaksi di <strong>I Love Emas</strong>.</p>
    </header>

    <?php if($this->session->userdata('status')==='success'): ?>
      <div style="margin:10px 0 0;">
        <span class="nc-badge"><?= html_escape($this->session->userdata('message')) ?></span>
      </div>
      <?php $this->session->set_userdata(['status'=>'','message'=>'']); ?>
    <?php endif; ?>

    <!-- Card -->
    <section class="nc-card">
      <div class="nc-card-hd">
        <h2><i class="fas fa-user-plus"></i> &nbsp;New Customer</h2>
      </div>
      <div class="nc-card-bd">
        <form action="<?= base_url('transaction/new-customer-process/') ?>" method="post" id="myForm" class="nc-form" novalidate>
          <!-- CSRF (jaga-jaga jika tidak auto) -->
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <input type="hidden" name="key" value="<?= $this->input->get('key'); ?>">

          <div class="form-group">
            <label for="u_name">Name</label>
            <input id="u_name" type="text" name="name" class="form-control" required autocomplete="name" placeholder="Nama lengkap">
          </div>

          <div class="form-group">
            <label for="u_id_number">ID Number (KTP)</label>
            <input id="u_id_number" type="text" name="idNumber" class="form-control" required inputmode="numeric" maxlength="20" placeholder="Nomor KTP">
          </div>

          <div class="form-group">
            <label for="u_address">Address</label>
            <textarea id="u_address" name="address" class="form-control" required placeholder="Alamat sesuai KTP"></textarea>
          </div>

          <div class="form-group">
            <label for="u_resident_address">Resident Address</label>
            <textarea id="u_resident_address" name="resident_address" class="form-control" required placeholder="Alamat domisili saat ini"></textarea>
          </div>

          <div class="form-group">
            <label for="u_phone">Phone</label>
            <input id="u_phone" type="text" name="phone" class="form-control" required inputmode="tel" maxlength="20" placeholder="08xxxxxxxxxx">
          </div>

          <div class="btn-row">
            <button type="submit" class="btn-grad"><i class="fas fa-save"></i> Simpan</button>
            <a href="<?= base_url('master/customer') ?>" class="btn-soft"><i class="fas fa-arrow-left"></i> Kembali</a>
          </div>
        </form>
      </div>
    </section>

  </div>
</div>

<script>
  // Select2 (kalau ada)
  $("#materialType").select2();
  $(".select2").select2();

  // jqKeyboard untuk input teks
  jQuery(function($){
    if ($.fn.keyboard){
      $('#u_name, #u_address, #u_resident_address, #u_phone, #u_id_number').keyboard({ layout:'qwerty' });
    }
  });
</script>
