<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
function nominal($angka){ return number_format((float)$angka, 0, ',', '.'); }
$dateStart = $this->input->get('dateStart') ?: date('Y-m-01');
$dateEnd   = $this->input->get('dateEnd')   ?: date('Y-m-t');
?>
<style>
  /* ===== SCOPED ke halaman ini ===== */
  .report-buy-scope .master-wrap{ margin-top: calc(var(--topbar-h) + 12px); padding: clamp(12px, 2vw, 20px); }
  .report-buy-scope .master-container{ max-width:1480px; margin-inline:auto; }

  .report-buy-scope .master-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .report-buy-scope .master-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .report-buy-scope .master-crumbs a:hover{ color:var(--blue-light); text-decoration:underline; }
  .report-buy-scope .master-crumbs .sep{ color:#7a8eb8; }

  .report-buy-scope .master-head h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .report-buy-scope .master-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  .report-buy-scope .card{ border-radius:16px; border:1px solid #e6eefc; }
  .report-buy-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border-bottom-color:#dfeaff; border-top-left-radius:16px; border-top-right-radius:16px;
  }
  .report-buy-scope .card-header h6{ color:#0e204a; font-weight:800; }

  /* Head tabel 1 baris, body boleh wrap */
  .report-buy-scope .table thead th{ white-space:nowrap; text-align:center; }
  .report-buy-scope table th, .report-buy-scope table td{ min-width:unset !important; }
  .report-buy-scope .text-wrap{ white-space:normal !important; }
  @media (min-width:1200px){ .report-buy-scope .table-responsive{ overflow-x:visible; } }

  .report-buy-scope .master-actions{ margin-top:12px; }
  .report-buy-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px; background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px; text-decoration:none;
    box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .report-buy-scope .btn-back:hover{ border-color:#cfe0ff; }

  /* ==== Modal & SweetAlert theming (aman & ringan) ==== */
  .swal2-container{ z-index:1300 !important; }
  .swal2-popup{ border-radius:16px !important; box-shadow:0 20px 60px rgba(0,0,0,.18); }
  #modalEdit .modal-content, #deleteModal .modal-content{ border-radius:16px; box-shadow:0 18px 50px rgba(0,0,0,.18); }
  #modalEdit .modal-header, #deleteModal .modal-header{
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border-bottom:1px solid #dfeaff;
  }
  #modalEdit .modal-title, #deleteModal .modal-title{ color:#0e204a; font-weight:800; }
</style>

<div class="report-buy-scope">
  <div class="master-wrap">
    <div class="master-container">

      <!-- Breadcrumb -->
      <nav class="master-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('report') ?>">Report</a>
        <span class="sep">/</span>
        <span>Buy</span>
      </nav>

      <!-- Header -->
      <header class="master-head">
        <h3>Report</h3>
        <p>Transaction Buy</p>
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
              <form id="filterForm" action="<?= base_url('report/buy/') ?>" method="get">
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
          <input type="hidden" name="type" id="type" value="buy">
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
(function ReportBuyPage(){
  const BASE_URL  = "<?= base_url() ?>";
  const CSRF_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  let   CSRF_HASH = "<?= $this->security->get_csrf_hash(); ?>";

  // Tunggu jQuery + DataTables + (opsional) SweetAlert2 siap
  function ensureLibs(cb){
    let tries = 0;
    const t = setInterval(function(){
      const hasJQ  = !!(window.jQuery);
      const hasDT  = !!(hasJQ && jQuery.fn && jQuery.fn.DataTable);
      const hasSw  = !!window.Swal; // bisa saja false; kita sediakan fallback
      if (hasJQ && hasDT){
        clearInterval(t);
        cb(jQuery, hasSw ? window.Swal : null);
      } else if (++tries > 240){ // ~12s
        clearInterval(t);
        console.warn('Some libs missing. Proceeding without SweetAlert if needed.');
        cb(window.jQuery || undefined, null);
      }
    }, 50);
  }

  document.addEventListener('DOMContentLoaded', function(){
    ensureLibs(function($, SwalLib){
      if (!$) { console.error('jQuery not loaded'); return; }

      // SweetAlert wrapper (fallback ke alert/confirm jika Swal belum ada)
      const Alert = SwalLib ? SwalLib.mixin({
        buttonsStyling:false,
        customClass:{ confirmButton:'btn btn-primary', cancelButton:'btn btn-secondary ml-2' }
      }) : {
        fire: (o)=> {
          const msg = (o && (o.title || o.text)) ? [o.title||'', o.text||''].filter(Boolean).join('\n') : '';
          if (o && (o.showCancelButton || o.icon==='warning' || o.icon==='question')) {
            const ok = window.confirm(msg || 'Lanjutkan?');
            return Promise.resolve({isConfirmed: ok});
          }
          window.alert(msg || 'Selesai');
          return Promise.resolve({});
        },
        // agar pemanggil bisa cek
        __fallback: true
      };

      // Pastikan modal berada di <body> (hindari z-index bentrok)
      $('#modalEdit, #deleteModal').appendTo('body');

      // Helpers global untuk aksi tabel
      window.openModalEdit = function(id){
        $('#edit_id').val(id);
        $('#modalEdit').modal('show');
      };

      window.submitKonfirmasi = function(){
        const alasan = ($('#alasan').val() || '').trim();
        if (!alasan){
          Alert.fire({icon:'error', title:'Gagal', text:'Alasan tidak boleh kosong.'});
          return;
        }

        if (SwalLib){
          Alert.fire({
            title:'Mohon Tunggu',
            html:'<div class="spinner-border" role="status" style="width:1.6rem;height:1.6rem;"></div>',
            showConfirmButton:false,
            allowOutsideClick:false,
            didOpen:()=> SwalLib.showLoading()
          });
        }

        $.post({
          url: BASE_URL + "transaction/confirm-edit",
          data:{
            type: $('#type').val(),
            id: $('#edit_id').val(),
            alasan: alasan,
            password: $('#password').val(),
            [CSRF_NAME]: CSRF_HASH
          }
        }).done(function(resp){
          let res = {}; try{ res = typeof resp==='object' ? resp : JSON.parse(resp); }catch(_){}
          if(!res || res.status==='gagal'){
            Alert.fire({icon:'error', title:'Gagal', text:'Kata sandi salah atau permintaan tidak valid.'});
            return;
          }
          Alert.fire({icon:'success', title:'Berhasil', text:'Mohon tunggu sebentar...'}).then(function(){
            window.location.href = BASE_URL + "transaction/redirect/" + (res.no_transaksi || "");
          });
        }).fail(function(){
          Alert.fire({icon:'error', title:'Gagal', text:'Terjadi kesalahan jaringan.'});
        });
      };

      // Modal delete global
      $(document).on('click', '.js-open-delete', function(e){
        e.preventDefault();
        const id = $(this).data('id');
        const no = $(this).data('no');
        $('#deleteModal .js-no-order').text(no);
        $('#deleteModal .js-delete-link').attr('href', BASE_URL + "transaction/buy-delete-transaction/" + id);
        $('#deleteModal').modal('show');
      });

      // DataTables
      const table = $('#dataTable').DataTable({
        serverSide:true,
        processing:true,
        autoWidth:false,
        scrollX:false,                 // hindari horizontal scroll
        order:[[4,'desc']],            // kolom Date
        ajax:{
          url: "<?= base_url('report/buy-dt') ?>",
          type:"POST",
          data:function(d){
            d.dateStart = $('#dateStart').val();
            d.dateEnd   = $('#dateEnd').val();
            d[CSRF_NAME]= CSRF_HASH;
          },
          dataSrc:function(json){
            if (json && typeof json[CSRF_NAME] !== 'undefined') CSRF_HASH = json[CSRF_NAME];
            return json && json.data ? json.data : [];
          }
        },
        dom:'Bfrtip',
        lengthMenu:[[10,25,50,100,-1],['10 rows','25 rows','50 rows','100 rows','Show all']],
        buttons:[
          { extend:'copyHtml5',  text:'<i class="fas fa-clipboard"></i> Copy',  className:'btn btn-default btn-flat', exportOptions:{columns:[0,2,3,4,5,6,7,8,9]} },
          { extend:'pdfHtml5',   orientation:'landscape', pageSize:'A4', text:'<i class="fas fa-file-pdf"></i> PDF', className:'btn btn-default btn-flat', exportOptions:{columns:[0,2,3,4,5,6,7,8,9]} },
          { extend:'excelHtml5', text:'<i class="fas fa-file-excel"></i> Excel', className:'btn btn-default btn-flat', exportOptions:{columns:[0,2,3,4,5,6,7,8,9]} },
          { extend:'csvHtml5',   text:'<i class="fas fa-file-csv"></i> CSV',    className:'btn btn-default btn-flat', exportOptions:{columns:[0,2,3,4,5,6,7,8,9]} },
          { extend:'pageLength', className:'selectTable btn btn-default btn-flat' }
        ],
        columnDefs:[
          { targets:[1,2,3,4,8,9], className:'text-nowrap' }, // tetap 1 baris
          { targets:[5,6,7],       className:'text-wrap'    }  // boleh membungkus
        ]
      });

      // Filter tanpa reload halaman
      $('#filterForm').on('submit', function(e){ e.preventDefault(); table.ajax.reload(); });

      // Sesuaikan lebar setelah modal tampil/ditutup & saat resize
      $(document).on('shown.bs.modal hidden.bs.modal', function(){ table.columns.adjust(); });
      $(window).on('resize', function(){ table.columns.adjust(); });
    });
  });
})();
</script>

