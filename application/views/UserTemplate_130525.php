<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>
		<?=$title?>
		</title>
		<link href="<?=base_url()?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
			rel="stylesheet">
			<link rel="stylesheet" href="<?=base_url()?>assets/offline/font-awesome.min.css">
			<link href="<?=base_url()?>assets/css/sb-admin-2.min.css" rel="stylesheet">
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
			<link href="<?=base_url()?>assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
			<link href="<?= base_url() ?>assets/summernote/summernote.min.css" rel="stylesheet">
			<script src="<?=base_url()?>assets/offline/jquery.min.js"></script>
			<script src="<?=base_url()?>assets/offline/jquery-ui.min.js"></script>
			<script src="<?=base_url()?>assets/offline/jqkeyboard.js"></script>
			<script src="<?=base_url()?>assets/offline/jqk.layout.en.js"></script>
			<link href="<?=base_url()?>assets/css/keyboard.css" rel="stylesheet">
			<link href="<?=base_url()?>assets/css/login.css" rel="stylesheet">
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
			<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
			<link rel="stylesheet" href="<?=base_url()?>/assets/select2/css/select2.min.css">
			<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css"> -->
			
			
			<script src="<?=base_url()?>assets/vendor/jquery/jquery.min.js"></script>
			<script src="<?=base_url()?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
			<script src="<?=base_url()?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>
			<script src="<?=base_url()?>assets/js/sb-admin-2.min.js"></script>
			
			
			<script src="<?=base_url()?>assets/vendor/datatables/jquery.dataTables.min.js"></script>
			<script src="<?=base_url()?>assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
			<script src="<?=base_url()?>assets/vendor/chart.js/Chart.min.js"></script>
			<script src='<?=base_url()?>assets/offline/dataTables.buttons.min.js'></script>
			<script src='<?=base_url()?>assets/offline/buttons.bootstrap4.min.js'></script>
			<script src='<?=base_url()?>assets/offline/jszip.min.js'></script>
			<!-- <script src='<?=base_url()?>assets/offline/pdfmake.min.js'></script> -->
			<!-- <script src='<?=base_url()?>assets/offline/vfs_fonts.js'></script> -->
			<script src='<?=base_url()?>assets/offline/buttons.html5.min.js'></script>
			<script src='<?=base_url()?>assets/offline/buttons.print.min.js'></script>
			<script src="<?= base_url() ?>assets/offline/sweetalert2.all.js"></script>
			<!-- <link rel="stylesheet" href="sweetalert2.min.css"> -->
			<!-- <script src='<?=base_url()?>assets/offline/buttons.colVis.min.js'></script> -->
			
			<style>
			body{
			font-family: 'Poppins', 'sans-serif';
			background-image: url(<?=base_url()?>assets/offline/bg-black.jpg);
			background-size: cover;
			background-repeat: no-repeat;
			background-position: center;
			width: 100%;
			min-height:750px;
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
			.btn-custom{
			color:#000;
			padding: 16px 0;
			width: 100%;
			background: #fff;
			border-radius: 4px;
			cursor: pointer;
			font-size: 16pt;
			box-shadow: 0 5px 15px -5px rgba(0,0,0,0.1);
			transition: all 250ms ease-in-out;
			}
			.select2 { width: 100%!important; }
			</style>
		</head>
		<body>
			<nav class="navbar navbar-expand navbar-light topbar mb-4 fixed-top shadow" style="background-color:#9de4ff;">
				<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
				<i class="fa fa-bars"></i>
				</button>
				<a href="<?=base_url()?>" style="text-decoration: none;color:#000;">
				<img src="<?=base_url()?>assets/offline/icon-ilovemas.png" alt="" style="max-width: 200px;">
				</a>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item dropdown no-arrow d-sm-none">
						<a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-search fa-fw"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
							<form class="form-inline mr-auto w-100 navbar-search">
								<div class="input-group">
									<input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
									aria-label="Search" aria-describedby="basic-addon2">
									<div class="input-group-append">
										<button class="btn btn-primary" type="button">
										<i class="fas fa-search fa-sm"></i>
										</button>
									</div>
								</div>
							</form>
						</div>
					</li>
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
			<!-- jQuery (required) & jQuery UI + theme (optional) -->
<link href="<?=base_url()?>assets/keyboard/docs/css/jquery-ui.min.css" rel="stylesheet">
<!-- still using jQuery v2.2.4 because Bootstrap doesn't support v3+ -->
<script src="<?=base_url()?>assets/keyboard/docs/js/jquery-latest.min.js"></script>
<script src="<?=base_url()?>assets/keyboard/docs/js/jquery-ui.min.js"></script>
<!-- <script src="<?=base_url()?>assets/keyboard/docs/js/jquery-migrate-3.0.0.min.js"></script> -->

<!-- keyboard widget css & script (required) -->
<link href="<?=base_url()?>assets/keyboard/css/keyboard.css" rel="stylesheet">
<script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.js"></script>

<!-- keyboard extensions (optional) -->
<script src="<?=base_url()?>assets/keyboard/js/jquery.mousewheel.js"></script>
<script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.extension-typing.js"></script>
<script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.extension-autocomplete.js"></script>
<script src="<?=base_url()?>assets/keyboard/js/jquery.keyboard.extension-caret.js"></script>

<!-- demo only -->
<link rel="stylesheet" href="<?=base_url()?>assets/keyboard/docs/css/font-awesome.min.css">

<link href="<?=base_url()?>assets/keyboard/docs/css/tipsy.css" rel="stylesheet">
<link href="<?=base_url()?>assets/keyboard/docs/css/prettify.css" rel="stylesheet">
<script src="<?=base_url()?>assets/keyboard/docs/js/jquery.tipsy.min.js"></script>
<script src="<?=base_url()?>assets/keyboard/docs/js/prettify.js"></script> <!-- syntax highlighting -->
<script src="<?=base_url()?>assets/summernote/summernote.min.js"></script>
<script src="<?=base_url()?>assets/summernote/summernote.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>/assets/select2/js/select2.min.js"></script>
			<?=$content?>
		</div>
	</div>
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
				</div> </div> </div> </div>
			</body>
			<script type="text/javascript">
			$(function () {
			jqKeyboard.init();
			});
			function renderTextArea() {
    $('.summernote').summernote({
          popover: {
            image: [
              ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
              ['float', ['floatLeft', 'floatRight', 'floatNone']],
              ['remove', ['removeMedia']]
            ],
            link: [
              ['link', ['linkDialogShow', 'unlink']]
            ],
            table: [
              ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
              ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
            ],
            air: [
              ['color', ['color']],
              ['font', ['bold', 'underline', 'clear']],
              ['para', ['ul', 'paragraph']],
              ['table', ['table']],
              ['insert', ['link', 'picture']]
            ]
          }
      });
  }
			</script>
		</html>