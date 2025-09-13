<!-- Dashboard — iPadOS Inspired + Dynamic Background (I Love Emas) -->
<style>
  :root{
    --blue-light:#2970f5;
    --blue-dark:#0a1a3f;
    --blue-pastel:#bcdcff;
    --text:#0B0F1A;
    --muted:#5c6b85;
    --card-bg:rgba(255,255,255,.72);
    --card-br:rgba(255,255,255,.28);
    --shadow:0 8px 32px rgba(0,0,0,.14);
    --radius:22px;
    --topbar-h:72px;
  }

  /* ===== Sistem font ala Apple (SF Pro) ===== */
  html, body, .ilv-dash, .kpi-card, .ilv-card, .ilv-hero {
    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display",
                 "Helvetica Neue", Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
  }

  /* ===== Dynamic background image layer (DO NOT REMOVE) ===== */
  .page-bg{
    position:fixed; inset:0; z-index:-2;
    background: center / cover no-repeat var(--bg, transparent);
  }
  .page-bg::after{
    content:""; position:absolute; inset:0; z-index:-1;
    background:
      radial-gradient(1200px 600px at 50% 0%, rgba(10,26,63,.45), transparent 60%),
      linear-gradient(180deg, rgba(10,26,63,.38), rgba(10,26,63,.10) 40%, rgba(10,26,63,.55));
    pointer-events:none;
  }

  /* ===== Dashboard wrapper (PERBAIKAN SPACING) ===== */
  .ilv-dash{
    padding: clamp(16px, 2.5vw, 28px);
    /* Hapus margin-top & top padding ganda. Cukup satu padding-top yang menghormati topbar + sedikit ruang */
    padding-top: calc(var(--topbar-h) + 16px + env(safe-area-inset-top, 0px));
    padding-left: max(16px, env(safe-area-inset-left));
    padding-right: max(16px, env(safe-area-inset-right));
  }
  .ilv-container{ max-width:1100px; margin-inline:auto; }

  /* ===== Hero (glass / iPadOS) ===== */
  .ilv-hero{
    position:relative; overflow:hidden;
    border-radius:var(--radius);
    background:linear-gradient(160deg, rgba(10,26,63,.85), rgba(41,112,245,.70));
    color:#fff; padding:clamp(24px,4vw,36px);
    box-shadow:var(--shadow);
    backdrop-filter:blur(16px);
    -webkit-backdrop-filter:blur(16px);
  }
  .ilv-hero h1{ margin:0 0 8px; font-weight:800; font-size:clamp(22px,3vw,32px); letter-spacing:.2px; }
  .ilv-hero p{ margin:0; opacity:.96; font-size:clamp(14px,1.4vw,16px); }

  /* ===== KPI ===== */
  .kpi-row{ margin-top:20px; display:grid; gap:16px; grid-template-columns:1fr; }
  @media(min-width:640px){ .kpi-row{ grid-template-columns:repeat(2,1fr); } }

  .kpi-card{
    background:var(--card-bg); border:1px solid var(--card-br);
    border-radius:var(--radius); padding:18px;
    display:flex; align-items:center; gap:16px;
    box-shadow:var(--shadow);
    backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
  }
  .kpi-ico{
    width:54px; height:54px; border-radius:16px; display:grid; place-items:center;
    background:linear-gradient(135deg, var(--blue-pastel), #eef6ff);
    border:1px solid #d7e5ff;
  }
  .kpi-ico svg{ width:26px; height:26px; stroke:#074799; }
  .kpi-title{ margin:0; font-size:14px; color:#0a1a3f; }
  .kpi-value{ margin:4px 0 0; font-size:28px; font-weight:900; color:#ffffff; }
  .kpi-empty{ font-size:14px; color:#0a1a3f; }

  /* Skeleton */
  .kpi-card .skeleton{display:none;}
  .kpi-card.loading .skeleton{display:block;}
  .kpi-card.loading .kpi-value,.kpi-card.loading .kpi-empty{display:none!important;}
  .skeleton{
    height:24px;width:120px;border-radius:8px;
    background:linear-gradient(90deg,#cfe0ff 25%,#eaf2ff 37%,#cfe0ff 63%);
    background-size:400% 100%; animation:shimmer 1.2s infinite;
  }
  @keyframes shimmer{0%{background-position:100% 0;}100%{background-position:0 0;}}

  /* ===== Menu grid ===== */
  .ilv-grid{ margin-top:24px; display:grid; gap:18px; grid-template-columns:1fr; }
  @media(min-width:540px){ .ilv-grid{ grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); } }

  .ilv-card{
    display:flex; align-items:flex-start; gap:14px; text-decoration:none;
    background:var(--card-bg); border:1px solid var(--card-br); color:#fff;
    border-radius:var(--radius); padding:18px; box-shadow:var(--shadow);
    backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px);
    transition:transform .12s ease, box-shadow .2s ease;
  }
  @media(hover:hover){ .ilv-card:hover{ transform:translateY(-3px); box-shadow:0 14px 34px rgba(0,0,0,.18); } }
  .ilv-card:focus-visible{ outline:3px solid #8fd2ff; outline-offset:2px; }

  .ilv-ico{ flex:0 0 52px; height:52px; display:grid; place-items:center; border-radius:16px;
    background:linear-gradient(135deg,var(--blue-pastel),#f0f8ff); border:1px solid #d7e5ff; }
  .ilv-ico svg{ width:26px; height:26px; stroke:#074799; }
  .ilv-tit{ font-weight:700; margin:0; font-size:16px; color:#0a1a3f; }
  .ilv-sub{ margin:4px 0 0; font-size:14px; color:#0a1a3f; line-height:1.4; } /* lebih kontras di atas glass */

  .ilv-meta{
    margin-top:20px; display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between;
    font-size:13px; color:#d3e1ff;
  }
  .ilv-badge{
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.28);
    padding:6px 12px; border-radius:999px; color:#eaf2ff;
  }

  /* Overlay loader */
  .app-overlay{ position:fixed; inset:0; z-index:3000; background:rgba(0,0,0,.2);
    backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center;
    transition:opacity .25s ease; }
  .app-overlay.hidden{ opacity:0; visibility:hidden; pointer-events:none; }
  .loader{ display:flex; flex-direction:column; align-items:center; gap:12px;
    padding:20px 24px; border-radius:18px; background:rgba(255,255,255,.9); }
  .ring{ width:48px; height:48px; border-radius:50%;
    border:3px solid rgba(0,26,110,.25); border-top-color:var(--blue-dark);
    animation:spin 1s linear infinite; }
  @keyframes spin{ to{ transform:rotate(360deg); } }
  .loader-label{ font-size:14px; color:#0b1f4f; font-weight:700; }
</style>

<!-- Dynamic background image (ambil dari config/variabel, fallback ke aset lokal) -->
<div class="page-bg"
     style="--bg:url('<?= !empty($config['dashboard_bg']) ? $config['dashboard_bg'] : base_url('assets/images/bg-dashboard.jpg') ?>')">
</div>

<main class="ilv-dash" role="main">
  <div class="ilv-container">
    <section class="ilv-hero" aria-label="Ringkasan Dashboard">
      <div>
        <h1>Dashboard</h1>
        <p>
          Selamat datang<?= isset($userData[0]->u_name) ? ', <strong>'.$userData[0]->u_name.'</strong>' : '';?>.
          Pilih menu di bawah untuk mulai bekerja.
        </p>
      </div>

      <!-- KPI -->
      <div class="kpi-row">
        <div class="kpi-card loading" id="kpiTx">
          <span class="kpi-ico" aria-hidden="true">
            <!-- ikon transaksi -->
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M8 2h8l2 2v16l-2 2H8l-2-2V4z"/><path d="M9 7h6M9 11h6M9 15h6"/>
            </svg>
          </span>
          <div>
            <p class="kpi-title">Transaksi Hari Ini</p>
            <div class="skeleton" aria-hidden="true"></div>
            <div class="kpi-value" aria-live="polite"></div>
            <div class="kpi-empty">Data belum tersedia</div>
          </div>
        </div>

        <div class="kpi-card loading" id="kpiCust">
          <span class="kpi-ico" aria-hidden="true">
            <!-- ikon customer -->
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="7" r="4"/>
              <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>
            </svg>
          </span>
          <div>
            <p class="kpi-title">Total Customer</p>
            <div class="skeleton" aria-hidden="true"></div>
            <div class="kpi-value" aria-live="polite"></div>
            <div class="kpi-empty">Data belum tersedia</div>
          </div>
        </div>
      </div>

      <!-- Menu -->
      <nav class="ilv-grid" aria-label="Menu utama">
        <a class="ilv-card" href="<?= base_url('transaction-list') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7 7h14l-4-4"/><path d="M17 17H3l4 4"/>
              <path d="M21 7v6" opacity=".4"/><path d="M3 11v6" opacity=".4"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Transaction</h3><p class="ilv-sub">Kelola transaksi harian & proses operasional.</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('archive') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 7h5l2 3h11v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <path d="M3 7V5a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v3" opacity=".4"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Archive</h3><p class="ilv-sub">Akses arsip dokumen & histori transaksi.</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('master') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <ellipse cx="12" cy="5" rx="9" ry="3"/>
              <path d="M3 5v6c0 1.7 4 3 9 3s9-1.3 9-3V5"/>
              <path d="M3 11v6c0 1.7 4 3 9 3s9-1.3 9-3v-6" opacity=".4"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Master Data</h3><p class="ilv-sub">Atur data master (user, cabang, dsb.).</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('report') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 3v18h18"/><rect x="6" y="13" width="3" height="5"/>
              <rect x="11" y="9" width="3" height="9"/><rect x="16" y="5" width="3" height="13"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Report</h3><p class="ilv-sub">Lihat ringkasan & analitik performa.</p></span>
        </a>

        <?php if (!empty($userData) && strtolower($userData[0]->u_rule ?? '') === 'administrator'): ?>
        <a class="ilv-card" href="<?= base_url('config') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Konfigurasi</h3><p class="ilv-sub">Kelola logo, warna, nama & versi aplikasi.</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('maintenance/truncate') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
              <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Data Maintenance</h3><p class="ilv-sub">Truncate data transaksi & customer.</p></span>
        </a>
        <?php endif; ?>
      </nav>
    </section>

    <div class="ilv-meta">
      <span class="ilv-badge">Versioning aplikasi — v3.0.0</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>
  </div>
</main>

<!-- Overlay while loading KPI -->
<div id="dashLoading" class="app-overlay" aria-live="polite" aria-busy="true">
  <div class="loader">
    <div class="ring" aria-hidden="true"></div>
    <div class="loader-label">Memuat dashboard…</div>
  </div>
</div>

<script>
(function(){
  const endpoint = "<?= base_url('api/kpi-dashboard') ?>";
  const elTx   = document.getElementById('kpiTx');
  const elCust = document.getElementById('kpiCust');
  const overlay = document.getElementById('dashLoading');
  const nf = new Intl.NumberFormat('id-ID');

  function finishLoading(node){
    node.classList.remove('loading');
    const sk = node.querySelector('.skeleton'); if (sk) sk.style.display='none';
  }
  function setKPI(node, value){
    finishLoading(node);
    const v = node.querySelector('.kpi-value');
    const e = node.querySelector('.kpi-empty');
    const num = Number.isFinite(value) ? value : 0;
    v.textContent = nf.format(num);
    v.style.display = 'block';
    e.style.display = (num > 0) ? 'none' : 'block';
  }
  function setError(node){
    finishLoading(node);
    node.querySelector('.kpi-value').style.display='none';
    const e = node.querySelector('.kpi-empty'); e.textContent='Gagal memuat'; e.style.display='block';
  }
  function hideOverlay(){ overlay.classList.add('hidden'); overlay.setAttribute('aria-busy','false'); }

  fetch(endpoint, { headers:{ 'X-Requested-With':'XMLHttpRequest' } })
    .then(r => r.ok ? r.json() : Promise.reject(r))
    .then(d => {
      setKPI(elTx,   Number(d.tx_today || 0));
      setKPI(elCust, Number(d.customer_total || 0));
    })
    .catch(() => { setError(elTx); setError(elCust); })
    .finally(hideOverlay);
})();
</script>
