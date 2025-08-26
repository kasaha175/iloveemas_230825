<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$cfg            = isset($config) && is_array($config) ? $config : [];
$appName        = $cfg['app_name']       ?? 'I Love Emas';
$appVersion     = $cfg['app_version']    ?? '3.0.0';
$colorPrimary   = $cfg['color_primary']  ?? '#074799';
$colorSecondary = $cfg['color_secondary']?? '#001A6E';
$colorPastel    = $cfg['color_pastel']   ?? '#B1F0F7';

$logoPath       = !empty($cfg['logo'])    ? base_url($cfg['logo'])    : base_url('assets/img/logo.png');
$faviconPath    = !empty($cfg['favicon']) ? base_url($cfg['favicon']) : base_url('assets/img/favicon.png');
$bgLoginUrl     = !empty($cfg['bg_login'])? base_url($cfg['bg_login']): base_url('assets/img/back.jpg');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($appName) ?> — Login</title>

  <link rel="icon" href="<?= $faviconPath ?>" type="image/png"/>

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --blue-light: <?= $colorPrimary ?>;
      --blue-dark:  <?= $colorSecondary ?>;
      --blue-pastel:<?= $colorPastel ?>;
      --text:#0B0F1A; --text-invert:#fff;
      --card-bg:rgba(255,255,255,.14); --card-border:rgba(255,255,255,.25);
      --shadow:0 20px 40px rgba(0,0,0,.18); --radius:20px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);
      background:radial-gradient(1200px 800px at 10% 10%,var(--blue-pastel) 0%,#e8fbff 35%,#f6fbff 55%,#fbfdff 70%,#fff 100%);
      overflow:hidden}
    .auth-page{display:grid;grid-template-columns:1.1fr .9fr;min-height:100vh;position:relative}
    @media (max-width:1024px){.auth-page{grid-template-columns:1fr} body{overflow:auto}}
    .hero{position:relative;padding:clamp(24px,4vw,48px);display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;
      background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));color:var(--text-invert)}
    .hero::before{content:"";position:absolute;inset:0;opacity:.18;background:url('<?= $bgLoginUrl ?>') center/cover no-repeat fixed}
    .hero-top{display:flex;align-items:center;gap:16px;position:relative;z-index:2}
    .hero-top img{height:48px;width:auto;filter:drop-shadow(0 6px 12px rgba(0,0,0,.25))}
    .hero-content{position:relative;z-index:2;max-width:640px;margin-top:clamp(24px,6vw,64px)}
    .hero h1{font-size:clamp(28px,4.2vw,48px);line-height:1.1;margin:0 0 12px;font-weight:700}
    .hero p{font-size:clamp(14px,1.4vw,16px);opacity:.92;margin:0}
    .blob{position:absolute;border-radius:50%;filter:blur(40px);opacity:.35}
    .blob.one{width:380px;height:380px;background:var(--blue-pastel);right:-80px;top:-80px}
    .blob.two{width:280px;height:280px;background:#7ec7ff;left:-60px;bottom:-60px;opacity:.28}

    .panel{display:flex;align-items:center;justify-content:center;position:relative;padding:clamp(24px,4vw,48px)}
    .auth-card{width:100%;max-width:430px;padding:clamp(20px,3.4vw,36px);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
      background:var(--card-bg);border:1px solid var(--card-border);border-radius:var(--radius);box-shadow:var(--shadow)}
    .auth-header{text-align:center;margin-bottom:18px}
    .auth-logo{height:46px;margin-bottom:10px}
    .auth-title{margin:0;font-size:22px;font-weight:600;color:#0E204A}
    .auth-sub{margin:6px 0 0;font-size:13px;opacity:.75}

    .field{position:relative;margin-top:16px}
    .input{width:100%;padding:14px 44px 14px 14px;border-radius:12px;border:1px solid #dfe7ff;background:#fff;
      outline:none;font-size:15px;transition:border .2s,box-shadow .2s}
    .input:focus{border-color:var(--blue-light);box-shadow:0 0 0 4px rgba(7,71,153,.12)}
    .floating-label{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:14px;color:#6c7a99;pointer-events:none;
      transition:all .15s ease;background:transparent;padding:0 4px}
    .input:focus + .floating-label,.input:not(:placeholder-shown) + .floating-label{top:0;transform:translateY(-50%) scale(.88);
      color:var(--blue-light);background:#fff}
    .suffix-btn{position:absolute;right:8px;top:50%;transform:translateY(-50%);border:none;background:transparent;padding:6px 10px;
      border-radius:10px;cursor:pointer}
    .suffix-btn:focus{outline:2px solid rgba(7,71,153,.25)}
    .actions{display:flex;align-items:center;justify-content:space-between;margin-top:14px;gap:10px;margin-bottom:14px}
    .remember{display:flex;align-items:center;gap:8px;font-size:13px}

    /* Button + spinner */
    .btn{appearance:none;border:none;cursor:pointer;width:100%;border-radius:12px;padding:12px 16px;font-weight:600;font-size:15px;
      letter-spacing:.2px;color:#fff;background:linear-gradient(135deg,var(--blue-light),var(--blue-dark));
      box-shadow:0 10px 20px rgba(0,26,110,.25);transition:transform .06s ease,box-shadow .2s ease;display:inline-flex;
      align-items:center;justify-content:center;gap:10px}
    .btn:hover{box-shadow:0 14px 28px rgba(0,26,110,.28)}
    .btn:active{transform:translateY(1px)}
    .btn[disabled]{opacity:.8;cursor:not-allowed}
    .spinner{width:16px;height:16px;border-radius:50%;border:2px solid rgba(255,255,255,.5);border-top-color:#fff;
      animation:spin 1s linear infinite;display:none}
    .is-loading .spinner{display:inline-block}
    @keyframes spin{to{transform:rotate(360deg)}}

    .meta{margin-top:16px;text-align:center;font-size:12px;color:#5c6a92}
    .alert{display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;margin-top:14px;
      background:#fff4f4;border:1px solid #ffd7d7;color:#9a1a1a}
    .hero-footer{position:relative;z-index:2;font-size:12px;opacity:.85}
    @media (max-width:1024px){.hero{min-height:38vh}}
    @media (max-width:560px){.auth-card{background:#fff}}
  </style>
</head>
<body>
  <main class="auth-page">
    <section class="hero" aria-label="Brand">
      <div class="hero-top"><img src="<?= $logoPath ?>" alt="<?= html_escape($appName) ?>" /></div>
      <div class="hero-content">
        <h1>Selamat datang</h1>
        <p>Akses panel Anda dengan aman. Desain baru ini responsif untuk semua perangkat.</p>
      </div>
      <div class="hero-footer">© <?= date('Y') ?> — <?= html_escape($appName) ?></div>
      <span class="blob one"></span><span class="blob two"></span>
    </section>

    <section class="panel" aria-label="Login form">
      <div class="auth-card" role="dialog" aria-labelledby="loginTitle" aria-describedby="loginDesc">
        <header class="auth-header">
          <img src="<?= $logoPath ?>" alt="<?= html_escape($appName) ?>" class="auth-logo" />
          <h2 id="loginTitle" class="auth-title">Masuk ke Akun</h2>
          <p id="loginDesc" class="auth-sub">Silakan masukkan kredensial Anda</p>
        </header>

        <form id="loginForm" action="<?= base_url('login-process') ?>" method="post" novalidate>
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="field">
            <input id="username" name="username" class="input" type="text" placeholder=" " autocomplete="username" required aria-required="true" />
            <label for="username" class="floating-label">Username</label>
          </div>
          <div class="field">
            <input id="password" name="password" class="input" type="password" placeholder=" " autocomplete="current-password" required aria-required="true" />
            <label for="password" class="floating-label">Password</label>
            <button class="suffix-btn" type="button" id="togglePw" aria-label="Tampilkan/Sembunyikan password">👁️</button>
          </div>

          <div class="actions">
            <label class="remember"><input type="checkbox" name="remember" value="1" /> Ingat saya</label>
          </div>

          <!-- Tombol dengan spinner -->
          <button id="btnLogin" class="btn" type="submit" aria-live="polite" aria-busy="false">
            <span class="spinner" aria-hidden="true"></span>
            <span class="btn-label">Masuk</span>
          </button>
        </form>

        <?php if ($this->session->userdata('failedLogin') === true): ?>
          <div class="alert" role="alert" aria-live="polite">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 9v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>Username atau password salah.</span>
          </div>
          <?php $this->session->set_userdata(['failedLogin' => false]); ?>
        <?php endif; ?>

        <div class="meta"><?= html_escape($appName) ?> — v<?= html_escape($appVersion) ?></div>
      </div>
    </section>
  </main>

  <script>
    // toggle password
    (function(){
      var btn = document.getElementById('togglePw');
      var pw  = document.getElementById('password');
      if(btn && pw){
        btn.addEventListener('click', function(){
          pw.type = (pw.type === 'password') ? 'text' : 'password';
        });
      }
    })();

    // disable + spinner on submit
    (function(){
      var form = document.getElementById('loginForm');
      var btn  = document.getElementById('btnLogin');
      if(!form || !btn) return;

      form.addEventListener('submit', function(e){
        // Jika ingin pakai validasi HTML5, hapus 'novalidate' di <form> dan aktifkan cek berikut:
        // if (!form.checkValidity()) { return; }
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy','true');
        var label = btn.querySelector('.btn-label');
        if (label) label.textContent = 'Memproses...';
      }, { once:true }); // cegah double submit
    })();
  </script>
</body>
</html>
