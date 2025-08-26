<!-- Dashboard — modern, responsive, aesthetic (I Love Emas) -->
<style>
  :root{
    --blue-light:#074799;   /* biru muda */
    --blue-dark:#001A6E;    /* biru tua  */
    --blue-pastel:#B1F0F7;  /* biru pastel */
    --text:#0B0F1A;
    --muted:#6b7a99;
    --card-bg:#ffffff;
    --card-br:#e6eefc;
    --shadow:0 18px 30px rgba(0,0,0,.10);
    --radius:18px;
    --topbar-h:72px;        /* kira-kira tinggi navbar fixed-top di template */
  }

  /* scope ke dashboard agar tidak bentrok dengan sb-admin */
  .ilv-dash{ padding:clamp(16px,2.2vw,28px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1200px; margin-inline:auto; }

  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(20px,3.5vw,32px);
    box-shadow: var(--shadow);
  }
  .ilv-hero h1{ margin:0 0 6px; font-weight:700; letter-spacing:.3px; font-size:clamp(20px,3.2vw,30px); }
  .ilv-hero p{ margin:0; opacity:.95; font-size:clamp(13px,1.3vw,15px); }

  .ilv-hero .blob{position:absolute; border-radius:50%; filter:blur(44px); opacity:.25; pointer-events:none;}
  .ilv-hero .b1{ width:380px;height:380px;background:var(--blue-pastel);right:-120px;top:-120px;}
  .ilv-hero .b2{ width:240px;height:240px;background:#8fd2ff;left:-80px;bottom:-100px;opacity:.2;}

  /* KPI row */
  .kpi-row{
    margin-top:14px;
    display:grid; grid-template-columns:1fr; gap:12px;
  }
  @media (min-width:760px){ .kpi-row{ grid-template-columns:repeat(2,1fr); } }

  .kpi-card{
    background:#fff; border:1px solid var(--card-br); border-radius:16px;
    padding:14px 16px; display:flex; align-items:center; gap:14px;
    box-shadow:0 10px 20px rgba(0,0,0,.06);
  }
  .kpi-ico{
    width:42px; height:42px; border-radius:12px; display:grid; place-items:center;
    background:linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border:1px solid #d7f4ff;
  }
  .kpi-title{ margin:0; font-size:12px; color:#6b7a99; }
  .kpi-value{ margin:2px 0 0; font-size:26px; line-height:1; font-weight:800; color:#001A6E; }
  .kpi-empty{ font-size:13px; color:#6b7a99; }

  /* Skeleton / shimmer loader */
  .kpi-card .skeleton{ display:none; }                  /* default: tersembunyi */
  .kpi-card.loading .skeleton{ display:block; }         /* tampil hanya saat loading */
  .kpi-card.loading .kpi-value,
  .kpi-card.loading .kpi-empty{ display:none !important; }
  .skeleton{
    height:24px; width:120px; border-radius:8px;
    background: linear-gradient(90deg,#edf3ff 25%,#f7fbff 37%,#edf3ff 63%);
    background-size:400% 100%;
    animation: shimmer 1.2s ease-in-out infinite;
  }
  @keyframes shimmer{ 0%{background-position:100% 0;} 100%{background-position:0 0;} }

  /* Menu grid */
  .ilv-grid{
    margin-top: clamp(18px, 2.5vw, 24px);
    display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px;
  }
  .ilv-card{
    display:flex; align-items:flex-start; gap:14px; text-decoration:none;
    background:var(--card-bg); border:1px solid var(--card-br); color:var(--text);
    border-radius:var(--radius); padding:16px; box-shadow:0 6px 14px rgba(0,0,0,.06);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
  }
  .ilv-card:hover{ transform: translateY(-2px); box-shadow:0 14px 32px rgba(0,0,0,.12); border-color:#d7e5ff; background:#fff; }
  .ilv-ico{
    flex:0 0 48px; height:48px; display:grid; place-items:center; border-radius:14px;
    background:linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border:1px solid #d7f4ff;
  }
  .ilv-ico svg{width:22px;height:22px;}
  .ilv-tit{font-weight:600; margin:0; font-size:15px;}
  .ilv-sub{margin:4px 0 0; font-size:13px; color:var(--muted); line-height:1.35;}

  .ilv-meta{
    margin-top:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;
    font-size:12px; color:#26406e;
  }
  .ilv-badge{
    background:#ffffff; color:#0e2b68; border:1px solid #dfeaff; padding:6px 10px; border-radius:999px;
  }

  /* kecilkan spasi di layar sangat kecil */
  @media (max-width:480px){
    .ilv-hero{ padding:16px; }
    .ilv-card{ padding:14px; }
  }
</style>

<div class="ilv-dash">
  <div class="ilv-container">
    <section class="ilv-hero" aria-label="Ringkasan Dashboard">
      <div>
        <h1>Dashboard</h1>
        <p>
          Selamat datang<?= isset($userData[0]->u_name) ? ', <strong>'.$userData[0]->u_name.'</strong>' : '';?>.
          Pilih menu di bawah untuk mulai bekerja.
        </p>
      </div>

      <!-- KPI row (async) -->
      <div class="kpi-row">
        <!-- Transaksi Hari Ini -->
        <div class="kpi-card loading" id="kpiTx">
          <span class="kpi-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M8 2h8l2 2v16l-2 2H8l-2-2V4l2-2z"/><path d="M9 7h6M9 11h6M9 15h6"/>
            </svg>
          </span>
          <div>
            <p class="kpi-title">Transaksi Hari Ini</p>
            <div class="skeleton" aria-hidden="true"></div>
            <div class="kpi-value" aria-live="polite"></div>
            <div class="kpi-empty">Data belum tersedia</div>
          </div>
        </div>

        <!-- Total Customer -->
        <div class="kpi-card loading" id="kpiCust">
          <span class="kpi-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
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
            <svg viewBox="0 0 24 24" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7 7h14l-4-4"/><path d="M17 17H3l4 4"/><path d="M21 7v6" opacity=".4"/><path d="M3 11v6" opacity=".4"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Transaction</h3><p class="ilv-sub">Kelola transaksi harian & proses operasional.</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('archive') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 7h5l2 3h11v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 7V5a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v3" opacity=".4"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Archive</h3><p class="ilv-sub">Akses arsip dokumen & histori transaksi.</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('master') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v6c0 1.7 4 3 9 3s9-1.3 9-3V5"/><path d="M3 11v6c0 1.7 4 3 9 3s9-1.3 9-3v-6" opacity=".4"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Master Data</h3><p class="ilv-sub">Atur data master (user, cabang, dsb.).</p></span>
        </a>

        <a class="ilv-card" href="<?= base_url('report') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 3v18h18"/><rect x="6" y="13" width="3" height="5"/><rect x="11" y="9" width="3" height="9"/><rect x="16" y="5" width="3" height="13"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Report</h3><p class="ilv-sub">Lihat ringkasan & analitik performa.</p></span>
        </a>

        <?php if (!empty($userData) && strtolower($userData[0]->u_rule ?? '') === 'administrator'): ?>
        <a class="ilv-card" href="<?= base_url('config') ?>">
          <span class="ilv-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="#074799" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09c-.18 0-.35.04-.51.1-.6.25-.99.85-.99 1.51z"/>
            </svg>
          </span>
          <span><h3 class="ilv-tit">Konfigurasi</h3><p class="ilv-sub">Kelola logo, warna, nama & versi aplikasi.</p></span>
        </a>
        <?php endif; ?>
      </nav>

      <!-- decorative blobs -->
      <span class="blob b1"></span>
      <span class="blob b2"></span>
    </section>

    <div class="ilv-meta">
      <span class="ilv-badge">Versioning aplikasi — v3.0.0</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>
  </div>
</div>

<script>
(function(){
  const endpoint = "<?= base_url('api/kpi-dashboard') ?>";
  const elTx   = document.getElementById('kpiTx');
  const elCust = document.getElementById('kpiCust');

  function finishLoading(node){
    node.classList.remove('loading');                 // lepas state loading
    const sk = node.querySelector('.skeleton');       // sembunyikan skeleton (jaga-jaga)
    if (sk) sk.style.display = 'none';
  }

  function setKPI(node, value){
    finishLoading(node);
    const v = node.querySelector('.kpi-value');
    const e = node.querySelector('.kpi-empty');

    if (typeof value === 'number' && value > 0){
      v.textContent = value;
      v.style.display = 'block';
      e.style.display = 'none';
    } else {
      v.style.display = 'none';
      e.textContent = 'Data belum tersedia';
      e.style.display = 'block';
    }
  }

  function setError(node){
    finishLoading(node);
    const v = node.querySelector('.kpi-value');
    const e = node.querySelector('.kpi-empty');
    v.style.display = 'none';
    e.textContent = 'Gagal memuat';
    e.style.display = 'block';
  }

  fetch(endpoint, { headers:{ 'X-Requested-With':'XMLHttpRequest' } })
    .then(r => r.ok ? r.json() : Promise.reject(r))
    .then(d => {
      setKPI(elTx,   Number(d.tx_today || 0));
      setKPI(elCust, Number(d.customer_total || 0));
    })
    .catch(() => {
      setError(elTx);
      setError(elCust);
    });
})();
</script>
