<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
  $dateStart = date('Y-m-01');
  $dateEnd   = date('Y-m-t');
?>
<style>
  /* === SCOPED: tidak menyentuh :root === */
  .report-scope .master-wrap{
    margin-top: calc(var(--topbar-h) + 12px);
    padding: clamp(12px, 2vw, 20px);
  }
  .report-scope .master-container{ max-width:1160px; margin-inline:auto; }

  /* breadcrumb chip (samakan dengan Master) */
  .report-scope .master-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .report-scope .master-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .report-scope .master-crumbs a:hover{ color:var(--blue-light); text-decoration:underline; }
  .report-scope .master-crumbs .sep{ color:#7a8eb8; }

  /* heading */
  .report-scope .master-head h3{
    margin:8px 0 6px; color:#fff; font-weight:800;
    font-size:clamp(20px, 3.2vw, 28px); text-align:left;
  }
  .report-scope .master-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* grid & card */
  .report-scope .master-grid{
    margin-top:14px;
    display:grid; gap:14px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
  .report-scope .master-card{
    display:flex; gap:14px; align-items:flex-start;
    background:#fff; color:#0b0f1a; text-decoration:none;
    border:1px solid #e6eefc; border-radius:16px; padding:14px 16px;
    box-shadow:0 10px 20px rgba(0,0,0,.06);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
  }
  .report-scope .master-card:hover{
    transform:translateY(-2px);
    box-shadow:0 18px 36px rgba(0,0,0,.12);
    border-color:#d7e5ff;
  }
  .report-scope .master-ico{
    flex:0 0 46px; height:46px; display:grid; place-items:center; border-radius:14px;
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border:1px solid #d7f4ff;
  }
  /* penting: samakan warna ikon dengan halaman Master */
  .report-scope .master-ico svg{ stroke: var(--blue-light); }

  .report-scope .master-tit{ margin:0; font-weight:700; font-size:15px; color:#0e204a; }
  .report-scope .master-sub{ margin:4px 0 0; font-size:13px; color:#6378a7; }

  /* back button */
  .report-scope .master-actions{ margin-top:12px; }
  .report-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px;
    text-decoration:none; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .report-scope .btn-back:hover{ border-color:#cfe0ff; }
</style>

<div class="report-scope">
  <div class="master-wrap">
    <div class="master-container">

      <!-- Breadcrumb -->
      <nav class="master-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <span>Report</span>
      </nav>

      <!-- Header -->
      <header class="master-head">
        <h3>Report</h3>
        <p>Pilih jenis laporan transaksi atau grafik periode berjalan.</p>
      </header>

      <!-- Grid menu -->
      <section class="master-grid" aria-label="Menu Report">
        <a href="<?= base_url('report/buy?dateStart='.$dateStart.'&dateEnd='.$dateEnd) ?>" class="master-card">
          <span class="master-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 12.39A2 2 0 0 0 9.62 15H19a2 2 0 0 0 2-1.72l1-7H6"></path>
            </svg>
          </span>
          <span>
            <h4 class="master-tit">Buy</h4>
            <p class="master-sub">Laporan pembelian (<?= $dateStart ?> s/d <?= $dateEnd ?>).</p>
          </span>
        </a>

        <a href="<?= base_url('report/sell?dateStart='.$dateStart.'&dateEnd='.$dateEnd) ?>" class="master-card">
          <span class="master-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </span>
          <span>
            <h4 class="master-tit">Sell</h4>
            <p class="master-sub">Laporan penjualan (<?= $dateStart ?> s/d <?= $dateEnd ?>).</p>
          </span>
        </a>

        <!-- <a href="<?= base_url('report/buy-graph') ?>" class="master-card">
          <span class="master-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
              <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
          </span>
          <span>
            <h4 class="master-tit">Buy Graph</h4>
            <p class="master-sub">Visualisasi tren pembelian.</p>
          </span>
        </a> -->

        <!-- <a href="<?= base_url('report/sell-graph') ?>" class="master-card">
          <span class="master-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="20" x2="18" y2="10"></line>
              <line x1="12" y1="20" x2="12" y2="4"></line>
              <line x1="6" y1="20" x2="6" y2="14"></line>
            </svg>
          </span>
          <span>
            <h4 class="master-tit">Sell Graph</h4>
            <p class="master-sub">Visualisasi tren penjualan.</p>
          </span>
        </a> -->
      </section>

      <div class="master-actions">
        <a href="<?= base_url('dashboard') ?>" class="btn-back">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Kembali ke Dashboard
        </a>
      </div>

    </div>
  </div>
</div>
