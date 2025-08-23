<div class="col-md-12" style="margin-top:110px;">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb fixed-top bg-transparent w-50" style="margin-top:5rem">
			<li class="breadcrumb-item"><a class="text-decoration-none text-white" href="<?=base_url()?>dashboard/"><i class="fas fa-home fa-fw"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>transaction/">Transaction</a></li>
			<li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>">Sell</a></li>
		</ol>
	</nav>
	<h3 class="text-center" style="color:#fff">SELL</h3>
	<h3 class="text-center" style="color:#fff">Choose Material</h3>
	<br>
	<div class="container">
		<div class="row">
			<div class="col-md-12 mt-3 mb-5">
				<a href="<?=base_url()?>transaction/" class="btn btn-light btn-icon-split btn-lg">
					<span class="icon text-white-50">
						<i class="fas fa-arrow-left text-dark"></i>
					</span>
					<span class="text">Back</span>
				</a>
			</div>
			<?php foreach($data as $d){ ?>
			<div class="col-md-2" style="padding:10px 10px">
				<a href="<?=base_url()?>transaction/sell/<?=$d->m_id?>/" style="text-decoration: none;">
					<div class="box-type">
						<img src="<?=base_url()?>assets/offline/<?=$d->m_img?>" class="img-fluid mt-3" style="max-width:100%;max-height:88px;">
						<br><br>
						<span><?=$d->m_name?></span>
					</div>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>