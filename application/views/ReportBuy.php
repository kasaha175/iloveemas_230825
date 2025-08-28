<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$dateStart = $this->input->get('dateStart') ?: date('Y-m-01');
$dateEnd   = $this->input->get('dateEnd')   ?: date('Y-m-t');

/* ===== Ambil warna dari konfigurasi aplikasi (fallback aman) ===== */
$cfg = isset($config) && is_array($config) ? $config : [];
$brand     = $cfg['color_primary']   ?? $cfg['primary_color'] ?? '#2563eb';
$brand2    = $cfg['color_primary_2'] ?? '#1d4ed8';
$pastel    = $cfg['color_pastel']    ?? '#e7f1ff';
$ink       = $cfg['color_text']      ?? '#0e204a';
$accent    = $cfg['color_accent']    ?? '#4a7dff';
$mutedLine = '#e6eefc';
?>
<style>
/* ===== Scoped styles agar aman ===== */
.report-buy-scope{
  --brand: <?= html_escape($brand) ?>;
  --brand2: <?= html_escape($brand2) ?>;
  --blue-pastel: <?= html_escape($pastel) ?>;
  --ink: <?= html_escape($ink) ?>;
  --accent: <?= html_escape($accent) ?>;
  --line: <?= html_escape($mutedLine) ?>;
}
.report-buy-scope .wrap{ margin-top: calc(var(--topbar-h, 98px) + 12px); padding: clamp(12px, 2vw, 20px); }
.report-buy-scope .container-max{ max-width: 1380px; margin-inline:auto; }

