<?php
$total = 0;
foreach($this->cart->contents() as $row){
    $opt   = isset($row['options']) ? $row['options'] : [];
    $total += isset($opt['priceTotal']) && is_numeric($opt['priceTotal']) ? (float)$opt['priceTotal'] : (float)$row['subtotal'];
}

function nominal($angka){
    $jd = number_format($angka, 0, ',', '.');
    return $jd;
}
?>
<div class="col-md-12" style="margin-top:110px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb fixed-top bg-transparent w-50" style="margin-top:5rem">
            <li class="breadcrumb-item"><a class="text-decoration-none text-white" href="<?=base_url()?>dashboard/"><i class="fas fa-home fa-fw"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>transaction/">Transaction</a></li>
            <li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>transaction/sell">Sell</a></li>
            <li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="<?=base_url()?>">Cart</a></li>
        </ol>
    </nav>
    <h3 class="text-center" style="color:#fff">SELL</h3>
    <h3 class="text-center" style="color:#fff">Checkout Session</h3>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3 mb-5">
                <a href="<?=base_url()?>transaction/sell/" class="btn btn-light btn-icon-split btn-lg">
                    <span class="icon text-white-50">
                        <i class="fas fa-arrow-left text-dark"></i>
                    </span>
                    <span class="text">Back</span>
                </a>
            </div>
        </div>
        <div class="row">
			<?php
            // Debug session untuk memastikan noOrder ada atau tidak
            echo "<pre>";
            print_r($this->session->userdata());
            echo "</pre>";

            // Atau kalau hanya ingin cek noOrder saja:
            echo "<p style='color:yellow;background:#000;padding:5px;'>noOrder: "
                . $this->session->userdata('noOrder') . "</p>";
            ?>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                                <h6 class="m-0 font-weight-bold text-primary">MATERIAL DETAIL (<?=$materianName?>)</h6>
                            </a>
                            <div class="collapse show" id="collapseCardExample" style="">
                                <div class="card-body">
                                    <form action="<?=base_url()?>transaction/sell-add-to-cart/" method="post">
                                        <input type="hidden" name="idMaterial" value="<?=$this->uri->segment(3)?>">
                                        <?php if(in_array($this->uri->segment(3), array("13"))){ ?>
                                            <div class="form-group">
                                                <label>POTONGAN</label>
                                                <select required class="zein form-control select2" name="tahun_potongan">
                                                    <option value="">Pilih Potongan</option>
                                                    <?php 
                                                    $tahun_mulai = 2018;
                                                    while ($tahun_mulai <= date('Y')+1) { ?>
                                                        <option value="<?=$tahun_mulai?>">LM Certi <?=$tahun_mulai?></option>
                                                    <?php $tahun_mulai++; } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label>WEIGHT</label>
                                            <input type="number" step="any" name="weight" id="weight" required class="form-control">
                                        </div>
                                        <div class="form-group mb-0">
                                            <input type="submit" class="btn btn-primary btn-block btn-lg mb-0" value="Add To Cart">
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
                    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                        <h6 class="m-0 font-weight-bold text-primary">SELL CART <?='('.$nameCustomer.')'?> => IDR <?=nominal($total)?></h6>
                    </a>
                    <div class="collapse show" id="collapseCardExample" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="font-size:15px;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Material</th>
                                            <th>Type</th>
                                            <th>Carat</th>
                                            <th>Weight</th>
                                            <th stlye="min-width:100px;">Price/Gr</th>
                                            <th stlye="min-width:100px;">Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=0; foreach($this->cart->contents() as $a){ $no++; $opt = isset($a['options']) ? $a['options'] : []; ?>
                                        <tr>
                                            <td><?=$no?></td>
                                            <td><?= !empty($opt['materialName']) ? $opt['materialName'] : $a['name'] ?></td>
                                            <td><?= $opt['materialType'] ?? '' ?></td>
                                            <td><?= $opt['carat'] ?? '' ?></td>
                                            <td><?= $opt['weight'] ?? '' ?></td>
                                            <td>
                                                <?php 
                                                $isDiamond = (strtoupper($opt['materialName'] ?? $a['name']) === 'DIAMOND');
                                                echo $isDiamond ? ($a['price']) : nominal($a['price']);
                                                ?>
                                            </td>
                                            <td><?= nominal( isset($opt['priceTotal']) && is_numeric($opt['priceTotal']) ? $opt['priceTotal'] : $a['subtotal'] ) ?></td>
                                            <td>
                                                <a href="<?=base_url()?>transaction/sell-add-to-cart-reset/?idMaterial=<?=$this->uri->segment(3);?>&idRow=<?=$a['rowid']?>" class="btn btn-danger btn-circle btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <form action="<?=base_url()?>transaction/sell-checkout/">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <p>PLUS/MINUS</p>
                                            <select type="number" step="any" class="form-control" name="operator">
                                                <option>+</option>
                                                <option>-</option>
                                            </select>
                                        </div>
                                        <div class="col-md-10">
                                            <p>BIAYA ADMIN</p>
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
                <a class="btn btn-danger" href="<?=base_url()?>transaction/sell-add-to-cart-reset/?idMaterial=<?=$this->uri->segment(3);?>">Reset</a>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function ($) {
    $('.biayaAdmin').keyboard({
        layout: 'num',
        restrictInput : true,
        preventPaste : true,
        autoAccept : true
    });
    $('#weight').keyboard({
        layout: 'num',
        restrictInput : true,
        preventPaste : true,
        autoAccept : true
    });
    prettyPrint();
});
</script>
