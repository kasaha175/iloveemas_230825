<?php
// Nama material dari URL (segment 3) + normalisasi tampilan
$kode = ucwords((string)($this->uri->segment(3) ?? ''));

// Ambil m_id sekali (hindari query berulang)
$materialId = (int) ($this->db->select('m_id')->where('m_name', $kode)->get('tb_material')->row('m_id') ?? 0);

// Ikon back dari config (fallback)
$iconBack = $this->config->item('iconBack') ?? 'fas fa-arrow-left';
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
    --grid-min: clamp(140px, 16vw, 170px);
  }

  .ilv-buy{ padding:clamp(14px,2vw,24px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1000px; margin-inline:auto; }

  /* Hero (sama dgn halaman Buy) */
  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(16px,3vw,28px);
    box-shadow: var(--shadow);
  }
  .ilv-hero h1{ margin:0 0 4px; font-weight:800; letter-spacing:.2px; font-size:clamp(18px,3vw,28px); }
  .ilv-hero p{ margin:0; opacity:.96; font-size:clamp(12px,1.2vw,14px); }

  .ilv-crumbs{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    background: rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.20);
    backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    border-radius:999px; padding:8px 12px; width:max-content;
  }
  .ilv-crumbs a{ color:#e8f2ff; text-decoration:none; font-weight:600; }
  .ilv-crumbs .sep{ color:#c9defe; opacity:.7; }

  .ilv-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-top:12px;
  }

  /* Tombol kembali (sama) */
  .ilv-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#ffffff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:999px; padding:7px 12px; text-decoration:none; font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .ilv-back:hover{ transform: translateY(-1px); box-shadow:0 14px 26px rgba(0,0,0,.12); border-color:#d7e5ff; }

  /* Grid kartu (pakai gaya tile yg sama dengan halaman Buy) */
  .ilv-grid{
    margin-top: clamp(14px, 2vw, 20px);
    display:grid; grid-template-columns: repeat(auto-fit, minmax(var(--grid-min), 1fr));
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
  .ilv-thumb .ico{ font-size:28px; color:#074799; }
  .ilv-caption{
    text-align:center; padding:8px 10px; font-weight:800; color:#001A6E;
    border-top:1px solid var(--card-br); font-size:14px;
  }
  .ilv-sub{ text-align:center; font-size:12px; color:var(--muted); padding:0 10px 12px; }

  /* Footer meta (sama) */
  .ilv-meta{
    margin-top:12px; display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;
    font-size:12px; color:#26406e;
  }
  .ilv-badge{ background:#ffffff; color:#0e2b68; border:1px solid #dfeaff; padding:6px 10px; border-radius:999px; }

  @media (min-width:1200px){
    :root{ --grid-min: 220px; }
    .ilv-grid{ gap:12px; }
  }
</style>

<div class="ilv-buy">
  <div class="ilv-container">

    <!-- Hero -->
    <section class="ilv-hero" aria-label="Buy — Pilih Mode">
      <nav aria-label="breadcrumb">
        <div class="ilv-crumbs">
          <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i>&nbsp;Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= base_url('transaction') ?>">Transaction</a>
          <span class="sep">›</span>
          <a href="<?= base_url('transaction/buy') ?>">Buy</a>
          <span class="sep">›</span>
          <span style="opacity:.9"><?= htmlspecialchars($kode ?: 'Material', ENT_QUOTES) ?></span>
        </div>
      </nav>

      <div class="ilv-head">
        <div>
          <h1>Buy — Pilih Mode</h1>
          <p>Pilih <strong>High</strong> atau <strong>Low</strong> untuk material
             <strong><?= htmlspecialchars($kode ?: '-', ENT_QUOTES) ?></strong>.</p>
        </div>

        <!-- Tombol kembali DI ATAS (gaya sama) -->
        <a href="<?= base_url('transaction/buy') ?>" class="ilv-back" aria-label="Kembali ke daftar material">
          <i class="<?= htmlspecialchars($iconBack, ENT_QUOTES) ?>"></i> Kembali
        </a>
      </div>
    </section>

    <?php if ($materialId > 0): ?>
      <!-- Tiles HIGH/LOW dengan style yang sama -->
      <section class="ilv-grid" aria-label="Pilih Mode Material">
        <a class="ilv-item" href="<?= base_url('transaction/buy/'.$materialId.'/?t=high') ?>" aria-label="High Material">
          <div class="ilv-thumb"><i class="fas fa-level-up-alt ico" aria-hidden="true"></i></div>
          <div class="ilv-caption">HIGH MATERIAL</div>
          <div class="ilv-sub">Kadar/grade tinggi sesuai rumus harga high.</div>
        </a>

        <a class="ilv-item" href="<?= base_url('transaction/buy/'.$materialId.'/?t=low') ?>" aria-label="Low Material">
          <div class="ilv-thumb"><i class="fas fa-level-down-alt ico" aria-hidden="true"></i></div>
          <div class="ilv-caption">LOW MATERIAL</div>
          <div class="ilv-sub">Kadar/grade rendah dengan penyesuaian potongan.</div>
        </a>
      </section>
    <?php else: ?>
      <div class="alert alert-warning mt-3">
        Material <strong><?= htmlspecialchars($kode ?: '-', ENT_QUOTES) ?></strong> tidak ditemukan.
        <a href="<?= base_url('transaction/buy') ?>" class="alert-link">Kembali ke daftar material</a>.
      </div>
    <?php endif; ?>

    <!-- Footer meta (letak di BAWAH seperti halaman Buy) -->
    <div class="ilv-meta">
      <span class="ilv-badge">Mode Transaksi — Buy</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>

  </div>
</div>