.report-buy-scope .crumbs{
  display:flex; align-items:center; gap:10px; flex-wrap:wrap;
  padding:10px 16px; border-radius:9999px;
  background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
  border:1px solid #dfeaff; color:var(--ink);
  box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
}
.report-buy-scope .crumbs a{ color:var(--ink); text-decoration:none; font-weight:700; font-size:12px; }
.report-buy-scope .crumbs a:hover{ color:var(--accent); text-decoration:underline; }
.report-buy-scope .crumbs .sep{ color:#7a8eb8; }

.report-buy-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
.report-buy-scope .heading p{ margin:0; color:#dbe8ff; font-size:12px; }

/* Card */
.report-buy-scope .card{
  border-radius:16px; border:1px solid var(--line); overflow:hidden;
  box-shadow:0 10px 24px rgba(0,0,0,.08); background:#fff;
}
.report-buy-scope .card-header{
  background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
  border-bottom:1px solid #dfeaff;
}
.report-buy-scope .card-header h6{ margin:0; color:var(--ink); font-weight:800; }

/* ==== DataTable look & feel (font-size 11px) ==== */
.report-buy-scope .dataTables_wrapper{ font-size:11px; }
.report-buy-scope .dataTables_length{ display:flex; align-items:center; }
.report-buy-scope .dataTables_length label{
  margin-bottom:0; font-weight:600; display:flex; align-items:center; gap:8px;
}
.report-buy-scope .dataTables_length select{
  font-size:11px; padding:.35rem .7rem; height:30px; border-radius:8px;
  border:1px solid #e2e8f0; min-width:160px; /* lebih lebar */
}
.report-buy-scope .dataTables_filter label{ margin-bottom:0; font-weight:600; }
.report-buy-scope .dataTables_filter input{
  font-size:11px; padding:.35rem .6rem; height:30px; border-radius:8px;
  border:1px solid #e2e8f0;
}

/* Pagination (gaya kotak) */
.report-buy-scope .dataTables_paginate{ padding-top:.25rem; }
.report-buy-scope .dataTables_paginate .paginate_button{
  padding:.28rem .55rem !important; margin:0 .12rem !important; border-radius:6px !important;
  border:1px solid #e2e8f0 !important; background:#fff !important; color:#334155 !important;
}
.report-buy-scope .dataTables_paginate .paginate_button:hover{ background:#f1f5f9 !important; }
.report-buy-scope .dataTables_paginate .paginate_button.current{
  background:var(--brand) !important; color:#fff !important; border-color:var(--brand) !important;
}
.report-buy-scope .dataTables_info{ padding-top:.6rem; }
.report-buy-scope .dt-buttons .btn{ font-size:11px; padding:.35rem .6rem; border-radius:8px; }

/* Table */
.report-buy-scope .table{ width:100%; border:1px solid var(--line); border-radius:12px; overflow:hidden; }
.report-buy-scope thead th{
  background:#f5fbff; color:var(--ink); font-weight:800; text-align:center; white-space:nowrap; font-size:11px;
  position:sticky; top:0; z-index:2;
}
.report-buy-scope .table td, 
.report-buy-scope .table th{ vertical-align:middle !important; font-size:11px; }

/* Action dropdown button (kecil) */
.report-buy-scope .btn-action{ font-size:11px; padding:.3rem .5rem; border-radius:8px; }

/* Wrap teks kolom panjang, tanpa scroll horizontal */
.report-buy-scope .text-wrap{ white-space:normal !important; }
.report-buy-scope .text-nowrap{ white-space:nowrap !important; }

/* ===== Overlay preloader (min 1.5s) ===== */
#rbOverlay{
  position: fixed; inset: 0; background: rgba(15,23,42,.35); backdrop-filter: saturate(110%) blur(2px);
  display:flex; align-items:center; justify-content:center; z-index: 5000;
  opacity:0; visibility:hidden; pointer-events:none; transition: opacity .2s ease;
}
#rbOverlay.show{ opacity:1; visibility:visible; pointer-events:auto; }
#rbOverlay .box{
  display:flex; flex-direction:column; align-items:center; gap:10px;
  background: rgba(255,255,255,.95); border:1px solid var(--line); border-radius:16px;
  padding:16px 18px; box-shadow:0 12px 30px rgba(0,0,0,.18); min-width:220px;
}
#rbOverlay .spinner{ width:26px; height:26px; border-radius:50%; border:3px solid #dbeafe; border-top-color:var(--brand); animation:spin 1s linear infinite; }
@keyframes spin{ to{ transform: rotate(360deg); } }

/* ===== Modal Detail – restyle seragam brand ===== */
#detailModal .modal-content{ border:1px solid var(--line); border-radius:16px; overflow:hidden; }
#detailModal .modal-header{
  background: linear-gradient(135deg, var(--blue-pastel), #eef8ff);
  border-bottom:1px solid var(--line);
}
#detailModal .modal-title{ color:var(--ink); font-weight:800; }
#detailModal .table th{ background:#f7fbff; width:28%; }
#detailModal .badge-status{
  display:inline-flex; align-items:center; gap:6px; font-weight:700; letter-spacing:.3px;
  padding:.18rem .5rem; border-radius:999px; background:#eef2ff; color:#3730a3; border:1px solid #e5e7eb;
}
.btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
.btn-brand:hover{ background:var(--brand2); border-color:var(--brand2); color:#fff; }

@media (max-width:576px){
  #detailModal .modal-dialog{ margin:10px; }
}

/* ====== Modal Detail: footer & tombol ====== */
#rbDetailModal .modal-footer{
  background: #f7fbff;
  border-top: 1px solid #e6eefc;
}
#rbDetailModal .modal-footer .btn{
  font-size: 11px;               /* konsisten dengan halaman */
  font-weight: 700;
  border-radius: 10px;
  padding: .38rem .7rem;
}

/* tombol utama sesuai brand */
#rbDetailModal .btn-primary{
  background: var(--brand-600, #2563eb);
  border-color: var(--brand-600, #2563eb);
  color: #fff;
}
#rbDetailModal .btn-primary:hover{
  background: var(--brand-700, #1e40af);
  border-color: var(--brand-700, #1e40af);
}

/* tombol tutup (outline) */
#rbDetailModal .btn-outline-secondary{
  background: #fff;
  color: #334155;
  border-color: #cbd5e1;
}
#rbDetailModal .btn-outline-secondary:hover{
  background: #f8fafc;
  color: #0f172a;
}

/* ikon pada tombol */
#rbDetailModal .modal-footer .btn .fa,
#rbDetailModal .modal-footer .btn .fas{
  margin-right: .4rem;
}
</style>

