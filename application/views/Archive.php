<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped agar tidak bentrok dengan template lain ===== */
  .archive-scope .wrap    { margin-top: calc(var(--topbar-h) + 12px); padding: clamp(12px, 2vw, 20px); }
  .archive-scope .container-max { max-width:1160px; margin-inline:auto; }

  /* Breadcrumb pil seperti halaman Master */
  .archive-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .archive-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .archive-scope .crumbs a:hover{ color:var(--blue-light); text-decoration:underline; }
  .archive-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .archive-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .archive-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Kartu menu (mirip Master) */
  .archive-scope .menu-grid{
    display:grid; gap:16px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  @media (max-width: 768px){
    .archive-scope .menu-grid{ grid-template-columns: 1fr; }
  }
  .archive-scope .menu-item{
    display:flex; align-items:center; gap:16px; padding:18px 20px;
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border:1px solid #e6eefc; border-radius:16px; text-decoration:none;
    box-shadow:0 10px 22px rgba(0,0,0,.08);
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    color:#0e204a;
  }
  .archive-scope .menu-item:hover{
    transform: translateY(-2px);
    border-color:#d0e3ff;
    box-shadow:0 14px 26px rgba(0,0,0,.10);
  }
  .archive-scope .icon-bubble{
    width:48px; height:48px; border-radius:14px;
    display:grid; place-items:center;
    background:#fff; color:var(--blue-dark);
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    flex:0 0 auto;
  }
  .archive-scope .menu-title{ font-weight:800; margin:0; }
  .archive-scope .menu-desc { margin:2px 0 0; font-size:13px; color:#3a4e7a; }

  /* Tombol kembali (sama seperti Master) */
  .archive-scope .actions{ margin-top:12px; }
  .archive-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px; background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px; text-decoration:none;
    box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .archive-scope .btn-back:hover{ border-color:#cfe0ff; }
</style>

<div class="archive-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <span>Archive</span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Archive</h3>
        <p>Pilih kategori arsip transaksi.</p>
      </header>

      <!-- Menu cards -->
      <section class="menu-grid">
        <a class="menu-item" href="<?= base_url('archive/buy') ?>">
          <span class="icon-bubble"><i class="fas fa-download"></i></span>
          <div>
            <h5 class="menu-title">BUY</h5>
            <p class="menu-desc">Lihat arsip transaksi pembelian.</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/sell') ?>">
          <span class="icon-bubble"><i class="fas fa-upload"></i></span>
          <div>
            <h5 class="menu-title">SELL</h5>
            <p class="menu-desc">Lihat arsip transaksi penjualan.</p>
          </div>
        </a>
      </section>

      <!-- Back -->
      <div class="actions">
        <a href="<?= base_url('dashboard') ?>" class="btn-back">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Kembali ke Dashboard
        </a>
      </div>

    </div>
  </div>
</div>
