<?php

// Data material
$materials = [
  ["url" => "buy/1",                "img" => "diamond.png",    "name" => "Diamond"],
  ["url" => "buy/2",                "img" => "gold.png",       "name" => "Gold"],
  ["url" => "buy/lm/select",        "img" => "ubs.png",        "name" => "LM & UBS"],
  ["url" => "buy/silver/select",    "img" => "silver.png",     "name" => "Silver"],
  ["url" => "buy/platinum/select",  "img" => "platinum.png",   "name" => "Platinum"],
  ["url" => "buy/paladium/select",  "img" => "paladium.png",   "name" => "Paladium"],
  ["url" => "buy/iridium/select",   "img" => "iridium.png",    "name" => "Iridium"],
  ["url" => "buy/rhodium/select",   "img" => "rhodium.png",    "name" => "Rhodium"],
  ["url" => "buy/10",               "img" => "material-au.png","name" => "C. Profesional"],
  ["url" => "buy/ruthenium/select", "img" => "ruthenium.png",  "name" => "Ruthenium"],
  ["url" => "buy/21",               "img" => "tantalum.png",   "name" => "Tantalum"],
];
?>

<style>
  :root{
    --blue-light:#074799;
    --blue-dark:#001A6E;
    --blue-pastel:#B1F0F7;
    --text:#0B0F1A;
    --muted:#6b7a99;
    --card-bg:#ffffff;
    --card-br:#e6eefc;
    --shadow:0 18px 30px rgba(0,0,0,.10);
    --radius:18px;
    --topbar-h:72px;

    /* ukuran minimum kolom supaya grid bisa merapat */
    --grid-min: clamp(140px, 16vw, 170px);
  }

  .ilv-buy{ padding:clamp(14px,2vw,24px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1200px; margin-inline:auto; }

  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(16px,3vw,28px);
    box-shadow: var(--shadow);
  }
  .ilv-hero h1{ margin:0 0 4px; font-weight:800; letter-spacing:.2px; font-size:clamp(18px,3vw,28px); }
  .ilv-hero p{ margin:0; opacity:.96; font-size:clamp(12px,1.2vw,14px); }

  .ilv-hero .blob{position:absolute; border-radius:50%; filter:blur(44px); opacity:.25; pointer-events:none;}
  .ilv-hero .b1{ width:320px;height:320px;background:var(--blue-pastel);right:-120px;top:-120px;}
  .ilv-hero .b2{ width:200px;height:200px;background:#8fd2ff;left:-80px;bottom:-100px;opacity:.2;}

  /* Breadcrumb pills */
  .ilv-crumbs{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    background: rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.20);
    backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    border-radius:999px; padding:8px 12px; width:max-content;
  }
  .ilv-crumbs a{ color:#e8f2ff; text-decoration:none; font-weight:600; }
  .ilv-crumbs .sep{ color:#c9defe; opacity:.7; }

  /* Header actions */
  .ilv-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-top:12px;
  }
  .ilv-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#ffffff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:999px; padding:7px 12px; text-decoration:none; font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .ilv-back:hover{ transform: translateY(-1px); box-shadow:0 14px 26px rgba(0,0,0,.12); border-color:#d7e5ff; }

  /* ====== COMPACT GRID ====== */
  .ilv-grid{
    margin-top: clamp(14px, 2vw, 20px);
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(var(--grid-min), 1fr));
    gap:10px;
  }

  .ilv-item{
    text-decoration:none; color:var(--text);
    background:var(--card-bg); border:1px solid var(--card-br);
    border-radius:14px; overflow:hidden;
    box-shadow:0 6px 14px rgba(0,0,0,.06);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
    display:flex; flex-direction:column; min-height:150px;
  }
  .ilv-item:hover{ transform: translateY(-2px); box-shadow:0 14px 28px rgba(0,0,0,.12); border-color:#d7e5ff; background:#fff; }

  .ilv-thumb{
    display:grid; place-items:center; min-height:96px; padding:10px 10px 6px;
    background:linear-gradient(180deg,rgba(241,248,255,.6),rgba(255,255,255,1));
  }
  .ilv-thumb img{ width:82%; max-height:86px; object-fit:contain; border-radius:10px; }

  .ilv-caption{
    text-align:center; padding:8px 10px; font-weight:800; color:#001A6E;
    border-top:1px solid var(--card-br); font-size:14px;
  }

  /* Meta footer */
  .ilv-meta{
    margin-top:12px; display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;
    font-size:12px; color:#26406e;
  }
  .ilv-badge{ background:#ffffff; color:#0e2b68; border:1px solid #dfeaff; padding:6px 10px; border-radius:999px; }

  /* Sedikit lebih rapat di layar besar */
  @media (min-width:1200px){
    :root{ --grid-min: 180px; } /* tetap harmonis di monitor besar */
    .ilv-grid{ gap:12px; }
  }

  @media (max-width:480px){
    .ilv-hero{ padding:14px; }
  }
</style>


<div class="ilv-buy">
  <div class="ilv-container">

    <!-- Hero -->
    <section class="ilv-hero" aria-label="Buy - Pilih Material">
      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <div class="ilv-crumbs">
          <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i>&nbsp;Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= base_url('transaction') ?>">Transaction</a>
          <span class="sep">›</span>
          <span style="opacity:.9">Buy</span>
        </div>
      </nav>

      <div class="ilv-head">
        <div>
          <h1>Buy — Choose Material</h1>
          <p>Pilih jenis material untuk memulai transaksi pembelian.</p>
        </div>

        <!-- Tombol back: jika ingin 100% dari config, ganti class ke <?= htmlspecialchars($btnPrimary, ENT_QUOTES) ?> -->
        <a href="<?= base_url('transaction') ?>" class="ilv-back" aria-label="Kembali ke Transaction">
          <i class="<?= htmlspecialchars($iconBack, ENT_QUOTES) ?>"></i> Kembali
        </a>
      </div>
    </section>

    <!-- Grid Material -->
    <section class="ilv-grid" aria-label="Pilihan Material">
      <?php foreach ($materials as $m): ?>
        <a class="ilv-item" href="<?= base_url('transaction/'.$m['url'].'/') ?>" aria-label="Pilih <?= htmlspecialchars($m['name']) ?>">
          <div class="ilv-thumb">
            <img
              src="<?= base_url('assets/offline/'.$m['img']) ?>"
              alt="<?= htmlspecialchars($m['name']) ?>"
              loading="lazy" decoding="async">
          </div>
          <div class="ilv-caption"><?= htmlspecialchars($m['name']) ?></div>
        </a>
      <?php endforeach; ?>
    </section>

    <!-- Footer Meta (versi/brand) -->
    <div class="ilv-meta">
      <span class="ilv-badge">Mode Transaksi — Buy</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>

  </div>
</div>
