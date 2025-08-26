<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped agar tak bentrok template lain ===== */
  .archive-sell-scope .wrap{ margin-top: calc(var(--topbar-h, 98px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .archive-sell-scope .container-max{ max-width:1160px; margin-inline:auto; }

  /* Breadcrumb pil (match gaya Master / Archive Buy) */
  .archive-sell-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .archive-sell-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .archive-sell-scope .crumbs a:hover{ color:var(--blue-light, #4a7dff); text-decoration:underline; }
  .archive-sell-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .archive-sell-scope .heading h3{
    margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px);
  }
  .archive-sell-scope .heading p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Grid kartu menu */
  .archive-sell-scope .menu-grid{
    display:grid; gap:16px;
    grid-template-columns: repeat(3, minmax(0,1fr));
  }
  @media (max-width: 992px){
    .archive-sell-scope .menu-grid{ grid-template-columns: repeat(2, minmax(0,1fr)); }
  }
  @media (max-width: 576px){
    .archive-sell-scope .menu-grid{ grid-template-columns: 1fr; }
  }

  .archive-sell-scope .menu-item{
    display:flex; align-items:center; gap:16px; padding:18px 20px;
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #e9faff);
    border:1px solid #e6eefc; border-radius:16px; text-decoration:none;
    box-shadow:0 10px 22px rgba(0,0,0,.08);
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease, background .15s ease;
    color:#0e204a;
  }
  .archive-sell-scope .menu-item:hover{
    transform: translateY(-2px);
    border-color:#d0e3ff;
    box-shadow:0 14px 26px rgba(0,0,0,.10);
    background: linear-gradient(135deg, #eaf3ff, #f2fbff);
  }

  /* Ikon/Badge elemen */
  .archive-sell-scope .icon-bubble{
    width:48px; height:48px; border-radius:14px;
    display:grid; place-items:center;
    background:#fff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    font-weight:800; letter-spacing:.5px;
  }

  .archive-sell-scope .menu-title{ font-weight:800; margin:0; }
  .archive-sell-scope .menu-desc { margin:2px 0 0; font-size:13px; color:#3a4e7a; }

  /* Aksi bawah */
  .archive-sell-scope .actions{ margin-top:12px; }
  .archive-sell-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px; background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px; text-decoration:none;
    box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .archive-sell-scope .btn-back:hover{ border-color:#cfe0ff; }
</style>

<div class="archive-sell-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive') ?>">Archive</a>
        <span class="sep">/</span>
        <span>Sell</span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Archive Sell</h3>
        <p>Pilih kategori untuk mengelola arsip penjualan.</p>
      </header>

      <!-- Menu Grid -->
      <section class="menu-grid">
        <a class="menu-item" href="<?= base_url('archive/sell/?key=lm') ?>" aria-label="Archive Sell LM">
          <span class="icon-bubble">LM</span>
          <div>
            <h5 class="menu-title">LM</h5>
            <p class="menu-desc">Tarif/arsip penjualan Logam Mulia per gramase.</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/sell/?key=material-au') ?>" aria-label="Archive Sell Material AU">
          <span class="icon-bubble">AU</span>
          <div>
            <h5 class="menu-title">Material AU</h5>
            <p class="menu-desc">Arsip penjualan material emas (Au).</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/sell/?key=material-ag') ?>" aria-label="Archive Sell Material AG">
          <span class="icon-bubble">AG</span>
          <div>
            <h5 class="menu-title">Material AG</h5>
            <p class="menu-desc">Arsip penjualan material perak (Ag).</p>
          </div>
        </a>

        <a class="menu-item" href="<?= base_url('archive/sell/?key=material-ubs') ?>" aria-label="Archive Sell UBS">
          <span class="icon-bubble">UBS</span>
          <div>
            <h5 class="menu-title">UBS</h5>
            <p class="menu-desc">Arsip penjualan produk UBS.</p>
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
