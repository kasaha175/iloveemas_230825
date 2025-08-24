<?php
error_reporting(0);

$total = 0;
foreach($this->cart->contents() as $row){
    $opt   = isset($row['options']) ? $row['options'] : [];
    $total += isset($opt['priceTotal']) && is_numeric($opt['priceTotal']) ? (float)$opt['priceTotal'] : (float)$row['subtotal'];
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
                                <h6 class="m-0 font-weight-bold text-primary">MATERIAL DETAIL (<?=$materianName?>)<br><?php if(!empty($_GET['t'])){ echo ucwords($_GET['t']).' Material'; } ?></h6>
                            </a>
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
                    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                        <h6 class="m-0 font-weight-bold text-primary">BUY CART <?='('.$nameCustomer.')'?> => RP <?=nominal($total)?></h6>
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
                                            <th style="min-width:85px;">Carat</th>
                                            <th>Weight</th>
                                            <th>Price/Gr</th>
                                            <th>Total Price</th>
                                            <th>Type Material</th>
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
                                            <td><?= $opt['types'] ?? (isset($_GET['t']) ? ucwords($_GET['t']) : '-') ?></td>
                                            <td>
                                                <a href="<?=base_url()?>transaction/buy-add-to-cart-reset/?idMaterial=<?=$this->uri->segment(3);?>&idRow=<?=$a['rowid']?>&t=<?=$_GET['t'];?>" class="btn btn-danger btn-circle btn-sm">
                                                    <i class="fas fa-trash"></i>
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
    $('.aang').keyboard({
        layout: 'num',
        restrictInput : true,
        preventPaste : true,
        autoAccept : true
    });
    $('.biayaAdmin').keyboard({
        layout: 'num',
        restrictInput : true,
        preventPaste : true,
        autoAccept : true
    });
    prettyPrint();
});
</script>
