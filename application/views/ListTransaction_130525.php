<?php 
function nominal($valuengka){
    $jd = number_format($valuengka, 0, ',', '.');
    return $jd;
}
?>
<div class="col-md-12" style="margin-top: 110px;">
    <div style="color: #fff; margin-top: -30px;">
        <a style="color: #fff; text-decoration: none;" href="<?=base_url()?>dashboard/" class="fa fa-home"></a>
        <a style="color: #fff; text-decoration: none;" href="<?=base_url()?>dashboard/">Dashboard</a>
        >
        <a style="color: #fff; text-decoration: none;" href="<?=base_url()?>transaction-list/">Transaction</a>
    </div>
    <h3 class="text-center" style="color: #fff;">Transaction</h3>
    <br />
    <div class="col-md-12" style="padding: 0px 150px;">
        <div class="row">
            

            <div class="col-md-12" style="padding: 10px 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Accordion -->
                            <div class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="m-0 font-weight-bold text-primary">Transaction Data</h6>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="<?= base_url('transaction') ?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Transaction</a>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- Card Content - Collapse -->
                            <div class="collapse show" id="collapseCardExample" style="">
                                <div class="card-body">
                                <?php if($this->session->userdata('status')=='success'){ ?>
                            <div style="margin: 15px 0px">
                            <div class="alert alert-success alert-dismissible m-0">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?=$this->session->userdata('message')?>
                            </div>
                            </div>
                            <?php } 
                            $data_session = array(
                                'status' => '',
                                'message' => "",
                            );
                            $this->session->set_userdata($data_session); ?>
                                    <div class="table-responsive">
                                        
                                        <table style="overflow: scroll;" class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead style="text-align: center">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Action</th>
                                                    <th>Transaction</th>
                                                    <th>No Order</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Customer</th>
                                                    <th>Qty</th>
                                                    <th>Price Total</th>
                                                </tr>
                                            </thead>
                                            
                                        
                                            <tbody>
                                                <?php foreach($transaction as $key => $value){ ?>
                                                <tr>
                                                    <td style="text-align: center"><?=$key+1?></td>
                                                    <td style="text-align: center">
                                                        <a href="<?= base_url('transaction/redirect/'.$value->t_no_order) ?>"  class="btn btn-primary btn-circle btn-sm" data-toggle="tooltip" data-placement="top" title="Lanjutkan Transaction"><i class="fa fa-arrow-right"></i></a>
                                                        <!-- <div onclick="deleteData('<?= $value->t_no_order ?>')" class="btn btn-danger btn-circle btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus Transaksi"><i class="fa fa-trash"></i></div> -->
                                                    </td>
                                                    <td style="text-align: center"><?=$value->t_type?></td>
                                                    <td><?=$value->t_no_order?></td>
                                                    <td>ONPROCESS</td>
                                                    <td><?=$value->t_date_created?></td>
                                                    <td><?=$value->t_paid_by?></td>
                                                    <td style="text-align: center"><?=$value->t_qtt?></td>
                                                    <td style="text-align: right">
                                                        IDR
                                                        <?=nominal($value->t_price_total)?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <a href="<?=base_url()?>dashboard/" class="btn btn-primary btn-icon-split btn-lg">
                            <span class="icon text-white-50">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            <span class="text">Back</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function deleteData(no_order){
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Transaksi yang dihapus tidak dapat dikembalikan!",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Batal Hapus",
            denyButtonText: `Hapus`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isDenied) {
                window.location.href = "<?= base_url('transaction/delete-transaction/') ?>"+no_order;
            } else{
                Swal.close();
            }
        });

    }
</script>