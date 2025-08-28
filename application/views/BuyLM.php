<?php
// Konfigurasi kecil agar seragam
$iconBack = $this->config->item('iconBack') ?? 'fas fa-arrow-left';

// Data tile LM
$lmTiles = [
  ['id' => 3,  'name' => 'LM Certi',  'img' => 'lm-baru.png'],
  ['id' => 4,  'name' => 'LM Retro',  'img' => 'lm-lama.png'],
  ['id' => 17, 'name' => 'UBS',       'img' => 'lm-lama.png'],
  ['id' => 23, 'name' => 'Gold Bar',  'img' => 'lm-lama.png'],
];
?>
<style>
  :root{
    --blue-light:#074799; --blue-dark:#001A6E; --blue-pastel:#B1F0F7;
    --text:#0B0F1A; --muted:#6b7a99;
    --card-bg:#fff; --card-br:#e6eefc;
    --shadow:0 18px 30px rgba(0,0,0,.10);
    --radius:18px; --topbar-h:72px;
    --grid-min: clamp(150px, 18vw, 190px);
  }

  .ilv-buy{ padding:clamp(14px,2vw,24px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1200px; margin-inline:auto; }

  /* HERO (header kartu) */
  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(16px,3vw,28px);
    box-shadow:var(--shadow);
  }
  .ilv-crumbs{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20);
    border-radius:999px; padding:8px 12px; width:max-content;
    backdrop-filter:blur(6px);
  }
  .ilv-crumbs a{ color:#e8f2ff; text-decoration:none; font-weight:600; }
  .ilv-crumbs .sep{ color:#c9defe; opacity:.7; }

  .ilv-hero h1{ margin:8px 0 4px; font-weight:800; font-size:clamp(18px,3vw,28px); }
  .ilv-hero p{ margin:0; opacity:.95; font-size:clamp(12px,1.2vw,14px); }
  .ilv-head{ margin-top:12px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }

  /* Tombol kembali (seragam) */
  .ilv-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#fff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:999px; padding:7px 12px; text-decoration:none; font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition:transform .12s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .ilv-back:hover{ transform:translateY(-1px); box-shadow:0 14px 26px rgba(0,0,0,.12); border-color:#d7e5ff; }

  /* GRID TILE */
  .ilv-grid{
    margin-top: clamp(14px, 2vw, 20px);
    display:grid; grid-template-columns:repeat(auto-fit,minmax(var(--grid-min),1fr));
    gap:12px;
  }
  .ilv-item{
    text-decoration:none; color:var(--text);
    background:var(--card-bg); border:1px solid var(--card-br);
    border-radius:14px; overflow:hidden;
    box-shadow:0 6px 14px rgba(0,0,0,.06);
    transition:transform .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
    display:flex; flex-direction:column; min-height:150px;
  }
  .ilv-item:hover{ transform:translateY(-2px); box-shadow:0 14px 28px rgba(0,0,0,.12); border-color:#d7e5ff; background:#fff; }

  .ilv-thumb{
    display:grid; place-items:center; min-height:110px; padding:12px 10px 6px;
    background:linear-gradient(180deg,rgba(241,248,255,.6),#fff);
  }
  .ilv-thumb img{ width:86%; max-height:100px; object-fit:contain; border-radius:10px; }

  .ilv-caption{
    text-align:center; padding:10px 12px; font-weight:800; color:#001A6E;
    border-top:1px solid var(--card-br); font-size:14px;
  }

  /* FOOTER META (seragam) */
  .ilv-meta{
    margin-top:12px; display:flex; flex-wrap:wrap; gap:10px;
    align-items:center; justify-content:space-between;
    font-size:12px; color:#26406e;
  }
  .ilv-badge{ background:#fff; color:#0e2b68; border:1px solid #dfeaff; padding:6px 10px; border-radius:999px; }

  @media (max-width:480px){ .ilv-hero{ padding:14px; } }
</style>

<div class="ilv-buy">
  <div class="ilv-container">

    <!-- HERO -->
    <section class="ilv-hero" aria-label="Buy — LM">
      <nav aria-label="breadcrumb">
        <div class="ilv-crumbs">
          <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i>&nbsp;Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= base_url('transaction') ?>">Transaction</a>
          <span class="sep">›</span>
          <a href="<?= base_url('transaction/buy') ?>">Buy</a>
          <span class="sep">›</span>
          <span style="opacity:.9">LM</span>
        </div>
      </nav>

      <div class="ilv-head">
        <div>
          <h1>Buy — Choose LM Variant</h1>
          <p>Pilih varian LM untuk memulai transaksi pembelian.</p>
        </div>
        <!-- tombol back di ATAS -->
        <a href="<?= base_url('transaction/buy') ?>" class="ilv-back" aria-label="Kembali ke daftar material">
          <i class="<?= htmlspecialchars($iconBack, ENT_QUOTES) ?>"></i> Kembali
        </a>
      </div>
    </section>

    <!-- GRID -->
    <section class="ilv-grid" aria-label="Pilihan LM">
      <?php foreach ($lmTiles as $t): ?>
        <a class="ilv-item" href="<?= base_url('transaction/buy/'.$t['id'].'/') ?>" aria-label="Pilih <?= htmlspecialchars($t['name']) ?>">
          <div class="ilv-thumb">
            <img src="<?= base_url('assets/img/sell-icon/'.$t['img']) ?>"
                 alt="<?= htmlspecialchars($t['name']) ?>" loading="lazy" decoding="async">
          </div>
          <div class="ilv-caption"><?= htmlspecialchars($t['name']) ?></div>
        </a>
      <?php endforeach; ?>
    </section>

    <!-- FOOTER META -->
    <div class="ilv-meta">
      <span class="ilv-badge">Mode Transaksi — Buy (LM)</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>

  </div>
</div>
