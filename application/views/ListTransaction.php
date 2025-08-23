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
                                        <!-- Sweet Alert Modal Trigger -->
                                        <button class="btn btn-sm btn-warning" onclick="updateAllStatus()"><i class="fa fa-trash"></i> Clear Data</button>
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
    function clearData() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'All transaction data will be cleared and cannot be restored!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, clear it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke backend untuk action clear data
                window.location.href = "<?= base_url('transaction/clearData') ?>";
            }
        });
    }

    function deleteData(no_order){
        Swal.fire({
            title: "Are you sure?",
            text: "The transaction will be deleted and cannot be restored!",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Cancel",
            denyButtonText: `Delete`,
        }).then((result) => {
            if (result.isDenied) {
                window.location.href = "<?= base_url('transaction/delete-transaction/') ?>" + no_order;
            } else {
                Swal.close();
            }
        });
    }

    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('transaction/getTransactions') ?>",
            type: "POST",
        },
        columns: [
            { data: 'no', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false },
            { data: 'transaction' },
            { data: 'no_order' },
            { data: 'status' },
            { data: 'date' },
            { data: 'customer' },
            { data: 'qty' },
            {
                data: 'price_total',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        // Format angka dengan koma
                        return 'IDR ' + new Intl.NumberFormat('id-ID').format(data || 0);
                    }
                    return data;
                }
            }
        ]
    });

    function updateAllStatus() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will update the status of all transactions to SELESAI.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('transaction/updateAllStatus') ?>",
                    method: "POST",
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'All transactions have been updated to SELESAI.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'An error occurred while updating data.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to process the request. Please try again.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
