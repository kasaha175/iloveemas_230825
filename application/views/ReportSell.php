<?php 
function nominal($angka){
    $jd = number_format($angka, 0, ',', '.');
    return $jd;
}
?>
<div class="col-md-12" style="margin-top:110px;">
  <div style="color:#fff;margin-top:-30px;">
    <a style="color:#fff;text-decoration:none;" href="<?=base_url()?>dashboard/" class="fa fa-home"></a>
    <a style="color:#fff;text-decoration:none;" href="<?=base_url()?>dashboard/">Dashboard</a> > 
    <a style="color:#fff;text-decoration:none;" href="<?=base_url()?>report/">Report</a> > 
    <a style="color:#fff;text-decoration:none;" href="">Sell</a> 
  </div>
  <h3 class="text-center text-white">REPORT</h3>
  <h3 class="text-center text-white">Transaction Sell</h3>
  <br>

  <div class="col-md-12" style="padding:0px 150px;">

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
      <a href="#collapseFilter" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true">
        <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
      </a>
      <div class="collapse show" id="collapseFilter">
        <div class="card-body">
          <form action="<?=base_url()?>report/sell/">
            <div class="row">
              <div class="col-md-4">
                <label>Start Date</label>
                <input name="dateStart" required type="date" 
                  value="<?=empty($this->input->get('dateStart')) ? date('Y-m-d') : $this->input->get('dateStart')?>"
                  class="form-control">
              </div>
              <div class="col-md-4">
                <label>End Date</label>
                <input name="dateEnd" required type="date" 
                  value="<?=empty($this->input->get('dateEnd')) ? date('Y-m-d') : $this->input->get('dateEnd')?>"
                  class="form-control">
              </div>
              <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-primary">Filter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Transaction Table -->
    <div class="card shadow-sm">
      <a href="#collapseTable" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true">
        <h6 class="m-0 font-weight-bold text-primary">Transaction Data</h6>
      </a>
      <div class="collapse show" id="collapseTable">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped align-middle shadow-sm" id="dataTable" width="100%">
              <thead class="text-center" style="background: linear-gradient(90deg,#4e73df,#224abe); color:#fff;">
                <tr>
                  <th>No</th>
                  <th style="min-width:150px;">Action</th>
                  <th>No Order</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Created By</th>
                  <th>Receive By</th>
                  <th>Customer</th>
                  <th class="text-right">Qtt</th>
                  <th class="text-right">Price Total</th>
                </tr>
              </thead>
              <tbody>
                <?php $no=0; foreach($data as $a){ $no++; ?>
                <tr>
                  <td class="text-center"><?=$no?></td>
                  <td>
                    <div class="btn-group">
                      <a href="<?=base_url()?>report/sell/<?=$a->t_id?>/" class="btn btn-sm btn-primary" title="Detail">
                        <i class="fas fa-info-circle"></i>
                      </a>
                      <a href="<?=base_url()?>report/sell-print/<?=$a->t_id?>/" class="btn btn-sm btn-success" title="Print">
                        <i class="fas fa-print"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                        <span class="sr-only">Toggle</span>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item text-warning" href="#" onclick="openEditModal(<?=$a->t_id?>)">
                          <i class="fas fa-edit"></i> Edit
                        </a>
                        <a class="dropdown-item text-danger" href="#" onclick="openDeleteModal(<?=$a->t_id?>,'<?=$a->t_no_order?>')">
                          <i class="fas fa-trash"></i> Delete
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="openVoidModal(<?=$a->t_id?>,'<?=$a->t_no_order?>')">
                          <i class="fas fa-ban"></i> Void
                        </a>
                        <a class="dropdown-item text-info" href="#" onclick="openRevisionModal(<?=$a->t_id?>,'<?=$a->t_no_order?>')">
                          <i class="fas fa-sync-alt"></i> Revision
                        </a>
                      </div>
                    </div>
                  </td>
                  <td><?=$a->t_no_order?></td>
                  <td class="text-center">
                    <?php if($a->t_status == 'SELESAI'){ ?>
                      <span class="badge badge-success"><?=$a->t_status?></span>
                    <?php } elseif($a->t_status == 'CHECKOUT'){ ?>
                      <span class="badge badge-warning"><?=$a->t_status?></span>
                    <?php } elseif($a->t_status == 'VOID'){ ?>
                      <span class="badge badge-danger"><?=$a->t_status?></span>
                    <?php } elseif($a->t_status == 'REVISION'){ ?>
                      <span class="badge badge-info"><?=$a->t_status?></span>
                    <?php } else { ?>
                      <span class="badge badge-secondary"><?=$a->t_status?></span>
                    <?php } ?>
                  </td>
                  <td><?=date('d-m-Y', strtotime($a->t_date_created))?></td>
                  <td><?=$a->nameCreator?></td>
                  <td><?=$a->nameReceive?></td>
                  <td><?=$a->nameCustomer?></td>
                  <td class="text-center"><?=$a->t_qtt?></td>
                  <td class="text-right">IDR <?=nominal($a->t_price_total)?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12 mt-3">
      <a href="<?=base_url()?>report/" class="btn btn-primary btn-icon-split btn-lg">
        <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Back</span>
      </a>
    </div>
  </div>
</div>

<!-- ================== MODALS ================== -->
<!-- Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top: 84px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Perubahan</h5>
        <button class="close" type="button" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <form id="formEdit" method="post" action="<?=base_url('transaction/confirm-edit')?>">
          <input type="hidden" name="type" value="sell">
          <input type="hidden" name="id" id="edit_id">
          <div class="form-group">
            <label>Alasan Perubahan</label>
            <textarea name="alasan" id="alasan" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <label>Kata Sandi</label>
            <input type="password" name="password" id="password" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" onclick="submitKonfirmasi()"><i class="fa fa-save"></i> Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Delete</h5>
        <button class="close text-white" type="button" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body" id="deleteMessage"></div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a id="deleteConfirmBtn" class="btn btn-danger" href="#">Delete</a>
      </div>
    </div>
  </div>
</div>

<!-- Void -->
<div class="modal fade" id="modalVoid" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Void</h5>
        <button class="close text-white" type="button" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body" id="voidMessage"></div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a id="voidConfirmBtn" class="btn btn-danger" href="#">Void</a>
      </div>
    </div>
  </div>
</div>

<!-- Revision -->
<div class="modal fade" id="modalRevision" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Request Revision</h5>
        <button class="close text-white" type="button" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <form id="revisionForm" method="post">
          <p id="revisionMessage"></p>
          <div class="form-group">
            <label>Reason for Revision</label>
            <textarea name="revision_reason" class="form-control" required></textarea>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <button class="btn btn-info" type="submit"><i class="fas fa-save"></i> Submit Revision</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ================== SCRIPT ================== -->
<script>
function openEditModal(id){ $('#edit_id').val(id); $('#modalEdit').modal('show'); }

function openDeleteModal(id,noOrder){
  $('#deleteMessage').html('Are you sure want to <b>DELETE</b> transaction <b>'+noOrder+'</b>?');
  $('#deleteConfirmBtn').attr('href',"<?=base_url()?>transaction/sell-delete-transaction/"+id);
  $('#modalDelete').modal('show');
}

function openVoidModal(id,noOrder){
  $('#voidMessage').html('Are you sure want to <b>VOID</b> transaction <b>'+noOrder+'</b>?');
  $('#voidConfirmBtn').attr('href',"<?=base_url()?>transaction/sell-void/"+id);
  $('#modalVoid').modal('show');
}

function openRevisionModal(id, noOrder) {
    $('#revisionMessage').html(
        'Request revision for transaction <b>ID: ' + id + '</b> with No Order <b>' + noOrder + '</b>'
    );

    // arahkan ke redirectTransaction
    $('#revisionForm').attr('action', "<?= base_url('transaction/redirect/') ?>" + noOrder);

    // pastikan id modal sesuai dengan HTML di reportsell.php
    $('#modalRevision').modal('show');
}

function submitKonfirmasi(){
  $("#formEdit").submit();
}

$(document).ready(function(){
  $('#dataTable').DataTable({
    dom:'Bfrtip',
    lengthMenu:[[10,25,50,100,-1],['10 rows','25 rows','50 rows','100 rows','Show all']],
    buttons:[
      {extend:'copyHtml5',text:'<i class="fa fa-clipboard"></i> Copy',className:'btn btn-sm btn-outline-primary'},
      {extend:'excelHtml5',text:'<i class="fa fa-file-excel-o"></i> Excel',className:'btn btn-sm btn-outline-success'},
      {extend:'csvHtml5',text:'<i class="fa fa-file-text-o"></i> CSV',className:'btn btn-sm btn-outline-info'},
      {extend:'pdfHtml5',orientation:'landscape',pageSize:'A4',text:'<i class="fa fa-file-pdf-o"></i> PDF',className:'btn btn-sm btn-outline-danger'},
      {extend:'pageLength',className:'btn btn-sm btn-outline-secondary'}
    ]
  });
});
</script>
