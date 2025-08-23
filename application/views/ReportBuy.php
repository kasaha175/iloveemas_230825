<?php 
function nominal($angka){
    $jd = number_format($angka, 0, ',', '.');
    return $jd;
}
?> <div class="col-md-12" style="margin-top:110px;">
  <div style="color:#fff;margin-top:-30px;">
    <a style="color:#fff;text-decoration:none;" href="
			<?=base_url()?>dashboard/" class="fa fa-home">
    </a>
    <a style="color:#fff;text-decoration:none;" href="
			<?=base_url()?>dashboard/">Dashboard </a> > <a style="color:#fff;text-decoration:none;" href="
			<?=base_url()?>report/">Report </a> > <a style="color:#fff;text-decoration:none;" href="">Buy</a>
  </div>
  <h3 class="text-center" style="color:#fff">REPORT</h3>
  <h3 class="text-center" style="color:#fff">Transaction Buy</h3>
  <br>
  <div class="col-md-12" style="padding:0px 150px;">
    <div class="row">
      <div class="col-md-12" style="padding:10px 10px">
        <div class="row">
          <div class="col-md-12">
            <div class="card shadow mb-4">
              <!-- Card Header - Accordion -->
              <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
              </a>
              <!-- Card Content - Collapse -->
              <div class="collapse show" id="collapseCardExample" style="">
                <div class="card-body">
                  <form action="
											<?=base_url()?>report/buy/">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Start Date</label> <?php if(empty($this->input->get('dateStart'))){ ?> <input name="dateStart" required type="date" value="
															<?=date('Y-m-d')?>" class="form-control"> <?php }else{ ?> <input name="dateStart" required type="date" value="
																<?=$this->input->get('dateStart')?>" class="form-control"> <?php } ?>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="">End Date</label> <?php if(empty($this->input->get('dateEnd'))){ ?> <input name="dateEnd" required type="date" value="
																	<?=date('Y-m-d')?>" class="form-control"> <?php }else{ ?> <input name="dateEnd" required type="date" value="
																		<?=$this->input->get('dateEnd')?>" class="form-control"> <?php } ?>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Filter?</label>
                          <input required type="submit" class="btn btn-block btn-primary" value="Filter">
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card shadow mb-4">
              <!-- Card Header - Accordion -->
              <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Transaction Data</h6>
              </a>
              <!-- Card Content - Collapse -->
              <div class="collapse show" id="collapseCardExample" style="">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle shadow-sm" id="dataTable" width="100%">
                      <thead class="text-center" style="background: linear-gradient(90deg,#4e73df,#224abe); color:#fff;">
                        <tr>
                          <th>No</th>
                          <th style="min-width:150px;">Action</th>
                          <th style="min-width:120px;">No Order</th>
                          <th style="min-width:100px;">Status</th>
                          <th style="min-width:120px;">Date</th>
                          <th style="min-width:120px;">Created By</th>
                          <th style="min-width:120px;">Receive By</th>
                          <th style="min-width:150px;">Customer</th>
                          <th class="text-right">Qty</th>
                          <th class="text-right" style="min-width:120px;">Price Total</th>
                        </tr>
                      </thead>
                      <tbody> <?php $no=0; foreach($data as $a){ $no++; ?> <tr>
                          <td class="text-center"> <?=$no?> </td>
                          <td class="text-center">
                            <div class="btn-group">
                                <!-- Action utama -->
                                <a href="<?=base_url()?>report/buy/<?=$a->t_id?>/" 
                                    class="btn btn-sm btn-primary" data-toggle="tooltip" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="<?=base_url()?>report/buy-print/<?=$a->t_id?>/" 
                                    class="btn btn-sm btn-success" data-toggle="tooltip" title="Print">
                                    <i class="fas fa-print"></i>
                                </a>

                                <!-- Dropdown untuk aksi lainnya -->
                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" 
                                        data-toggle="dropdown">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item text-warning" href="#" 
                                    data-toggle="modal" 
                                    data-target="#modalEdit" 
                                    onclick="openModalEdit(<?= $a->t_id ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a class="dropdown-item text-danger" href="#" 
                                    onclick="openDeleteModal(<?=$a->t_id?>, '<?=$a->t_no_order?>')">
                                    <i class="fas fa-trash"></i> Delete
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <!-- Tombol Baru -->
                                    <a class="dropdown-item text-danger" href="#" 
                                    onclick="openVoidModal(<?=$a->t_id?>, '<?=$a->t_no_order?>')">
                                    <i class="fas fa-ban"></i> Void
                                    </a>
                                    <a class="dropdown-item text-info" href="#" 
                                    onclick="openRevisionModal(<?=$a->t_id?>, '<?=$a->t_no_order?>')">
                                    <i class="fas fa-sync-alt"></i> Revision
                                    </a>
                                </div>
                                </div>
                          </td>
                          <td class="text-center"> <?=$a->t_no_order?> </td>
                          <td class="text-center">
                            <?php 
                                if ($a->t_status == 'SELESAI') { 
                                    echo '<span class="badge badge-success">SELESAI</span>'; 
                                } elseif ($a->t_status == 'CHECKOUT') { 
                                    echo '<span class="badge badge-primary">CHECKOUT</span>'; 
                                } elseif ($a->t_status == 'VOID') { 
                                    echo '<span class="badge badge-danger">VOID</span>'; 
                                } elseif ($a->t_status == 'REVISION') { 
                                    echo '<span class="badge badge-warning text-dark">REVISION</span>'; 
                                } else { 
                                    echo '<span class="badge badge-secondary">'.$a->t_status.'</span>'; 
                                }
                            ?>
                          </td>
                          <td> <?=date('d-m-Y', strtotime($a->t_date_created))?> </td>
                          <td> <?=$a->nameCreator?> </td>
                          <td> <?=$a->nameReceive?> </td>
                          <td> <?=$a->nameCustomer?> </td>
                          <td class="text-center"> <?=$a->t_qtt?> </td>
                          <td class="text-right">IDR <?=nominal($a->t_price_total)?> </td>
                          <!-- Modal Delete -->
                          <div class="modal fade" id="deleteModal
																				<?=$a->t_id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="top: 84px;">
                              <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title">Confirm Delete</h5>
                                  <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                  </button>
                                </div>
                                <div class="modal-body"> Are you sure want to delete transaction <b> <?=$a->t_no_order?> </b> ? </div>
                                <div class="modal-footer">
                                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                  <a class="btn btn-danger" href="
																								<?=base_url()?>transaction/buy-delete-transaction/
																								<?=$a->t_id?>">Delete </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </tr> <?php } ?> </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mt-3">
            <a href="
													<?=base_url()?>report/" class="btn btn-primary btn-icon-split btn-lg">
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="top: 84px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Perubahan</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="">
          <input type="hidden" name="type" id="type" value="buy">
          <input type="hidden" name="id" value="" id="edit_id">
          <div class="form-group">
            <label for="">Alasan Perubahan</label>
            <textarea name="alasan" id="alasan" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <label for="">Kata Sandi</label>
            <input type="password" name="password" id="password" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <div class="btn btn-primary" onclick="submitKonfirmasi()">
          <i class="fa fa-save"></i> Submit
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="globalModalDelete" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top: 84px;">
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

<!-- Modal Global Void -->
<div class="modal fade" id="globalModalVoid" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Void</h5>
        <button class="close text-white" type="button" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body" id="voidMessage">
        <!-- isi via JS -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a id="voidConfirmBtn" class="btn btn-danger" href="#">Void</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Global Revision -->
<div class="modal fade" id="globalModalRevision" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Request Revision</h5>
        <button class="close text-white" type="button" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <form id="revisionForm" method="post">
          <input type="hidden" id="revision_id" name="revision_id">
          <input type="hidden" id="revision_no_order" name="revision_no_order">
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

<script>

    function openDeleteModal(id, noOrder) {
        $('#deleteMessage').html('Are you sure want to <b>DELETE</b> transaction <b>' + noOrder + '</b>?');
        $('#deleteConfirmBtn').attr('href', "<?=base_url()?>transaction/buy-delete-transaction/" + id);
        $('#globalModalDelete').modal('show');
    }

    function openVoidModal(id, noOrder) {
        $('#voidMessage').html('Are you sure want to <b>VOID</b> transaction <b>' + noOrder + '</b>?');
        $('#voidConfirmBtn').attr('href', "<?=base_url()?>transaction/buy-void/" + id);
        $('#globalModalVoid').modal('show');
    }

    function openRevisionModal(id, noOrder) {
      $('#revisionMessage').html(
          'Request revision for transaction <b>ID: ' + id + '</b> with No Order <b>' + noOrder + '</b>'
      );

      // isi hidden input
      $('#revision_id').val(id);
      $('#revision_no_order').val(noOrder);

      // arahkan ke controller redirect dengan parameter no_order
      $('#revisionForm').attr('action', "<?= base_url('transaction/redirect/') ?>" + noOrder);

      $('#globalModalRevision').modal('show');
  }

  function submitKonfirmasi() {
    Swal.fire({
      title: 'Mohon Tunggu Sebentar',
      html: ' < i class = "fa fa-spin fa-refresh" > < /i>',
      showConfirmButton: false,
    });
    if ($('#alasan').val() == null) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Alasan Tidak Boleh Kosong',
      });
    }
    $.ajax({
      url: " < ? = base_url('transaction/confirm-edit') ? > ",
      method: "POST",
      data: {
        type: $('#type').val(),
        id: $('#edit_id').val(),
        alasan: $('#alasan').val(),
        password: $('#password').val(),
      },
      success: function(data) {
        var res = JSON.parse(data);
        console.log(res);
        if (res.status == 'gagal') {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Kata Sandi Salah!',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Mohon Tunggu Sebentar',
          });
          window.location.href = " < ? = base_url('transaction/redirect/') ? > "+res.no_transaksi
        }
      }
    });
  }

  function openModalEdit(t_id) {
    $('#edit_id').val(t_id);
    // $('#modalEdit').modal('toggle');
  }
  $(document).ready(function() {
    $('#dataTable').DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
        [10, 25, 50, 100, -1],
        ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
    ],
    buttons: [
        {
            extend: 'copyHtml5',
            text: '<i class="fa fa-clipboard"></i> Copy',
            titleAttr: 'Copy',
            className: 'btn btn-sm btn-outline-primary'
        },
        {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            titleAttr: 'Excel',
            className: 'btn btn-sm btn-outline-success'
        },
        {
            extend: 'csvHtml5',
            text: '<i class="fa fa-file-text-o"></i> CSV',
            titleAttr: 'CSV',
            className: 'btn btn-sm btn-outline-info'
        },
        {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'A4',
            text: '<i class="fa fa-file-pdf-o"></i> PDF',
            titleAttr: 'PDF',
            className: 'btn btn-sm btn-outline-danger'
        },
        {
            extend: 'pageLength',
            titleAttr: 'Record Show',
            className: 'btn btn-sm btn-outline-secondary'
        }
    ]
});

    $('#dataTable_filter').addClass('col-md-12');
  });
</script>