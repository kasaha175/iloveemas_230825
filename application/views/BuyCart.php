<?php
error_reporting(0);
	$total = 0;
	foreach($this->cart->contents() as $a){
		$total = $total + $a['priceTotal'];
	}

	function nominal($angka){
		$jd = number_format(floor($angka), 0, ',', '.');
		return $jd;
	}
?>
<div class="col-md-12" style="margin-top:110px;">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb fixed-top bg-transparent w-50" style="margin-top:5rem">
			<li class="breadcrumb-item"><a class="text-decoration-none text-white" href="<?=base_url()?>dashboard/"><i class="fas fa-home fa-fw"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>transaction/">Transaction</a></li>
			<li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>transaction/buy">Buy</a></li>
			<li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>">Cart</a></li>
		</ol>
	</nav>
	<h3 class="text-center" style="color:#fff">BUY</h3>
	<h3 class="text-center" style="color:#fff">Checkout Session</h3>
	<br>
	<div class="container">
		<div class="row">
			<div class="col-md-12 mt-3 mb-5">
				<a href="<?=base_url()?>transaction/buy/" class="btn btn-light btn-icon-split btn-lg">
					<span class="icon text-white-50">
						<i class="fas fa-arrow-left text-dark"></i>
					</span>
					<span class="text">Back</span>
				</a>
			</div>
		</div>
		<div class="row">
			<?php
echo "<pre>";
echo "===== DEBUG DATA BUYCART =====\n";

// kalau ada variabel tambahan dari controller
if (isset($userData)) {
    echo "\nUSER DATA:\n";
    print_r($userData);
}

if (isset($id_transaction)) {
    echo "\nID TRANSACTION:\n";
    var_dump($id_transaction);
}

if (isset($no_order)) {
    echo "\nNO ORDER:\n";
    var_dump($no_order);
}

if (isset($customer)) {
    echo "\nCUSTOMER DATA:\n";
    print_r($customer);
}

// debug isi cart CI3
echo "\nCART CONTENTS:\n";
print_r($this->cart->contents());

