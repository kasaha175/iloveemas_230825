<?php
// Hitung total harga dari cartContents (DB)
$total = 0;
if (!empty($cartContents)) {
    foreach ($cartContents as $a) {
        $total += isset($a['ti_price_total']) ? $a['ti_price_total'] : 0;
    }
}

// Helper nominal
function nominal($angka){
    return number_format($angka, 0, ',', '.');
}
?>
<div class="col-md-12" style="margin-top:110px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb fixed-top bg-transparent w-50" style="margin-top:5rem">
            <li class="breadcrumb-item">
                <a class="text-decoration-none text-white" href="<?=base_url()?>dashboard/">
                    <i class="fas fa-home fa-fw"></i> Dashboard
                </a>
            </li>
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
            <!-- Sidebar Form -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true">
                        <h6 class="m-0 font-weight-bold text-primary">MATERIAL DETAIL (<?=$materianName ?? '-'?>)</h6>
                    </a>
                    <div class="collapse show" id="collapseCardExample">
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

            <!-- Cart Table -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <a href="#collapseCardTable" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true">
                        <h6 class="m-0 font-weight-bold text-primary">
                            SELL CART (<?=$nameCustomer ?? '-'?>) => IDR <?=nominal($total)?>
                        </h6>
                    </a>
                    <div class="collapse show" id="collapseCardTable">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" style="font-size:15px;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Material</th>
                                            <th>Type</th>
                                            <th>Carat</th>
                                            <th>Weight</th>
                                            <th>Price/Gr</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cartContents)) { $no=0; foreach($cartContents as $a){ $no++; ?>
                                            <tr>
                                                <td><?=$no?></td>
                                                <td><?=$a['ti_material']?></td>
                                                <td><?=$a['ti_material_type']?></td>
                                                <td><?=$a['ti_carat']?></td>
                                                <td><?=$a['ti_weight']?></td>
                                                <td><?=nominal($a['ti_price'])?></td>
                                                <td><?=nominal($a['ti_price_total'])?></td>
                                                <td>
                                                    <a href="<?=base_url()?>transaction/sell-delete-item/<?=$a['ti_id']?>" class="btn btn-danger btn-circle btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php }} else { ?>
                                            <tr><td colspan="8" class="text-center">No items found</td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <form action="<?=base_url()?>transaction/sell-checkout/">
                                <div class="row">
                                    <div class="col-md-2">
                                        <p>PLUS/MINUS</p>
                                        <select class="form-control" name="operator">
                                            <option value="+">+</option>
                                            <option value="-">-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-10">
                                        <p>BIAYA ADMIN</p>
                                        <input type="number" step="any" class="form-control biayaAdmin" name="biayaAdmin">
                                    </div>
                                </div>

                                <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document" style="top: 84px;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Ready to Checkout?</h5>
                                                <button class="close" type="button" data-dismiss="modal">×</button>
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

                            <a href="#" data-toggle="modal" data-target="#resetModal" class="btn btn-danger btn-icon-split btn-lg mt-3" style="margin-right:13px">
                                <span class="icon text-white-50"><i class="fa fa-times"></i></span>
                                <span class="text">Reset</span>
                            </a>
                            <a href="#" data-toggle="modal" data-target="#checkoutModal" class="btn btn-primary btn-icon-split btn-lg mt-3" style="margin-right:13px">
                                <span class="icon text-white-50"><i class="fa fa-check"></i></span>
                                <span class="text">Checkout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset -->
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="top: 84px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ready to Reset?</h5>
                <button class="close" type="button" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">Select "Reset" below if you are ready to end your cart session.</div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" href="<?=base_url()?>transaction/sell-add-to-cart-reset/?idMaterial=<?=$this->uri->segment(3);?>">Reset</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#materialType").select2();
    $(".select2").select2();
    // Numpad input
    $('.biayaAdmin').keyboard({ layout: 'num', restrictInput:true, preventPaste:true, autoAccept:true });
    $('#weight').keyboard({ layout: 'num', restrictInput:true, preventPaste:true, autoAccept:true });
</script>
