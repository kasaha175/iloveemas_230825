<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?=$title?></title>

    <!-- Core CSS -->
    <link href="<?=base_url()?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?=base_url()?>assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/css/keyboard.css" rel="stylesheet">
    <link href="<?=base_url()?>/assets/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/summernote/summernote.min.css" rel="stylesheet">
	<!-- <link href="<?= base_url() ?>assets/offline/custom.css" rel="stylesheet"> -->

    <!-- Keyboard Plugin -->
    <link href="<?=base_url()?>assets/keyboard/docs/css/jquery-ui.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/keyboard/css/keyboard.css" rel="stylesheet">

    <!-- Optional Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <!-- Core JavaScript -->
    <script src="<?=base_url()?>assets/vendor/jquery/jquery.min.js"></script>
	<script src="<?=base_url()?>assets/select2/js/select2.min.js"></script>
    <script src="<?=base_url()?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="<?=base_url()?>assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- jqKeyboard Dependencies -->
    <script src="<?=base_url()?>assets/keyboard/docs/js/jquery-ui.min.js"></script>
    <script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.js"></script>
    <script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.extension-typing.js"></script>
    <script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.extension-autocomplete.js"></script>
    <script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.extension-caret.js"></script>

    <!-- Plugins -->
    <script src="<?= base_url()?>assets/offline/sweetalert2.all.js"></script>
    <script src="<?=base_url()?>assets/js/sb-admin-2.min.js"></script>
    <script src="<?=base_url()?>assets/summernote/summernote.min.js"></script>
    <script src="<?=base_url()?>assets/offline/dataTables.buttons.min.js"></script>
    <script src="<?=base_url()?>assets/offline/jszip.min.js"></script>
    <script src="<?=base_url()?>assets/offline/buttons.html5.min.js"></script>
    <script src="<?=base_url()?>assets/offline/buttons.print.min.js"></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', 'sans-serif';
            background-image: url(<?=base_url()?>assets/offline/bg-black.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            width: 100%;
            min-height: 750px;
        }
        body:before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background: rgba(0,0,0,.45);
        }
        .btn-custom {
            color: #000;
            padding: 16px 0;
            width: 100%;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16pt;
            box-shadow: 0 5px 15px -5px rgba(0,0,0,0.1);
            transition: all 250ms ease-in-out;
        }
        .select2 {
            width: 100%!important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light topbar mb-4 fixed-top shadow" style="background-color:#9de4ff;">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>
        <a href="<?=base_url()?>" style="text-decoration: none;color:#000;">
            <img src="<?=base_url()?>assets/offline/icon-ilovemas.png" alt="" style="max-width: 200px;">
        </a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline small" style="color:#000;">Administrator</span>
                    <img class="img-profile rounded-circle" src="<?=base_url()?>assets/img/user/logo.png">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Content -->
    <div class="container-fluid">
        <?=$content?>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="top: 84px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="<?=base_url()?>logout-process/">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Initialization -->
    <script>
        $(function () {
            // Initialize Keyboard
            if (typeof jqKeyboard !== 'undefined') {
                jqKeyboard.init();
            }

            // Initialize Summernote
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['misc', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>
