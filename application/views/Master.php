<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== scope: hanya untuk halaman Master ===== */

  .master-wrap{
    margin-top: calc(var(--topbar-h) + 12px);
    padding: clamp(12px, 2vw, 20px);
  }
  .master-container{ max-width: 1160px; margin-inline: auto; }

  /* breadcrumb chip pastel — tidak mengubah navbar */
  .master-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .master-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .master-crumbs a:hover{ color:var(--blue-dark); text-decoration:underline; }
  .master-crumbs .sep{ color:#7a8eb8; }

  .master-head h3{
    margin: 8px 0 6px; color:#fff; font-weight:800;
    font-size: clamp(20px, 3.2vw, 28px);
    text-align:left;
  }
  .master-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* grid menu */
  .master-grid{
    margin-top:14px;
    display:grid; gap:14px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
  .master-card{
    display:flex; gap:14px; align-items:flex-start;
    background:#fff; color:#0b0f1a; text-decoration:none;
    border:1px solid #e6eefc; border-radius:16px; padding:14px 16px;
    box-shadow:0 10px 20px rgba(0,0,0,.06);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .master-card:hover{ transform:translateY(-2px); box-shadow:0 18px 36px rgba(0,0,0,.12); border-color:#d7e5ff; }
  .master-ico{
    flex:0 0 46px; height:46px; display:grid; place-items:center; border-radius:14px;
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border: 1px solid #d7f4ff;
  }
  .master-tit{ margin:0; font-weight:700; font-size:15px; color:#0e204a; }
  .master-sub{ margin:4px 0 0; font-size:13px; color:#6378a7; }

  /* tombol kembali */
  .master-actions{ margin-top:12px; }
  .btn-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px;
    text-decoration:none; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-back:hover{ border-color:#cfe0ff; }
</style>

<div class="master-wrap">
  <div class="master-container">

    <!-- Breadcrumb (hanya konten, navbar tidak diubah) -->
    <nav class="master-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <span>Master</span>
    </nav>

    <!-- Judul + subjudul -->
    <header class="master-head">
      <h3>Master</h3>
      <p>Atur data master aplikasi <strong>I Love Emas</strong>.</p>
    </header>

    <!-- Grid menu -->
    <section class="master-grid" aria-label="Menu Master">
      <a href="<?= base_url('master/customer') ?>" class="master-card">
        <span class="master-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
          </svg>
        </span>
        <span>
          <h4 class="master-tit">Customer</h4>
          <p class="master-sub">Kelola data pelanggan.</p>
        </span>
      </a>

      <a href="<?= base_url('master/memo') ?>" class="master-card">
        <span class="master-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M20 2H8a2 2 0 0 0-2 2v14"/><path d="M18 2v4a2 2 0 0 0 2 2h4"/>
          </svg>
        </span>
        <span>
          <h4 class="master-tit">Syarat & Ketentuan</h4>
          <p class="master-sub">Kelola memo/S&K yang tampil di sistem.</p>
        </span>
      </a>

      <a href="<?= base_url('master/cabang') ?>" class="master-card">
        <span class="master-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
          </svg>
        </span>
        <span>
          <h4 class="master-tit">Daftar Cabang</h4>
          <p class="master-sub">Kelola data cabang/gerai.</p>
        </span>
      </a>
    </section>

    <!-- Tombol kembali -->
    <div class="master-actions">
      <a href="<?= base_url('dashboard') ?>" class="btn-back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali ke Dashboard
      </a>
    </div>

  </div>
</div>
