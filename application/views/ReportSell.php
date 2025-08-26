<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
function nominal($angka){ return number_format((float)$angka, 0, ',', '.'); }
$dateStart = $this->input->get('dateStart') ?: date('Y-m-01');
$dateEnd   = $this->input->get('dateEnd')   ?: date('Y-m-t');
?>
<style>
  /* ===== SCOPED supaya tidak bentrok UserTemplate ===== */
  .report-sell-scope .master-wrap{ margin-top: calc(var(--topbar-h) + 12px); padding: clamp(12px, 2vw, 20px); }
  .report-sell-scope .master-container{ max-width: 1480px; margin-inline:auto; }

  .report-sell-scope .master-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .report-sell-scope .master-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .report-sell-scope .master-crumbs a:hover{ color:var(--blue-light); text-decoration:underline; }
  .report-sell-scope .master-crumbs .sep{ color:#7a8eb8; }

  .report-sell-scope .master-head h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .report-sell-scope .master-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  .report-sell-scope .card{ border-radius:16px; border:1px solid #e6eefc; }
  .report-sell-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border-bottom-color:#dfeaff; border-top-left-radius:16px; border-top-right-radius:16px;
  }
  .report-sell-scope .card-header h6{ color:#0e204a; font-weight:800; }

  /* header tabel satu baris; body boleh wrap */
  .report-sell-scope .table thead th{ white-space:nowrap; text-align:center; }
  .report-sell-scope table th, .report-sell-scope table td{ min-width:unset !important; }
  .report-sell-scope .text-wrap{ white-space:normal !important; }
  @media (min-width: 1200px){ .report-sell-scope .table-responsive{ overflow-x:visible; } }

  .report-sell-scope .master-actions{ margin-top:12px; }
  .report-sell-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px; background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px; text-decoration:none;
    box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .report-sell-scope .btn-back:hover{ border-color:#cfe0ff; }

  /* ==== FIX LAYERING: Swal di atas modal bootstrap (global, jangan dibatasi scope) ==== */
  .modal-backdrop { z-index: 1190 !important; }
  .modal          { z-index: 1200 !important; }
  .swal2-container.swal2-on-top { z-index: 1600 !important; }
</style>

<div class="report-sell-scope">
  <div class="master-wrap">
    <div class="master-container">

      <!-- Breadcrumb -->
      <nav class="master-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('report') ?>">Report</a>
        <span class="sep">/</span>
        <span>Sell</span>
      </nav>

      <!-- Header -->
      <header class="master-head">
        <h3>Report</h3>
        <p>Transaction Sell</p>
      </header>

      <!-- Filter -->
      <section class="mb-3">
        <div class="card shadow-sm">
          <a href="#filterCollapse" class="d-block card-header py-3" data-toggle="collapse" role="button"
             aria-expanded="true" aria-controls="filterCollapse">
            <h6 class="m-0 font-weight-bold">Filter Data</h6>
          </a>
          <div class="collapse show" id="filterCollapse">
            <div class="card-body">
              <form id="filterForm" action="<?= base_url('report/sell/') ?>" method="get">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="dateStart">Start Date</label>
                    <input id="dateStart" name="dateStart" required type="date" value="<?= html_escape($dateStart) ?>" class="form-control">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="dateEnd">End Date</label>
                    <input id="dateEnd" name="dateEnd" required type="date" value="<?= html_escape($dateEnd) ?>" class="form-control">
                  </div>
                  <div class="form-group col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>

      <!-- Tabel data -->
      <section>
        <div class="card shadow-sm">
          <a href="#dataCollapse" class="d-block card-header py-3" data-toggle="collapse" role="button"
             aria-expanded="true" aria-controls="dataCollapse">
            <h6 class="m-0 font-weight-bold">Transaction Data</h6>
          </a>
          <div class="collapse show" id="dataCollapse">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th class="text-nowrap">Action</th>
                      <th class="text-nowrap">No Order</th>
                      <th class="text-nowrap">Status</th>
                      <th class="text-nowrap">Date</th>
                      <th class="text-wrap">Created By</th>
                      <th class="text-wrap">Receive By</th>
                      <th class="text-wrap">Customer</th>
                      <th class="text-nowrap">Qtt</th>
                      <th class="text-nowrap">Price Total</th>
                    </tr>
                  </thead>
                  <tbody></tbody><!-- server-side -->
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Back -->
      <div class="master-actions">
        <a href="<?= base_url('report') ?>" class="btn-back">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Kembali ke Report
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Modal DELETE (global) -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteTitle" aria-hidden="true">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteTitle">Ready to Delete?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">Select "Delete" below if you are ready to delete transaction <b class="js-no-order"></b> ?</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <a class="btn btn-danger js-delete-link" href="#">Delete</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal KONFIRMASI EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="editTitle" aria-hidden="true">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTitle">Konfirmasi Perubahan</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="confirmForm" method="post" action="javascript:void(0);">
          <input type="hidden" name="type" id="type" value="sell">
          <input type="hidden" name="id" id="edit_id" value="">
          <div class="form-group">
            <label for="alasan">Alasan Perubahan</label>
            <textarea name="alasan" id="alasan" class="form-control" required></textarea>
          </div>
          <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" name="password" id="password" class="form-control" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="button" onclick="submitKonfirmasi()"><i class="fa fa-save"></i> Submit</button>
      </div>
    </div>
  </div>
</div>

<script>
(function ReportSellPage(){
  const BASE_URL  = "<?= base_url() ?>";
  const CSRF_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  let   CSRF_HASH = "<?= $this->security->get_csrf_hash(); ?>";

  // util
  function onReady(fn){ document.readyState!=='loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }
  function ensureDT(fn){
    let n=0, t=setInterval(function(){
      if (window.jQuery && jQuery.fn && jQuery.fn.DataTable){ clearInterval(t); fn(jQuery); }
      else if(++n>200){ clearInterval(t); console.error('jQuery/DataTables not found'); }
    },50);
  }
  // SweetAlert helper: selalu di atas modal
  function swalert(cfg){
    if (!cfg) cfg = {};
    const merged = Object.assign({ customClass:{ container:'swal2-on-top' }, backdrop:true }, cfg);
    (window.Swal && Swal.fire) ? Swal.fire(merged) : alert(cfg.title || cfg.text || '');
  }

  // global helpers
  window.openModalEdit = function(id){ jQuery('#edit_id').val(id); jQuery('#modalEdit').modal('show'); };
  window.submitKonfirmasi = function(){
    const $ = jQuery;
    const alasan = ($('#alasan').val() || '').trim();
    if (!alasan){ swalert({icon:'error',title:'Gagal',text:'Alasan tidak boleh kosong.'}); return; }

    swalert({title:'Mohon Tunggu Sebentar', html:'<i class="fa fa-sync fa-spin"></i>', showConfirmButton:false, allowOutsideClick:false});
    $.ajax({
      url: BASE_URL + "transaction/confirm-edit",
      type: "POST",
      data: {
        type: $('#type').val(),
        id: $('#edit_id').val(),
        alasan: alasan,
        password: $('#password').val(),
        [CSRF_NAME]: CSRF_HASH
      }
    }).done(function(resp){
      let res={}; try{ res = typeof resp==='object' ? resp : JSON.parse(resp); }catch(_){}
      if(!res || res.status==='gagal'){
        swalert({icon:'error',title:'Gagal',text:'Kata sandi salah atau permintaan tidak valid.'});
        return;
      }
      // tutup modal dulu supaya layering rapi
      $('#modalEdit').modal('hide');
      swalert({icon:'success',title:'Berhasil',text:'Mohon tunggu sebentar...'});
      window.location.href = BASE_URL + "transaction/redirect/" + (res.no_transaksi || "");
    }).fail(function(){
      swalert({icon:'error',title:'Gagal',text:'Terjadi kesalahan jaringan.'});
    });
  };

  onReady(function(){
    ensureDT(function($){
      // pastikan modal ada di <body> untuk hindari stacking context aneh
      $('#modalEdit, #deleteModal').appendTo('body');

      // 1 modal delete untuk semua row
      $(document).on('click', '.js-open-delete', function(e){
        e.preventDefault();
        const id = $(this).data('id');
        const no = $(this).data('no');
        $('#deleteModal .js-no-order').text(no);
        $('#deleteModal .js-delete-link').attr('href', BASE_URL + "transaction/sell-delete-transaction/" + id);
        $('#deleteModal').modal('show');
      });

      const hasButtons = $.fn.dataTable && $.fn.dataTable.Buttons;
      const table = $('#dataTable').DataTable({
        serverSide: true,
        processing: true,
        autoWidth: false,
        scrollX: false,               // tidak pakai horizontal scroll; kolom di-wrap sesuai CSS
        order: [[4,'desc']],          // Date
        ajax: {
          url: "<?= base_url('report/sell-dt') ?>",
          type: "POST",
          data: function(d){
            d.dateStart = $('#dateStart').val();
            d.dateEnd   = $('#dateEnd').val();
            d[CSRF_NAME]= CSRF_HASH;
          },
          dataSrc: function(json){
            if (json && typeof json[CSRF_NAME] !== 'undefined') CSRF_HASH = json[CSRF_NAME];
            return json && json.data ? json.data : [];
          }
        },
        dom: 'Bfrtip',
        lengthMenu: [[10,25,50,100,-1], ['10 rows','25 rows','50 rows','100 rows','Show all']],
        buttons: hasButtons ? [
          { extend:'copyHtml5',  text:'<i class="fas fa-clipboard"></i> Copy', className:'btn btn-default btn-flat',
            exportOptions:{ columns:[0,2,3,4,5,6,7,8,9] } },
          { extend:'pdfHtml5',   orientation:'landscape', pageSize:'A4',
            text:'<i class="fas fa-file-pdf"></i> PDF', className:'btn btn-default btn-flat',
            exportOptions:{ columns:[0,2,3,4,5,6,7,8,9] } },
          { extend:'excelHtml5', text:'<i class="fas fa-file-excel"></i> Excel', className:'btn btn-default btn-flat',
            exportOptions:{ columns:[0,2,3,4,5,6,7,8,9] } },
          { extend:'csvHtml5',   text:'<i class="fas fa-file-csv"></i> CSV', className:'btn btn-default btn-flat',
            exportOptions:{ columns:[0,2,3,4,5,6,7,8,9] } },
          { extend:'pageLength', className:'selectTable btn btn-default btn-flat' }
        ] : [],
        columnDefs: [
          { targets: [1,2,3,4,8,9], className: 'text-nowrap' }, // aksi, kode, tanggal, qty, harga
          { targets: [5,6,7],       className: 'text-wrap'    }  // nama panjang -> wrap
        ]
      });

      // filter tanpa reload halaman
      $('#filterForm').on('submit', function(e){
        e.preventDefault();
        table.ajax.reload();
      });

      // kosmetik & fix kolom saat modal muncul/hilang
      $('#dataTable_filter').addClass('col-md-12');
      $(document).on('shown.bs.modal hidden.bs.modal', function(){ table.columns.adjust(); });
    });
  });
})();
</script>
