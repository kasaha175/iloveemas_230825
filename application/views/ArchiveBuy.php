<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped agar tak bentrok template lain ===== */
  .archive-buy-scope .wrap{ margin-top: calc(var(--topbar-h) + 12px); padding: clamp(12px, 2vw, 20px); }
  .archive-buy-scope .container-max{ max-width:1160px; margin-inline:auto; }

  /* Breadcrumb pil (match Master) */
  .archive-buy-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .archive-buy-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .archive-buy-scope .crumbs a:hover{ color:var(--blue-light); text-decoration:underline; }
  .archive-buy-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .archive-buy-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .archive-buy-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Grid kartu menu */
  .archive-buy-scope .menu-grid{
    display:grid; gap:16px;
    grid-template-columns: repeat(2, minmax(0,1fr));
  }
  @media (max-width: 768px){
    .archive-buy-scope .menu-grid{ grid-template-columns: 1fr; }
  }
  .archive-buy-scope .menu-item{
    display:flex; align-items:center; gap:16px; padding:18px 20px;
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border:1px solid #e6eefc; border-radius:16px; text-decoration:none;
    box-shadow:0 10px 22px rgba(0,0,0,.08);
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    color:#0e204a;
  }
  .archive-buy-scope .menu-item:hover{
    transform: translateY(-2px);
    border-color:#d0e3ff;
    box-shadow:0 14px 26px rgba(0,0,0,.10);
  }

  /* Ikon/Badge elemen */
  .archive-buy-scope .icon-bubble{
    width:48px; height:48px; border-radius:14px;
    display:grid; place-items:center;
    background:#fff; color:var(--blue-dark);
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    font-weight:800; letter-spacing:.5px;
  }
  .archive-buy-scope .menu-title{ font-weight:800; margin:0; }
  .archive-buy-scope .menu-desc { margin:2px 0 0; font-size:13px; color:#3a4e7a; }

  /* Aksi bawah */
  .archive-buy-scope .actions{ margin-top:12px; }
  .archive-buy-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px; background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px; text-decoration:none;
    box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .archive-buy-scope .btn-back:hover{ border-color:#cfe0ff; }
</style>

<div class="archive-buy-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive') ?>">Archive</a>
        <span class="sep">/</span>
        <span>Buy</span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Archive Buy</h3>
        <p>Pilih jenis RTI untuk melihat arsip transaksi pembelian.</p>
      </header>

      <!-- Menu -->
      <section class="menu-grid">
        <a class="menu-item" href="<?= base_url('archive/buy/?key=rti-au') ?>">
          <span class="icon-bubble">AU</span>
          <div>
            <h5 class="menu-title">RTI AU</h5>
            <p class="menu-desc">Arsip pembelian untuk material emas (Au).</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/buy/?key=rti-pt') ?>">
          <span class="icon-bubble">PT</span>
          <div>
            <h5 class="menu-title">RTI PT</h5>
            <p class="menu-desc">Arsip pembelian untuk material platinum (Pt).</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/buy/?key=rti-ag') ?>">
          <span class="icon-bubble">AG</span>
          <div>
            <h5 class="menu-title">RTI AG</h5>
            <p class="menu-desc">Arsip pembelian untuk material perak (Ag).</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/buy/?key=rti-ru') ?>">
          <span class="icon-bubble">RU</span>
          <div>
            <h5 class="menu-title">RTI RU</h5>
            <p class="menu-desc">Arsip pembelian untuk material ruthenium (Ru).</p>
          </div>
        </a>

        <a class="menu-item" href <?= '<?= base_url(\'archive/buy/?key=rti-ta\') ?>' ?>
          <span class="icon-bubble">TA</span>
          <div>
            <h5 class="menu-title">RTI TA</h5>
            <p class="menu-desc">Arsip pembelian untuk material tantalum (Ta).</p>
          </div>
        </a>
      </section>

      <!-- Back -->
      <div class="actions">
        <a href="<?= base_url('archive') ?>" class="btn-back">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Kembali ke Archive
        </a>
      </div>

    </div>
  </div>
</div>
