<?php
foreach ($data as $a) {
};
function nominal($angka)
{
	$jd = number_format($angka, 0, ',', '.');
	return $jd;
}
function penyebut($nilai)
{
	$nilai = abs($nilai);
	$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	$temp = "";
	if ($nilai < 12) {
		$temp = " " . $huruf[$nilai];
	} else if ($nilai < 20) {
		$temp = penyebut($nilai - 10) . " belas";
	} else if ($nilai < 100) {
		$temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
	} else if ($nilai < 200) {
		$temp = " seratus" . penyebut($nilai - 100);
	} else if ($nilai < 1000) {
		$temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
	} else if ($nilai < 2000) {
		$temp = " seribu" . penyebut($nilai - 1000);
	} else if ($nilai < 1000000) {
		$temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
	} else if ($nilai < 1000000000) {
		$temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
	} else if ($nilai < 1000000000000) {
		$temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
	} else if ($nilai < 1000000000000000) {
		$temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
	}
	return $temp;
}
function terbilang($nilai)
{
	if ($nilai < 0) {
		$hasil = "minus " . trim(penyebut($nilai));
	} else {
		$hasil = trim(penyebut($nilai));
	}
	return $hasil . ' rupiah';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="" xml:lang="">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="https://fonts.googleapis.com/css?family=Gothic+A1:700&display=swap" rel="stylesheet">
	<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	<style>
		@page {
            margin: 0;
        }
		table {
			border-collapse: collapse;
		}

		body {
			font-family: sans-serif;
			margin: 0 !important;
		}

		input[type=checkbox] {
			transform: scale(1.5);
		}

		.no-margin p {
			margin: 0px !important;
		}
		.no-margin span {
			font-size: 12px !important;
		}
		@media print
		{    
			.no-print, .no-print *
			{
				display: none !important;
			}
		}
	</style>
	<title><?= $title ?></title>
</head>

<body vlink="blue" link="blue" style="background-color:#A0A0A0;">
	<div style="width:918px;min-height:1188px;background-color:#fff;">
		<div class="no-print" style="padding:5px;margin:0px;" id="printHide">
			<a id="doPrint" href="#!"><img src="<?= base_url() ?>assets/offline/print.png" alt="" style="width:30px;float:right;" /></a>
			<!-- <a href="<?= base_url() ?>report/sell/"> -->
			<img src="<?= base_url() ?>assets/offline/back.png" alt="" style="width:30px;float:right;cursor: pointer;" onclick="clickBack();" ontouchstart="clickBack();" />
			<!-- </a> -->
		</div>
		<div style="padding:45px;margin:0px;" id="printNow">
			<div style="width:100%">
				<div style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;">
					<div style="border:1px #000 solid">
						<p style="margin:5px; font-weight: bold; font-size:14px;; text-align:center;">
							PT Muara Logam Indonesia
						</p>
					</div>
					<div style="padding-left:5px; padding-top:1px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;border-bottom:1px #000 solid">
						<table style="width: 100%">
							<tr>
							<?php
								$this->db->order_by('urutan_cabang', 'ASC');
								$this->db->where('status', 'ENABLE');
								$cabang = $this->db->get('tb_cabang')->result();
								foreach ($cabang as $key => $value) : 
									if($key%2 == 0){ echo '</tr><tr>'; }
								?>
									<td style="width: 5%">
										<p style="font-size:12px; margin: 0px;">
											<input type="checkbox" />
										</p>
									</td>
									<td style="width: 45%">
										<p style="font-size:12px; margin: 0px;">
											<?= $value->nama_cabang ?> : <?= $value->alamat_cabang ?>
										</p>
									</td>
								<?php endforeach; ?>
							</tr>
						</table>
						<p style="text-align:center; font-size: 10px;"><a href="https://www.iloveemas.co.id/" style="text-decoration:none; color:black">www.iloveemas.co.id</a></p>
					</div>
				</div>
				<div style="padding-right:5px; width:48%; display:inline-block;vertical-align:text-top;margin-top:15px;">
					<div style="border:1px #000 solid; min-height:172px;">
						<p style="margin:5px; text-align:center; font-weight: bold; font-size:14px">
							Vendor
						</p>
						<span style="display: inline-block;width: 100%;border-top: 1px solid black; margin-bottom: 10px;"></span>
						<p style="margin:5px; font-size:12px">
							Name : <?= ucwords(strtolower($a->nameCustomer)) ?>
						</p>
						<p style="margin:5px; font-size:12px">
							Id Number : <?= strtoupper($a->c_id_number) ?>
						</p>
						<p style="margin:5px; font-size:12px ">
							Address : <?= ucwords(strtolower($a->c_address)) ?>
						</p>
						<p style="margin:5px; font-size:12px ;">
							Resident Address : <?= $a->c_resident_address ?>
						</p>
						<p style="margin:5px; font-size:12px ">
							Phone Number : <?= $a->c_phone ?>
						</p>
					</div>
				</div>
				
				<div style="margin-left: 15px; padding-right:5px; padding-top:15px;width:48%; display:inline-block;vertical-align:text-top;">
					<div>
						<p style="margin:5px; font-weight:bold; font-size:14px; text-align:center;">
							<b>Purchase Payment</b>
						</p>
					</div>
					<span style="display: inline-block;width: 100%;border-top: 1px solid black; margin-bottom:10px;"></span>
					<div>
						<div style="padding-right:5px; width:48%; display:inline-block;">
							<div style="border:1px #000 solid">
								<p style="margin:5px; font-weight:bold; text-align:center; font-size:14px">
									Payment Date
								</p>
							</div>
							<div style="text-align:center; min-height:20px; padding-top:30px; padding-bottom:30px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;border-bottom:1px #000 solid"><p style="padding-top:5px; font-size: 12px;"><?= date('Y-m-d', strtotime($a->t_date_created)) ?></p></div>
						</div>
						<div style="width:49%; display:inline-block;">
							<div style="border:1px #000 solid">
								<p style="margin:5px; font-weight:bold; text-align:center; font-size:14px">
									Invoice Number
								</p>
							</div>
							<div style="text-align:center; min-height:30px; padding-top:30px; padding-bottom:30px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;border-bottom:1px #000 solid"><p style="padding-top:5px; font-size: 12px;"><?= $a->t_no_order ?></p></div>
						</div>
					</div>
				</div>
			</div>

			<div style="width:100%;margin-top:15px;">
				<table style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top; page-break-inside:auto">
					<tr>
						<td style="padding:10px 0px; width:15px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							No
						</td>
						<td style="padding-left:5px; min-width:120px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							Material
						</td>
						<td style="padding-left:5px; min-width:110px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							Type
						</td>
						<td style="padding-left:5px; min-width:50px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							Carat / Percentage
						</td>
						<td style="padding-left:5px; min-width:100px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							Weight (gr)
						</td>
						<td style="padding-left:5px; min-width:145px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							Price/gr (Rp)
						</td>
						<td style="padding-left:5px; min-width:145px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">
							Amount (Rp)
						</td>
					</tr>
					<?php $no = 0;
					foreach ($detail as $d) {
						$no++; ?>
						<?php if ($no > 27) {
							break;
						} else { ?>
							<tr>
								<td style="padding-left:5px; min-width:30px; border: 1px solid black !important; font-size: 14px">
									<?= $no ?>
								</td>
								<td style="padding-left:5px; min-width:125px; border: 1px solid black !important; font-size: 14px">
									<?php if ($d->ti_material == 'Cust. Profesion') {
										echo 'Gold';
									} else {
										echo $d->ti_material;
									} ?>
								</td>
								<td style="padding-left:5px; min-width:110px; border: 1px solid black !important; font-size: 14px">
									<?= $d->ti_material_type ?>
								</td>
								<td style="padding-left:5px; min-width:50px; border: 1px solid black !important; font-size: 14px">
									<?= $d->ti_carat ?>
								</td>
								<td style="padding-left:5px; min-width:100px; border: 1px solid black !important; font-size: 14px">
									<?= $d->ti_weight ?>
								</td>
								<td style="padding-left:5px; min-width:145px; border: 1px solid black !important; font-size: 14px">
									<?php if ($d->ti_price != '-') {
										echo nominal($d->ti_price);
									} else {
										echo $d->ti_price;
									} ?>
								</td>
								<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right; font-size: 14px">
									<?= nominal($d->ti_price_total) ?>
								</td>
							</tr>
					<?php }
					} ?>
					<tr>
						<td style="padding-left:5px; min-width:30px; border: 1px solid black !important; font-size: 14px">
							#
						</td>
						<td style="padding-left:5px; border: 1px solid black !important; font-size: 14px" colspan="5">
							ADMIN
						</td>

						<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;" font-size: 14px>
							<?= nominal($a->t_price_admin) ?>
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid black !important; font-size: 14px" colspan="5">
							<span><input style="margin:10px 5px 10px 5px;" type="checkbox"><span>Cash</span></span>
							<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Credit</span></span>
							<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Debit</span></span>
							<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Transfer</span></span>
						</td>
						<td style="min-width:145px; border: 1px solid black !important;text-align:center; font-weight: bold; font-size: 14px">
							TOTAL
						</td>
						<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right; font-weight: bold;  font-size: 14px">
							<?= nominal($a->t_price_total + $a->t_price_admin) ?>
						</td>
					</tr>
				</table>
			</div>
			<?php if (count($detail) > 27) { ?>
				<div style="width:100%;margin-top:0px;">
					<table style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;">
						<?php $no = 0;
						foreach ($detail as $d) {
							$no++; ?>
							<?php if ($no >= 74) {
								break;
							} else if ($no <= 27) {
							} else { ?>
								<tr>
									<td style="padding-left:5px; min-width:30px; border: 1px solid black !important;">
										<?= $no ?>
									</td>
									<td style="padding-left:5px; min-width:125px; border: 1px solid black !important;">
										<?php if ($d->ti_material == 'Cust. Profesion') {
											echo 'Gold';
										} else {
											echo $d->ti_material;
										} ?>
									</td>
									<td style="padding-left:5px; min-width:110px; border: 1px solid black !important;">
										<?= $d->ti_material_type ?>
									</td>
									<td style="padding-left:5px; min-width:50px; border: 1px solid black !important;">
										<?= $d->ti_carat ?>
									</td>
									<td style="padding-left:5px; min-width:100px; border: 1px solid black !important;">
										<?= $d->ti_weight ?>
									</td>
									<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;">
										<?php if ($d->ti_price != '-') {
											echo nominal($d->ti_price);
										} else {
											echo $d->ti_price;
										} ?>
									</td>
									<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;">
										<?= nominal($d->ti_price_total) ?>
									</td>
								</tr>
						<?php }
						} ?>
						<tr>
							<td style="padding-left:5px; min-width:30px; border: 1px solid black !important;">
								#
							</td>
							<td style="padding-left:5px; border: 1px solid black !important;" colspan="5">
								ADMIN
							</td>

							<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;">
								<?= nominal($a->t_price_admin) ?>
							</td>
						</tr>
						<tr>
							<td style="border: 1px solid black !important;" colspan="5">
								<span><input style="margin:10px 5px 10px 5px;" type="checkbox"><span>Cash</span></span>
								<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Credit</span></span>
								<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Debit</span></span>
								<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Transfer</span></span>
							</td>
							<td style="min-width:145px; border: 1px solid black !important;text-align:center;">
								TOTAL
							</td>
							<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;">
								<?= nominal($a->t_price_total + $a->t_price_admin) ?>
							</td>
						</tr>
					</table>
				</div>
			<?php } ?>
			<?php if (count($detail) > 74) { ?>
				<div style="width:100%;margin-top:0px;">
					<table style="padding-right:5px; width:96%; display:inline-block;vertical-align:text-top;">
						<?php $no = 0;
						foreach ($detail as $d) {
							$no++; ?>
							<?php if ($no < 74) {
							} else { ?>
								<tr>
									<td style="padding-left:5px; min-width:30px; border: 1px solid black !important;">
										<?= $no ?>
									</td>
									<td style="padding-left:5px; min-width:125px; border: 1px solid black !important;">
										<?php if ($d->ti_material == 'Cust. Profesion') {
											echo 'Gold';
										} else {
											echo $d->ti_material;
										} ?>
									</td>
									<td style="padding-left:5px; min-width:110px; border: 1px solid black !important;">
										<?= $d->ti_material_type ?>
									</td>
									<td style="padding-left:5px; min-width:50px; border: 1px solid black !important;">
										<?= $d->ti_carat ?>
									</td>
									<td style="padding-left:5px; min-width:100px; border: 1px solid black !important;">
										<?= $d->ti_weight ?>
									</td>
									<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;">
										<?php if ($d->ti_price != '-') {
											echo nominal($d->ti_price);
										} else {
											echo $d->ti_price;
										} ?>
									</td>
									<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;">
										<?= nominal($d->ti_price_total) ?>
									</td>
								</tr>
						<?php }
						} ?>
						<tr>
							<td style="padding-left:5px; min-width:30px; border: 1px solid black !important;">
								#
							</td>
							<td style="padding-left:5px; border: 1px solid black !important;" colspan="5">
								ADMIN
							</td>

							<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;">
								<?= nominal($a->t_price_admin) ?>
							</td>
						</tr>
						<tr>
							<td style="border: 1px solid black !important;" colspan="5">
								<span><input style="margin:10px 5px 10px 5px;" type="checkbox"><span>Cash</span></span>
								<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Credit</span></span>
								<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Debit</span></span>
								<span><input style="margin:10px 5px 10px 65px;" type="checkbox"><span>Transfer</span></span>
							</td>
							<td style="min-width:145px; border: 1px solid black !important;text-align:center;">
								TOTAL
							</td>
							<td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;">
								<?= nominal($a->t_price_total + $a->t_price_admin) ?>
							</td>
						</tr>
					</table>
				</div>
			<?php } ?>
			<?php 
			if(count($detail) > 10){
			?>
				<div style="page-break-inside: avoid">
			<?php }else{ ?>
				<div style="width:100%;margin-top:5px;">
			<?php }?>
					<table style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;margin-top:10px; page-break-inside:auto">
						<tr>
							<td style="padding:10px 0px 10px 10px; min-width:465px; border: 1px solid black !important; text-align: center; font-weight: bold; font-size:14px;" colspan="5">
								Syarat & Ketentuan
							</td>
						</tr>
						<tr>
							<td style="padding:10px 10px 10px 10px; min-width:465px; border: 1px solid black !important;" colspan="5">
								<?php
								$this->db->order_by('tm_priority', 'asc');
								$memo = $this->db->get('tb_memo')->result(); ?>
								<?php foreach ($memo as $key => $value) : ?>
									<div class="no-margin" style="text-align: justify; font-size:12px"><?= $value->tm_value ?></div>
								<?php endforeach ?>
							</td>
						</tr>
					</table>
					<table style="padding-right:5px; width:100%;vertical-align:text-top;">
						<tr>
							<td style="width: 20%"></td>
							<td style="vertical-align:top; padding-left:5px; min-width:145px;">
								<p style="text-align:center; font-size: 14px">
									Received By
								</p>
								<p style="text-align:center;margin-top:100px; font-size: 14px">
									<?= strtoupper(strtolower($a->nameCustomer)) ?>
								</p>
								<p style="text-align:center;margin-top:-25px; font-size: 14px">
									--------------
								</p>
							</td>
							<td style="vertical-align:top; padding-left:5px; min-width:145px;">
								<p style="text-align:center; font-size: 14px">
									Paid By
								</p>
								<p style="text-align:center;margin-top:100px; font-size: 14px">
									I LOVE EMAS
								</p>
								<p style="text-align:center;margin-top:-25px; font-size: 14px">
									--------------
								</p>
							</td>
							<td style="width: 20%"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script>
			$("#doPrint").click(function() {
				window.print();
				ajaxdestroy();
				// clickBack();
			});

			$("#doPrint").touches(function() {
				window.print();
				ajaxdestroy();
				// clickBack();
			});

			function ajaxdestroy() {
				jQuery.ajax({
					url: '<?= base_url('transaction/chart-destroy') ?>',
					success: function(data, textStatus, xhr) {
						window.location.href = "<?= base_url('dashboard') ?>";
					},
				});
			}

			function clickBack() {
				// window.history.back();
				window.location.href = "<?= base_url('dashboard') ?>";
			}
		</script>
	</div>
</body>

</html>