<div class="report-buy-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>" class="fa fa-home" aria-label="Home"></a>
        <a href="<?= base_url('report') ?>">Report</a>
        <span class="sep">/</span>
        <span>Buy</span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Report</h3>
        <p>Transaction Buy</p>
      </header>

      <!-- Filter -->
      <section class="card mt-3">
        <div class="card-header py-3">
          <h6 class="m-0">Filter Data</h6>
        </div>
        <div class="card-body">
          <form id="filterForm" action="<?= base_url('report/buy/') ?>" method="get">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="dateStart">Start Date</label>
                <input id="dateStart" name="dateStart" required type="date" value="<?= html_escape($dateStart) ?>" class="form-control form-control-sm">
              </div>
              <div class="form-group col-md-4">
                <label for="dateEnd">End Date</label>
                <input id="dateEnd" name="dateEnd" required type="date" value="<?= html_escape($dateEnd) ?>" class="form-control form-control-sm">
              </div>
              <div class="form-group col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-brand btn-block btn-sm">Filter</button>
              </div>
            </div>
          </form>
        </div>
      </section>

      <!-- Data table -->
      <section class="card mt-3">
        <div class="card-header py-3">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="m-0">Transaction Data</h6>
            <div class="dt-topbar d-flex align-items-center"></div>
          </div>
        </div>
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
                  <th class="text-nowrap">Qty</th>
                  <th class="text-nowrap">Grand Total</th>
                </tr>
              </thead>
              <tbody></tbody><!-- server-side -->
            </table>
          </div>
        </div>
      </section>

      <!-- Back -->
      <div class="mt-3">
        <a href="<?= base_url('report') ?>" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left mr-1"></i> Kembali  
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Overlay Preloader -->
<div id="rbOverlay" aria-hidden="true" role="status">
  <div class="box" aria-live="polite">
    <div class="spinner" aria-hidden="true"></div>
    <div style="font-weight:800;color:#0e204a;letter-spacing:.2px;">Loading…</div>
    <div style="font-size:11px;color:#475569;">Please wait</div>
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
        <button class="btn btn-brand" type="button" onclick="submitKonfirmasi()"><i class="fa fa-save mr-1"></i> Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal DETAIL (2 tabel: ringkasan + items) -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailTitle">Transaction Detail</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>

      <div class="modal-body">
        <!-- Tabel Ringkasan -->
        <div class="table-responsive mb-3">
          <table class="table table-bordered mb-0">
            <tbody>
              <tr><th style="width:180px;">No Order</th><td id="d_no"></td></tr>
              <tr><th>Status</th><td><span id="d_status" class="badge-status"></span></td></tr>
              <tr><th>Tanggal</th><td id="d_date"></td></tr>
              <tr><th>Customer</th><td id="d_customer"></td></tr>
              <tr><th>Receive By</th><td id="d_receive"></td></tr>
              <tr><th>Qty</th><td id="d_qty"></td></tr>
            </tbody>
          </table>
        </div>

        <!-- Tabel Items -->
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead>
              <tr>
                <th class="text-center" style="width:48px;">No</th>
                <th>Item</th>
                <th class="text-center" style="width:80px;">Qty</th>
                <th class="text-right" style="width:140px;">Unit Price</th>
                <th class="text-right" style="width:160px;">Total</th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <tr><td colspan="5" class="text-center text-muted">Loading item…</td></tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-right">Subtotal</th>
                <th class="text-right" id="ft_subtotal">IDR 0</th>
              </tr>
              <tr>
                <th colspan="4" class="text-right">Admin Fee</th>
                <th class="text-right" id="ft_admin">IDR 0</th>
              </tr>
              <tr>
                <th colspan="4" class="text-right">Grand Total</th>
                <th class="text-right" id="ft_grand">IDR 0</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <small class="text-muted d-block mt-2">* Jika detail item tidak muncul, pastikan endpoint JSON tersedia.</small>
      </div>

      <div class="modal-footer">
        <button type="button"
                class="btn btn-outline-secondary btn-sm"
                data-dismiss="modal">
          <i class="fa fa-times fas" aria-hidden="true"></i> Tutup
        </button>

        <a id="rbBtnPrint"
          href="#"
          target="_blank"
          rel="noopener"
          class="btn btn-primary btn-sm">
          <i class="fa fa-print fas" aria-hidden="true"></i> Print
        </a>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const BASE_URL  = "<?= base_url() ?>";
  const CSRF_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  let   CSRF_HASH = "<?= $this->security->get_csrf_hash(); ?>";

  /* helper: buat URL PDF dari NoOrder & Date */
  function buildPdfUrl(noOrder, dateStr){
    // Ambil YYYY dan MM dari kolom Date (format Y-m-d / Y-m-d H:i:s)
    const m = (dateStr||'').match(/(\d{4})[-/](\d{2})/);
    const yyyy = m ? m[1] : '';
    const mm   = m ? m[2] : '';
    // Nama file = NoOrder + ".pdf"
    const file = (String(noOrder||'').replace(/[^\w\-]/g, '')) + '.pdf';
    // Path sesuai pola penyimpanan
    return BASE_URL + 'uploads/prints/buy/' + yyyy + '/' + mm + '/' + encodeURIComponent(file);
  }

  /* ===== Overlay ===== */
  const Overlay = (function(){
    const el = document.getElementById('rbOverlay');
    let minTimer=null, minDone=false, waitingXHR=true;
    function show(){ if(!el) return; el.classList.add('show'); minDone=false; waitingXHR=true; minTimer=setTimeout(()=>{ minDone=true; maybeHide(); },1500); }
    function hide(){ if(!el) return; el.classList.remove('show'); }
    function markXHRDone(){ waitingXHR=false; maybeHide(); }
    function maybeHide(){ if(minDone && !waitingXHR){ clearTimeout(minTimer); hide(); } }
    return { show, markXHRDone };
  })();

  function ensureLibs(cb){
    let tries=0;
    const t=setInterval(function(){
      const ok = !!(window.jQuery && jQuery.fn && jQuery.fn.DataTable);
      if (ok){ clearInterval(t); cb(jQuery); }
      else if (++tries > 240){ clearInterval(t); console.error('jQuery/DataTables not ready.'); }
    }, 50);
  }
  function fmtIDR(n){ return 'IDR ' + new Intl.NumberFormat('id-ID').format(Number(n||0)); }

  document.addEventListener('DOMContentLoaded', function(){
    ensureLibs(function($){
      Overlay.show();
      $('#modalEdit, #deleteModal, #detailModal').appendTo('body');

      window.openModalEdit = function(id){ $('#edit_id').val(id); $('#modalEdit').modal('show'); };
      window.submitKonfirmasi = function(){
        $.post({
          url: BASE_URL + "transaction/confirm-edit",
          data:{
            type: $('#type').val(),
            id: $('#edit_id').val(),
            alasan: ($('#alasan').val()||'').trim(),
            password: $('#password').val(),
            [CSRF_NAME]: CSRF_HASH
          }
        }).done(function(resp){
          let r={}; try{ r = typeof resp==='object'? resp : JSON.parse(resp); }catch(_){}
          if(!r || r.status==='gagal'){ alert('Kata sandi salah / permintaan tidak valid.'); return; }
          location.href = BASE_URL + "transaction/redirect/" + (r.no_transaksi || "");
        }).fail(function(){ alert('Terjadi kesalahan jaringan.'); });
      };

      const dt = $('#dataTable').DataTable({
        serverSide:true,
        processing:true,
        autoWidth:false,
        order:[[4,'desc']],
        ajax:{ url : "<?= base_url('report/buy-dt') ?>", type: "POST" },
        lengthMenu : [[10,50,100],[10,50,100]],
        pageLength : 10,
        dom:
          '<"d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2"l<"dt-btns"B>f>' +
          't' +
          '<"d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2"i p>',
        buttons:[
          { extend:'copyHtml5',  text:'<i class="fas fa-clipboard"></i> Copy', className:'btn btn-light btn-sm', titleAttr:'Copy' },
          { extend:'excelHtml5', text:'<i class="fas fa-file-excel"></i> Excel', className:'btn btn-light btn-sm', titleAttr:'Excel' },
          { extend:'csvHtml5',   text:'<i class="fas fa-file-csv"></i> CSV',   className:'btn btn-light btn-sm', titleAttr:'CSV' },
          { extend:'pdfHtml5',   text:'<i class="fas fa-file-pdf"></i>',       className:'btn btn-light btn-sm', titleAttr:'PDF', orientation:'landscape', pageSize:'A4' }
        ],
        columnDefs:[
          { targets:[0,1,3,8], className:'text-center text-nowrap' },
          { targets:[5,6,7],   className:'text-wrap' },
          { targets:[9],       className:'text-right text-nowrap' }
        ],

        /* ==== ACTION: ubah ke Preview PDF yang ambil dari path ==== */
        createdRow: function(row){
          const $cells  = $('td', row);
          const $actTd  = $cells.eq(1);
          const $tmp    = $('<div/>').html($actTd.html());
          const $del    = $tmp.find('.js-open-delete').first();
          const $edit   = $tmp.find('button[onclick^="openModalEdit"]').first();

          const noOrder = $cells.eq(2).text().trim();   // ex: PB-2508-1385
          const dateTxt = $cells.eq(4).text().trim();   // ex: 2025-08-28 20:50:11
          const id      = $del.data('id') || '';

          // Bangun URL PDF dari path yang disimpan (uploads/prints/buy/YYYY/MM/NoOrder.pdf)
          const previewUrl = buildPdfUrl(noOrder, dateTxt);

          const status  = $cells.eq(3).text().trim();
          const created = $cells.eq(5).text().trim();
          const receive = $cells.eq(6).text().trim();
          const cust    = $cells.eq(7).text().trim();
          const qty     = $cells.eq(8).text().trim();
          const total   = $cells.eq(9).text().trim();

          const dropdown =
            '<div class="dropdown">' +
              '<button class="btn btn-outline-primary btn-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                '<i class="fas fa-cog mr-1"></i> Actions' +
              '</button>' +
              '<div class="dropdown-menu dropdown-menu-right">' +
                '<a class="dropdown-item text-primary" href="'+ previewUrl +'" target="_blank" rel="noopener">' +
                  '<i class="fas fa-file-pdf mr-2"></i> Preview PDF</a>' +
                '<a class="dropdown-item text-info js-show-detail" href="#" '+
                   'data-id="'+(id||'')+'" '+
                   'data-no="'+$('<div/>').text(noOrder).html()+'" '+
                   'data-status="'+$('<div/>').text(status).html()+'" '+
                   'data-date="'+$('<div/>').text(dateTxt).html()+'" '+
                   'data-created="'+$('<div/>').text(created).html()+'" '+
                   'data-receive="'+$('<div/>').text(receive).html()+'" '+
                   'data-customer="'+$('<div/>').text(cust).html()+'" '+
                   'data-qty="'+$('<div/>').text(qty).html()+'" '+
                   'data-total="'+$('<div/>').text(total).html()+'">'+
                   '<i class="fas fa-info-circle mr-2"></i> Detail</a>' +
                ($edit.length ? ('<a class="dropdown-item text-warning" href="#" onclick="'+$edit.attr('onclick')+'"><i class="fas fa-edit mr-2"></i> Edit</a>') : '') +
                (($del.length || id) ? '<div class="dropdown-divider"></div>' : '') +
                '<a class="dropdown-item text-danger js-open-delete" href="#" data-id="'+(id||'')+'" data-no="'+$('<div/>').text(noOrder).html()+'"><i class="fas fa-trash mr-2"></i> Delete</a>' +
              '</div>' +
            '</div>';

          $actTd.html(dropdown);
        }
      });

      // kirim filter + CSRF di setiap request
      $('#dataTable').on('preXhr.dt', function (e, settings, data) {
        data.dateStart  = $('#dateStart').val() || '<?= html_escape($dateStart) ?>';
        data.dateEnd    = $('#dateEnd').val()   || '<?= html_escape($dateEnd) ?>';
        data[CSRF_NAME] = CSRF_HASH;
      });
      $('#dataTable')
        .on('xhr.dt', function(e, s, json){
          if (json && typeof json[CSRF_NAME] !== 'undefined') CSRF_HASH = json[CSRF_NAME];
          Overlay.markXHRDone();
        })
        .on('error.dt', function(){ Overlay.markXHRDone(); });

      $('.dt-btns').appendTo($('.dt-topbar'));

      $('#filterForm').on('submit', function(e){
        e.preventDefault();
        var s = $('#dateStart').val(), f = $('#dateEnd').val();
        if (s && f && s > f) { alert('Start Date tidak boleh lebih besar dari End Date.'); return; }
        dt.page('first').draw(false);
      });

      $(document).on('click', '.js-open-delete', function(e){
        e.preventDefault();
        const id = $(this).data('id'); const no = $(this).data('no');
        $('#deleteModal .js-no-order').text(no||'');
        $('#deleteModal .js-delete-link').attr('href', BASE_URL + "transaction/buy-delete-transaction/" + id);
        $('#deleteModal').modal('show');
      });

      /* ===== DETAIL handler ===== */
      $(document).on('click', '.js-show-detail', function(e){
        e.preventDefault();
        const d   = $(this).data();

        // Ringkasan
        $('#d_no').text(d.no || '');
        $('#d_status').text(d.status || '');
        $('#d_date').text(d.date || '');
        $('#d_customer').text(d.customer || '');
        $('#d_receive').text(d.receive || '');
        $('#d_qty').text(d.qty || '');

        $('#itemsBody').html('<tr><td colspan="5" class="text-center text-muted">Loading item…</td></tr>');
        $('#ft_subtotal').text('IDR 0');
        $('#ft_admin').text('IDR 0');
        $('#ft_grand').text(d.total || 'IDR 0');

        // tombol preview PDF di modal → pakai path (NoOrder + Date)
        $('#rbBtnPrint').attr('href', buildPdfUrl(d.no, d.date));

        $('#detailModal').modal('show');

        if(!d.id){
          $('#itemsBody').html('<tr><td colspan="5" class="text-center text-muted">Detail item tidak tersedia.</td></tr>');
          return;
        }

        $.ajax({
          url: BASE_URL + 'report/buy-items-json/' + d.id,
          type: 'POST',
          dataType: 'json',
          data: (function(){ var p={}; p[CSRF_NAME]=CSRF_HASH; return p; })()
        }).done(function(r){
          if (r && r[CSRF_NAME]) CSRF_HASH = r[CSRF_NAME];
          if (!r || r.ok !== true) {
            $('#itemsBody').html('<tr><td colspan="5" class="text-center text-muted">Detail item tidak tersedia.</td></tr>');
            return;
          }
          const items = Array.isArray(r.items) ? r.items : [];
          if (!items.length) {
            $('#itemsBody').html('<tr><td colspan="5" class="text-center text-muted">Tidak ada item.</td></tr>');
          } else {
            let rows = '', subtotal = 0;
            items.forEach(function(it, i){
              const lineTotal = Number(it.total || (Number(it.qty||0) * Number(it.price||0)));
              subtotal += lineTotal;
              rows += '<tr>'+
                        '<td class="text-center">'+(i+1)+'</td>'+
                        '<td>'+(it.name || '-')+'</td>'+
                        '<td class="text-center">'+(it.qty || 0)+' '+(it.unit||'')+'</td>'+
                        '<td class="text-right">'+fmtIDR(it.price || 0)+'</td>'+
                        '<td class="text-right">'+fmtIDR(lineTotal)+'</td>'+
                      '</tr>';
            });
            $('#itemsBody').html(rows);
            $('#ft_subtotal').text(fmtIDR(subtotal));
            const admin = Number(r.admin_fee || 0);
            $('#ft_admin').text(fmtIDR(admin));
            const grand = Number(r.grand_total || 0) ||
                          (function(){ try{ return Number((''+(d.total||'')).replace(/[^\d]/g,'')); }catch(e){ return 0; }})();
            $('#ft_grand').text(fmtIDR(grand || (subtotal + admin)));
          }
        }).fail(function(){
          $('#itemsBody').html('<tr><td colspan="5" class="text-center text-muted">Gagal memuat detail item.</td></tr>');
        });
      });

    });
  });
})();
</script>




