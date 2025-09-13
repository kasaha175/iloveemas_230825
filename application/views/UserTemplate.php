<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Guard + fallback config */
$cfg            = isset($config) && is_array($config) ? $config : [];
$appName        = $cfg['app_name']        ?? 'I Love Emas';
$appVersion     = $cfg['app_version']     ?? '3.0.0';
$colorPrimary   = $cfg['color_primary']   ?? '#074799';
$colorSecondary = $cfg['color_secondary'] ?? '#001A6E';
$colorPastel    = $cfg['color_pastel']    ?? '#B1F0F7';

$logoPath       = !empty($cfg['logo'])
                    ? base_url($cfg['logo'])
                    : base_url('assets/offline/icon-ilovemas.png');

$faviconPath    = !empty($cfg['favicon'])
                    ? base_url($cfg['favicon'])
                    : base_url('assets/img/favicon.png');

$bgDashboardUrl = !empty($cfg['bg_dashboard'])
                    ? base_url($cfg['bg_dashboard'])
                    : base_url('assets/offline/bg-black.jpg');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?= html_escape($appName) ?>">
  <title><?= isset($title) && $title ? html_escape($title).' — ' : '' ?><?= html_escape($appName) ?></title>

  <!-- Favicon (konfigurasi) -->
  <link rel="icon" href="<?= $faviconPath ?>" type="image/png">

  <!-- Core CSS -->
  <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900&display=swap" rel="stylesheet">
  <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/select2/css/select2.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/summernote/summernote.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/keyboard/docs/css/jquery-ui.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/keyboard/css/keyboard.css') ?>" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">

  <!-- Theme variables + fix z-index -->
  <style>
    :root{
      --blue-light:  <?= $colorPrimary ?>;
      --blue-dark:   <?= $colorSecondary ?>;
      --blue-pastel: <?= $colorPastel ?>;
      --topbar-h:    72px;
    }

    body{
      font-family: 'Poppins','Nunito','Segoe UI',Arial,Helvetica,sans-serif;
      background-image: url('<?= $bgDashboardUrl ?>');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      min-height: 100vh;
      position: relative;
    }
    /* Overlay latar di belakang, non-interaktif */
    body::before{
      content:"";
      position:absolute; inset:0;
      /* Jika ingin gelap: background: rgba(0,0,0,.35); */
      z-index:-1 !important;
      pointer-events:none;
    }

    /* Navbar/Dropdown di atas konten */
    .navbar.topbar { position: relative; z-index: 1050 !important; }
    .dropdown-menu { z-index: 1060 !important; }
    .page-wrap     { position: relative; z-index: 1; 
      /* padding-top: calc(var(--topbar-h) + 16px);  */
    }

    /* Topbar pakai warna konfigurasi */
    .topbar{
      background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    }
    .brand-logo{
      max-width:200px; height:auto;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,.15));
    }
    .img-profile{ object-fit:cover; }

    /* Komponen kecil */
    .select2{ width:100% !important; }

    /* Pastikan modal selalu di atas topbar & dropdown */
    /* modal harus di atas apapun */
    .modal-backdrop { z-index: 1190 !important; }
    .modal          { z-index: 1200 !important; }

    /* saat modal terbuka, jangan buat .page-wrap jadi konteks z-index yang menghalangi */
    body.modal-open .page-wrap { z-index: auto !important; }

    /* opsional: turunkan topbar sedikit saat modal terbuka (biar pasti di bawah backdrop) */
    body.modal-open .navbar.topbar { z-index: 1000 !important; }

    /* Internet Indicator */
    .net-indicator{display:inline-flex;align-items:center;gap:6px;font-size:12px;user-select:none}
    .net-indicator .bars{display:inline-flex;align-items:flex-end;gap:2px;width:18px;height:12px}
    .net-indicator .bar{width:3px;background:#d0d7e2;border-radius:1px;transition:height .2s,background .2s}
    .net-indicator .bar:nth-child(1){height:20%}
    .net-indicator .bar:nth-child(2){height:45%}
    .net-indicator .bar:nth-child(3){height:70%}
    .net-indicator .bar:nth-child(4){height:100%}
    .net-indicator[data-quality="0"] .bar{background:#d0d7e2}
    .net-indicator[data-quality="1"] .bar:nth-child(-n+1){background:#f59e0b}
    .net-indicator[data-quality="2"] .bar:nth-child(-n+2){background:#f59e0b}
    .net-indicator[data-quality="3"] .bar:nth-child(-n+3){background:#10b981}
    .net-indicator[data-quality="4"] .bar:nth-child(-n+4){background:#10b981}
    .net-indicator.offline .bar{background:#ef4444!important}
    .net-indicator .dot{width:8px;height:8px;border-radius:50%;background:#ef4444;box-shadow:0 0 0 2px rgba(0,0,0,.04) inset}
    .net-indicator.online .dot{background:#10b981}
    .net-indicator.degraded .dot{background:#f59e0b}
    .net-indicator .label{opacity:.75}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand navbar-light topbar fixed-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
      <i class="fa fa-bars"></i>
    </button>

    <a href="<?= base_url('dashboard') ?>" class="d-flex align-items-center text-decoration-none">
      <img src="<?= $logoPath ?>" alt="<?= html_escape($appName) ?>" class="brand-logo">
    </a>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item d-none d-sm-flex align-items-center pr-2" style="color:#000;">
        <small class="font-weight-600"><?= html_escape($appName) ?> — v<?= html_escape($appVersion) ?></small>
      </li>

      <!-- Internet indicator (global, bukan backend) -->
      <li class="nav-item d-flex align-items-center px-2">
        <span id="internetIndicator" class="net-indicator offline" data-quality="0" title="Offline">
          <span class="bars" aria-hidden="true">
            <span class="bar"></span><span class="bar"></span><span class="bar"></span><span class="bar"></span>
          </span>
          <span class="dot" aria-hidden="true"></span>
          <span class="label d-none d-md-inline">Internet • Offline</span>
        </span>
      </li>

      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="mr-2 d-none d-lg-inline small" style="color:#000;">
            <?= isset($userData[0]->u_name) ? html_escape($userData[0]->u_name) : 'User' ?>
          </span>
          <img class="img-profile rounded-circle" src="<?= base_url('assets/img/user/logo.png') ?>" width="36" height="36" alt="avatar">
        </a>

        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
          <!-- Logout: confirm() + POST -->
          <a href="#" class="dropdown-item js-logout">
            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Hidden logout form (POST + CSRF) -->
  <form id="logoutForm" action="<?= base_url('logout-process') ?>" method="post" style="display:none;">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
           value="<?= $this->security->get_csrf_hash(); ?>">
  </form>

  <!-- Konten halaman -->
  <main class="page-wrap">
    <div class="container-fluid">
      <?= isset($content) ? $content : '' ?>
    </div>
  </main>

  <!-- Core JS -->
  <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
  <script src="<?= base_url('assets/select2/js/select2.min.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

  <!-- jqKeyboard -->
  <script src="<?= base_url('assets/keyboard/docs/js/jquery-ui.min.js') ?>"></script>
  <script src="<?= base_url('assets/keyboard/js/jquery.keyboard.js') ?>"></script>
  <script src="<?= base_url('assets/keyboard/js/jquery.keyboard.extension-typing.js') ?>"></script>
  <script src="<?= base_url('assets/keyboard/js/jquery.keyboard.extension-autocomplete.js') ?>"></script>
  <script src="<?= base_url('assets/keyboard/js/jquery.keyboard.extension-caret.js') ?>"></script>

  <!-- Plugins -->
  <script src="<?= base_url('assets/offline/sweetalert2.all.js') ?>"></script>
  <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>
  <script src="<?= base_url('assets/summernote/summernote.min.js') ?>"></script>
  <script src="<?= base_url('assets/offline/dataTables.buttons.min.js') ?>"></script>
  <script src="<?= base_url('assets/offline/jszip.min.js') ?>"></script>
  <script src="<?= base_url('assets/offline/buttons.html5.min.js') ?>"></script>
  <script src="<?= base_url('assets/offline/buttons.print.min.js') ?>"></script>

  <script>
    $(function () {
      // Select2
      

      // Summernote (jika ada)
      $('.summernote').summernote({
        height: 200,
        toolbar: [
          ['style', ['bold','italic','underline','clear']],
          ['font', ['strikethrough','superscript','subscript']],
          ['para', ['ul','ol','paragraph']],
          ['insert', ['link','picture','video']],
          ['misc', ['fullscreen','codeview','help']]
        ]
      });

      // jqKeyboard (opsional)
      if ($.keyboard) {
        try { $('.use-keyboard').keyboard(); } catch(e){}
      }
    });

    // Logout via confirm() + POST
    $(document).on('click', '.js-logout', function (e) {
      e.preventDefault();
      // Tutup dropdown (kosmetik)
      $(this).closest('.dropdown-menu').removeClass('show');
      if (window.confirm('Anda yakin ingin keluar dari sesi saat ini?')) {
        document.getElementById('logoutForm').submit();
      }
    });

  (function(){
  const el = document.getElementById('internetIndicator');
  if(!el) return;
  const label = el.querySelector('.label');

  // Endpoint gambar ringan (tidak butuh CORS utk <img>)
  const PROBES = [
    'https://www.google.com/favicon.ico',
    'https://www.cloudflare.com/favicon.ico'
  ];

  // Konfigurasi
  const INTERVAL = 5000;   // cek tiap 5 dtk
  const TIMEOUT  = 4000;   // timeout 4 dtk
  const WINDOW_N = 3;      // moving average

  // Ambang kualitas (ms)
  // <=150: 4 (Sangat Baik), <=400:3 (Baik), <=800:2 (Cukup), <=1200:1 (Buruk), >1200:0
  const LTH = [1200, 800, 400, 150];

  let samples = [];
  let ticker = null;

  // Network Information API (opsional)
  function getConn(){
    return (navigator.connection || navigator.mozConnection || navigator.webkitConnection) || null;
  }
  function getEff(){
    const c = getConn();
    return c && c.effectiveType ? c.effectiveType : null; // '4g','3g',...
  }
  function getDownlink(){
    const c = getConn();
    return c && typeof c.downlink === 'number' ? c.downlink : null; // Mbps (approx)
  }

  function qualityFromLatency(ms){
    if (ms <= LTH[3]) return 4;
    if (ms <= LTH[2]) return 3;
    if (ms <= LTH[1]) return 2;
    if (ms <= LTH[0]) return 1;
    return 0;
  }
  const avg = () => samples.length ? Math.round(samples.reduce((a,b)=>a+b,0)/samples.length) : null;

  function setState({online, quality, latency, effType, downlink}){
    el.classList.toggle('offline', !online);
    el.classList.toggle('online',  online && quality >= 3);
    el.classList.toggle('degraded', online && quality < 3);
    el.setAttribute('data-quality', String(online ? quality : 0));

    if (!online){
      if(label) label.textContent = 'Internet • Offline';
      el.title = 'Internet tidak terhubung';
      return;
    }

    const grades = ['Sangat Buruk','Buruk','Cukup','Baik','Sangat Baik'];
    const grade  = grades[quality] || grades[0];

    const latTxt = latency != null ? `${latency} ms` : 'n/a';
    const effTxt = effType ? ` • ${effType.toUpperCase()}` : '';
    const dlTxt  = (typeof downlink === 'number') ? ` • ~${downlink.toFixed(1)} Mbps` : '';

    if(label) label.textContent = `Internet • ${grade}`;
    el.title = `Internet • Latency: ${latTxt}${effTxt}${dlTxt}`;
  }

  // ---- Image ping (tanpa CORS) ----
  function pingImage(url, timeoutMs = TIMEOUT){
    return new Promise((resolve, reject) => {
      const start = performance.now();
      const img = new Image();
      let done = false;

      const finish = (ok) => {
        if (done) return;
        done = true;
        clearTimeout(tmr);
        img.onload = img.onerror = null;
        if (ok) resolve(Math.round(performance.now() - start));
        else reject(new Error('img-error'));
      };

      // timeout
      const tmr = setTimeout(() => finish(false), timeoutMs);

      img.onload  = () => finish(true);
      img.onerror = () => finish(false);

      // cache buster
      img.src = url + (url.includes('?') ? '&' : '?') + 'cb=' + Date.now();
    });
  }

  // Ambil hasil tercepat dari beberapa host (first success)
  function probeLatency(){
    const tasks = PROBES.map(u => pingImage(u));
    if (Promise.any) return Promise.any(tasks);
    // fallback bila browser lama: first-resolve
    return new Promise((resolve, reject) => {
      let rejects = 0;
      tasks.forEach(p => p.then(resolve).catch(()=>{ if(++rejects===tasks.length) reject(new Error('all-failed')); }));
    });
  }

  function runProbe(){
    if(!navigator.onLine){
      samples = [];
      setState({online:false});
      return;
    }

    probeLatency()
      .then(ms=>{
        samples.push(ms);
        if(samples.length > WINDOW_N) samples.shift();
        const latency = avg();
        const effType = getEff();
        const down    = getDownlink();

        let q = qualityFromLatency(latency);
        if (effType && /^(2g|slow-2g)$/i.test(effType)) q = Math.min(q, 1);
        if (effType && /^3g$/i.test(effType))           q = Math.min(q, 2);

        setState({online:true, quality:q, latency, effType, downlink:down});
      })
      .catch(()=>{
        samples = [];
        setState({online:false});
      });
  }

  // listeners
  window.addEventListener('online',  runProbe);
  window.addEventListener('offline', ()=>setState({online:false}));
  const c = getConn();
  if (c && c.addEventListener) c.addEventListener('change', runProbe);

  // batasi polling saat tab tidak aktif (hemat & stabil)
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      if (ticker) { clearInterval(ticker); ticker = null; }
    } else {
      runProbe();
      if (!ticker) ticker = setInterval(runProbe, INTERVAL);
    }
  });

  // start
  runProbe();
  ticker = setInterval(runProbe, INTERVAL);
})();
  </script>
</body>
</html>
