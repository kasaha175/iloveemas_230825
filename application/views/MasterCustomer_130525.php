<?php 
function nominal($angka){
    $jd = number_format($angka, 2, ',', '.');
    return $jd;
}
?>
<div class="col-md-12" style="margin-top:110px;">
    <div style="color:#fff;margin-top:-30px;">
        <a style="color:#fff;text-decoration:none;" href="<?=base_url()?>dashboard/" class="fa fa-home"></a>
        <a style="color:#fff;text-decoration:none;" href="<?=base_url()?>dashboard/">Dashboard</a> 
        > 
        <a style="color:#fff;text-decoration:none;" href="<?=base_url()?>master/">Master</a> 
        > 
        <a style="color:#fff;text-decoration:none;" href="">Master Customer</a> 
    </div>
    <h3 class="text-center" style="color:#fff">MASTER</h3>
    <h3 class="text-center" style="color:#fff">Master Customer</h3>
    <br>
    <div class="col-md-12" style="padding:0px 150px;">
        <div class="row">
            <div class="col-md-12" style="padding:10px 10px">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Accordion -->
                            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button"
                                aria-expanded="true" aria-controls="collapseCardExample">
                                <h6 class="m-0 font-weight-bold text-primary">Customer Data </h6>
                            </a>
                            
                            <?php if($this->session->userdata('status')=='success'){ ?>
                            <div class="col-md-12 mt-3">
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
                            <!-- Card Content - Collapse -->
                            <div class="collapse show" id="collapseCardExample" style="">
                                <div class="card-body">
                                <a href="<?= base_url('transaction/new-customer/?key=add') ?>" class="text-info float-right mb-2">
                                    <span class="text"><span class="fas fa-user-plus"></span> Add Customer</span>
                                </a>
                                    <div class="table-responsive">
                                    
                                        <table style="overflow:scroll; display:block;"  class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Action</th>
                                                    <!-- <th style="min-width:100px;">Id User</th> -->
                                                    <th style="min-width:100px;">No Order</th>
                                                    <th style="min-width:100px;">Name</th>
                                                    <th style="min-width:100px;">Address</th>
                                                    <th style="min-width:100px;">Resident Address</th>
                                                    <th style="min-width:100px;">Phone</th>
                                                    <th style="min-width:100px;">Date Created</th>
                                                    <th style="min-width:100px;">Created By</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $no=0; foreach($customer as $a){ $no++; ?>
                                                <tr>
                                                    <td><?=$no?></td>
                                                    <td>
                                                    <a href="<?=base_url()?>master/customer/<?=$a->c_id?>/" class="btn btn-primary btn-circle btn-sm mr-2"><i class="fas fa-info"></i>
                                                    <a href="#" data-toggle="modal" data-target="#deleteModal<?=$a->c_id?>" class="btn btn-danger btn-circle btn-sm"><i class="fas fa-trash"></i>
                                                    </td>
                                                     <!-- <td><?=$a->c_id_number?></td> -->
                                                     <td><?=$a->c_no_order?></td>
                                                    <td><?=$a->c_name?></td>
                                                    <td><?=$a->c_address?></td>
                                                    <td><?=$a->c_resident_address?></td>
                                                    <td><?=$a->c_phone?></td>
                                                    <td><?=$a->c_date_created?></td>
                                                    <td><?=$a->u_name?></td>
                                                   
                                                    <div class="modal fade" id="deleteModal<?=$a->c_id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document" style="top: 84px;">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Ready to Delete?</h5>
                                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">Select "Delete" below if you are ready to delete data <b><?=$a->c_no_order?></b> ?</div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                <a class="btn btn-danger" href="<?=base_url()?>master/delete-customer-process/<?=$a->c_id?>/">Delete</a>
                                                    </div> </div> </div> </div>
                                                    
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
                        <a href="<?=base_url()?>/master/" class="btn btn-primary btn-icon-split btn-lg">
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
<script type="text/javascript">
    // Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable();
});
</script>