echo "===== END DEBUG =====\n";
echo "</pre>";
?>

			<div class="col-md-4">
				<div class="row">
					<div class="col-md-12">
						<div class="card shadow mb-4">
							<!-- Card Header - Accordion -->
							<a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
								<h6 class="m-0 font-weight-bold text-primary">MATERIAL DETAIL (<?=$materianName?>)<br><?php if(!empty($_GET['t'])){ echo ucwords($_GET['t']).' Material'; } ?></h6>
							</a>
							<!-- Card Content - Collapse -->
							<div class="collapse show" id="collapseCardExample" style="">
								<div class="card-body">
									<form action="<?=base_url()?>transaction/buy-add-to-cart/" method="post">
										<input type="hidden" name="idMaterial" value="<?=$this->uri->segment(3)?>">
										<input type="hidden" name="types" value="<?=$_GET['t']?>">
										<?php if(in_array($this->uri->segment(3), array("2", "5", "6", "7", "8", "9", "10"))){ ?>
										<div class="form-group">
											<label>TYPE</label>
											<select name="materialType" required class="zein form-control select2">
												<?php foreach($materialType as $m){ ?>
												<option><?=$m->mt_name?></option>
												<?php } ?>
											</select>
										</div>
										<?php } ?>
										<?php if(in_array($this->uri->segment(3), array("5", "5"))){ ?>
										<div class="form-group">
											<label>PRESENTASI</label>
											<!--<select name="carat" required class="zein form-control select2">-->
						                    <input type="number" step="any" name="carat" required class="aang form-control">
										</div>
										<?php } ?>
										
										<?php if(in_array($this->uri->segment(3), array("2", "23"))){ ?>
										<div class="form-group">
											<label>CARAT</label>
											<select name="carat" required class="zein form-control select2">
												<?php foreach($carat as $c){ ?>
												<option><?=$c->c_name?></option>
												<?php } ?>
											</select>
										</div>
										<?php } ?>
										
										<?php if(in_array($this->uri->segment(3), array("6","7","8","9","10","19","21"))){ ?>
										<div class="form-group">
											<label>PERCANTAGE</label>
											<input type="number" step="any" name="percentage" required class="aang form-control">
										</div>
										<?php } ?>
										
										
										<?php if(in_array($this->uri->segment(3), array("1"))){ ?>
										<div class="form-group">
											<label>PRICE</label>
											<input type="number" step="any" name="price" required class="aang form-control">
										</div>
										<?php } ?>
										<?php if(in_array($this->uri->segment(3), array("3"))){ ?>
										<div class="form-group">
											<label>POTONGAN</label>
											<select required class="zein form-control select2" name="tahun_potongan">
												<option value="">Pilih Potongan</option>
												
												<?php 
												$tahun_mulai = 2018;
												// $d=mktime(11, 14, 54, 8, 12, 2023);
												while ($tahun_mulai <= date('Y')+1) { ?>
													<option value="<?=$tahun_mulai?>">LM Certi <?=$tahun_mulai?></option>
												<?php $tahun_mulai++; } ?>
											</select>
										</div>
										<?php } ?>
										
										<?php if(in_array($this->uri->segment(3), array("2", "3", "4", "5", "6", "7", "8", "9", "10", "17", "18", "19", "23", "21"))){ ?>
										<div class="form-group">
											<label>WEIGHT</label>
											<input type="number" step="any" name="weight" required class="aang form-control">
										</div>
										<?php } ?>
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-block btn-lg" value="Add To Cart">
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="card shadow mb-4">
					<!-- Card Header - Accordion -->
					<a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
						<h6 class="m-0 font-weight-bold text-primary">BUY CART <?='('.$nameCustomer.')'?> => RP <?=nominal($total)?></h6>
					</a>
					<!-- Card Content - Collapse -->
					<div class="collapse show" id="collapseCardExample" style="">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="font-size:15px;">
									<thead>
										<tr>
											<th>No</th>
											<th>Material</th>
											<th>Type</th>
											<th style="min-width:85px;">Carat</th>
											<th>Weight</th>
											<th>Price/Gr</th>
											<th>Total Price</th>
											<th>Type Material</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0; foreach($this->cart->contents() as $a){ $no++; ?>
										<tr>
											<td><?=$no?></td>
											<td><?=$a['materialName']?></td>
											<td><?=$a['materialType']?></td>
											<td><?=$a['carat']?></td>
											<td><?=$a['weight']?></td>
											<td><?php if($a['materialName']!='DIAMOND'){echo nominal($a['prices']);}else{echo ($a['prices']);}?></td>
											<td><?=nominal($a['priceTotal'])?></td>
											<td><?=$a['types']?></td>
											<td><a href="<?=base_url()?>transaction/buy-add-to-cart-reset/?idMaterial=<?=$this->uri->segment(3);?>&idRow=<?=$a['rowid']?>&t=<?=$_GET['t'];?>" class="btn btn-danger btn-circle btn-sm"><i class="fas fa-trash"></i>
												</a>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								

								<form action="<?=base_url()?>transaction/buy-checkout/">
								<div class="row">
								<div class="col-md-4">
									<p>PLUS / MINUS BIAYA ADMIN</p>
									<select type="number" step="any" class="form-control" name="operator">
										<option value="+"> Plus (+)</option>
										<option value="-"> Minus (-)</option>
									</select>
								</div>
								<div class="col-md-8">
									<p>&nbsp;</p>
									<input type="number" step="any" class="form-control biayaAdmin" name="biayaAdmin">
								</div>
								</div>
								<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document" style="top: 84px;">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">Ready to Checkout?</h5>
												<button class="close" type="button" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
												</button>
											</div>
											<div class="modal-body">Select "Checkout" below if you are ready to end your cart session.</div>
											<div class="modal-footer">
												<button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
												<button type="submit" class="btn btn-success">Checkout</button>
											</div>
										</div>
									</div>
								</div>

								</form>
							</div>
							
							<a href="#" data-toggle="modal" data-target="#resetModal" class="btn btn-danger btn-icon-split btn-lg mt-3" style="margin-right:13px">
								<span class="icon text-white-50">
									<i class="fa fa-times"></i>
								</span>
								<span class="text">Reset</span>
							</a>
							<a href="#" data-toggle="modal" data-target="#checkoutModal" class="btn btn-primary btn-icon-split btn-lg mt-3" style="margin-right:13px">
								<span class="icon text-white-50">
									<i class="fa fa-check"></i>
								</span>
								<span class="text">Checkout</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("#materialType").select2();
	$(".select2").select2();
</script>

<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="top: 84px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Ready to Reset?</h5>
				<button class="close" type="button" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">Select "Reset" below if you are ready to end your cart session.</div>
			<div class="modal-footer">
				<button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger" href="<?=base_url()?>transaction/buy-add-to-cart-reset/?idMaterial=<?=$this->uri->segment(3);?>&t=<?=$_GET['t'];?>">Reset</a>
			</div>
		</div>
	</div>
</div>

<script>
    jQuery(function ($) {
        // Num Pad Input
	// ********************
	$('.aang').keyboard({
		layout: 'num',
		restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
		preventPaste : true,  // prevent ctrl-v and right click
		autoAccept : true
	});
	$('.biayaAdmin').keyboard({
		layout: 'num',
		restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
		preventPaste : true,  // prevent ctrl-v and right click
		autoAccept : true
	});
	
    prettyPrint();

    });
</script>