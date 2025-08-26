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
    .page-wrap     { position: relative; z-index: 1; padding-top: calc(var(--topbar-h) + 16px); }

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
      $('.select2').select2();

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
  </script>
</body>
</html>
