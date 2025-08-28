<?php
// ambil style tombol dari config (fallback aman)
$ui       = $this->config->item('ui') ?? [];
$btnLight = $ui['btn']['lightLg'] ?? 'btn btn-light btn-lg';
// Ikon back dari config (fallback)
$iconBack = $this->config->item('iconBack') ?? 'fas fa-arrow-left';
?>
<style>
  :root{
    /* fallback supaya seragam dengan Buy */
    --blue-light:#074799;
    --blue-dark:#001A6E;
    --blue-pastel:#B1F0F7;
    --text:#0B0F1A;
    --card-bg:#ffffff;
    --card-br:#e6eefc;
    --shadow:0 18px 30px rgba(0,0,0,.10);
    --radius:18px;
    --topbar-h:72px;

    /* PENTING: default untuk grid */
    --grid-min: clamp(150px, 16vw, 180px);
  }

  .ilv-sell{ padding:clamp(16px,2.2vw,28px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1200px; margin-inline:auto; }

  /* Hero */
  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(18px,3.2vw,28px);
    box-shadow: var(--shadow);
  }
  .ilv-crumbs{
    display:flex; gap:10px; align-items:center; width:max-content;
    background: rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22);
    padding:8px 12px; border-radius:9999px; margin-bottom:8px;
    backdrop-filter: blur(6px);
  }
  .ilv-crumbs a{ color:#e8f2ff; text-decoration:none; font-weight:700; font-size:13px; }
  .ilv-crumbs a:hover{ text-decoration:underline; }
  .ilv-crumbs .sep{ color:#c9defe; opacity:.8; }

  .ilv-hero h1{ margin:0 0 4px; font-weight:800; font-size:clamp(20px,3.2vw,30px); }
  .ilv-hero p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Header actions */
  .ilv-head{ margin-top:12px; display:flex; justify-content:flex-end; }

  /* Grid – samakan dengan Buy */
  .ilv-grid{
    margin-top: clamp(18px, 2.5vw, 24px);
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(var(--grid-min), 1fr));
    gap:16px;
  }

  .ilv-item{
    display:flex; flex-direction:column; text-decoration:none; color:var(--text);
    background:var(--card-bg); border:1px solid var(--card-br);
    border-radius:14px; overflow:hidden;
    box-shadow:0 6px 14px rgba(0,0,0,.06);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
    min-height:180px; /* konsisten dengan Buy */
  }
  .ilv-item:hover{ transform: translateY(-2px); box-shadow:0 14px 28px rgba(0,0,0,.12); border-color:#d7e5ff; background:#fff; }

  .ilv-thumb{
    display:grid; place-items:center;
    min-height:120px; padding:14px 12px 8px;
    background:linear-gradient(180deg,rgba(241,248,255,.65),rgba(255,255,255,1));
  }
  .ilv-thumb img{
    width:80%; max-height:100px; object-fit:contain; border-radius:10px;
  }

  .ilv-caption{
    text-align:center; padding:10px 12px; font-weight:800; color:#001A6E;
    border-top:1px solid var(--card-br); font-size:14px;
  }

  .ilv-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#ffffff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:999px; padding:7px 12px; text-decoration:none; font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .ilv-back:hover{ transform: translateY(-1px); box-shadow:0 14px 26px rgba(0,0,0,.12); border-color:#d7e5ff; }


  /* Meta footer */
  .ilv-meta{
    margin-top:12px; display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;
    font-size:12px; color:#26406e;
  }

  .ilv-badge{ background:#ffffff; color:#0e2b68; border:1px solid #dfeaff; padding:6px 10px; border-radius:999px; }

  @media (min-width:1200px){
    :root{ --grid-min: 180px; }
  }
</style>

<div class="ilv-sell">
  <div class="ilv-container">
    <section class="ilv-hero" aria-label="Sell — Choose Material">
      <!-- Breadcrumb -->
      <nav class="ilv-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>" class="fa fa-home" aria-label="Home"></a>
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= base_url('transaction') ?>">Transaction</a>
        <span class="sep">›</span>
        <span>Sell</span>
      </nav>

      <header>
        <h1>Sell — Choose Material</h1>
        <p>Pilih jenis material untuk memulai transaksi penjualan.</p>
      </header>

      <div class="ilv-head">
        <a href="<?= base_url('transaction') ?>" class="ilv-back" aria-label="Kembali ke Transaction">
          <i class="<?= htmlspecialchars($iconBack, ENT_QUOTES) ?>"></i> Kembali
        </a>
      </div>
    </section>

    <!-- Grid items -->
    <div class="ilv-grid">
      <?php foreach ($data as $d): ?>
        <a class="ilv-item" href="<?= base_url('transaction/sell/'.$d->m_id) ?>/">
          <div class="ilv-thumb">
            <img
              src="<?= base_url('assets/offline/'.$d->m_img) ?>"
              alt="<?= htmlspecialchars($d->m_name,ENT_QUOTES) ?>">
          </div>
          <div class="ilv-caption"><?= htmlspecialchars($d->m_name,ENT_QUOTES) ?></div>
        </a>
      <?php endforeach; ?>
    </div>

     <!-- Footer Meta (versi/brand) -->
    <div class="ilv-meta">
      <span class="ilv-badge">Mode Transaksi — Sell</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>
  </div>
</div>
