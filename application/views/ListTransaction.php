<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped supaya tak bentrok dengan template lain ===== */
  .transaction-list-scope .wrap{ margin-top: calc(var(--topbar-h, 98px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .transaction-list-scope .container-max{ max-width: 1160px; margin-inline:auto; }

  /* Breadcrumb pil (seragam) */
  .transaction-list-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .transaction-list-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .transaction-list-scope .crumbs a:hover{ color:var(--blue-light, #4a7dff); text-decoration:underline; }
  .transaction-list-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .transaction-list-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .transaction-list-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Card */
  .transaction-list-scope .card{
    border-radius:16px; border:1px solid #e6eefc; overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.08); background:#fff;
  }
  .transaction-list-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #e9faff);
    border-bottom:1px solid #dfeaff;
  }
  .transaction-list-scope .card-header h6{ margin:0; color:#0e204a; font-weight:800; }

  /* Tabel */
  .transaction-list-scope .table{ width:100%; border:1px solid #e6eefc; border-radius:12px; overflow:hidden; }
  .transaction-list-scope thead th{
    background:#f5fbff; color:#0e204a; font-weight:800; text-align:center; white-space:nowrap;
  }
  .transaction-list-scope .table td, 
  .transaction-list-scope .table th{ vertical-align:middle !important; }

  /* Tombol kanan header */
  .transaction-list-scope .header-actions{ display:flex; gap:8px; justify-content:flex-end; }

  /* Tombol besar bagian bawah */
  .transaction-list-scope .actions{ display:flex; gap:12px; flex-wrap:wrap; }
  @media (max-width:576px){
    .transaction-list-scope .actions .btn{ width:100%; }
  }

  /* Overlay Loading (lokal) */
  .loading-overlay{
    position: fixed; inset: 0;
    background: rgba(15,23,42,.35);
    backdrop-filter: saturate(110%) blur(1px);
    display:flex; align-items:center; justify-content:center;
    z-index: 5000;
    opacity:0; visibility:hidden; pointer-events:none;
    transition: opacity .2s ease;
  }
  .loading-overlay.show{ opacity:1; visibility:visible; pointer-events:auto; }
  .loading-box{
    display:flex; flex-direction:column; align-items:center; gap:14px;
    background: rgba(255,255,255,.95);
    border:1px solid #e6eefc; border-radius:16px;
    padding:18px 22px; box-shadow:0 12px 30px rgba(0,0,0,.18);
    min-width:220px;
  }
  .loading-text{ font-weight:800; color:#0e204a; letter-spacing:.3px; }
  .loading-box .fa-spin{ font-size:30px; color:#2563eb; }
  .loading-fallback{
    width:34px; height:34px; border-radius:50%;
    border:3px solid #dbeafe; border-top-color:#2563eb;
    animation: spin 1s linear infinite; display:none;
  }
  @keyframes spin{ to{ transform: rotate(360deg); } }
  @media (prefers-reduced-motion: reduce){
    .loading-box .fa-spin{ animation: none !important; }
    .loading-fallback{ animation: none !important; }
  }
</style>

<div class="transaction-list-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>" class="fa fa-home" aria-label="Home"></a>
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <span>Transaction</span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Transaction</h3>
        <p>Daftar transaksi aktif. Gunakan tombol di kanan untuk membuat atau memperbarui status.</p>
      </header>

      <!-- Card -->
      <section class="card mt-3">
        <div class="card-header py-3">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="m-0">Transaction Data</h6>
            <div class="header-actions">
              <a href="<?= base_url('transaction') ?>" class="btn btn-success btn-sm">
                <i class="fa fa-plus mr-1"></i> Add Transaction
              </a>
              <button class="btn btn-warning btn-sm" id="btnUpdateAll">
                <i class="fa fa-check-double mr-1"></i> Set All to SELESAI
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <?php if($this->session->userdata('status')=='success'){ ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $this->session->userdata('message') ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php } $this->session->set_userdata(['status'=>'','message'=>'']); ?>

          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
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
      </section>

      <!-- Back -->
      <div class="actions mt-3">
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Overlay Loading (lokal) -->
<div class="loading-overlay" id="loadingOverlay" aria-hidden="true" aria-label="Memproses…" role="status">
  <div class="loading-box" aria-live="polite">
    <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>
    <div class="loading-fallback" aria-hidden="true"></div>
    <div class="loading-text" id="loadingText">Loading…</div>
  </div>
</div>

<script>
/* ===== Overlay helper (lokal, fallback jika AppOverlay global tidak ada) ===== */
var Overlay = (function(){
  var box = document.getElementById('loadingOverlay');
  var txt = document.getElementById('loadingText');
  function show(msg){ if (txt && msg) txt.textContent = msg; if(box) box.classList.add('show'); }
  function hide(){ if(box) box.classList.remove('show'); }
  return {
    show: function(msg){
      if (window.AppOverlay && typeof AppOverlay.show === 'function') { AppOverlay.show(msg); return; }
      show(msg);
    },
    hide: function(){
      if (window.AppOverlay && typeof AppOverlay.hide === 'function') { AppOverlay.hide(); return; }
      hide();
    }
  };
})();

/* ===== Tunggu jQuery + DataTables siap ===== */
(function waitLib(retry){
  if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) { initDT(); return; }
  if (retry > 60) { console.error('jQuery/DataTables tidak tersedia.'); return; }
  setTimeout(function(){ waitLib(retry+1); }, 100);
})(0);

function initDT(){
  (function($){
    var csrfName = '<?= isset($this->security) ? $this->security->get_csrf_token_name() : '' ?>';
    var csrfHash = '<?= isset($this->security) ? $this->security->get_csrf_hash() : '' ?>';

    // Hook overlay untuk DataTables load
    $('#dataTable').on('preXhr.dt', function(){ Overlay.show('Loading data…'); })
                   .on('xhr.dt',    function(){ Overlay.hide(); });

    var dt = $('#dataTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= base_url('transaction/getTransactions') ?>",
        type: "POST",
        data: function(d){ if (csrfName) d[csrfName] = csrfHash; }
      },
      columns: [
        { data: 'no', orderable:false, searchable:false, className:'text-center' },
        { data: 'action', orderable:false, searchable:false, className:'text-center' },
        { data: 'transaction', className:'text-center' },
        { data: 'no_order', className:'text-center' },
        { data: 'status', className:'text-center' },
        { data: 'date', className:'text-center' },
        { data: 'customer' },
        { data: 'qty', className:'text-right' },
        {
          data: 'price_total', className:'text-right',
          render: function (data, type) {
            if (type === 'display' || type === 'filter')
              return 'IDR ' + new Intl.NumberFormat('id-ID').format(data || 0);
            return data;
          }
        }
      ]
    });

    // Bind tombol header
    $('#btnUpdateAll').on('click', function(){
      Swal.fire({
        title: 'Are you sure?',
        text: 'This will update the status of all transactions to SELESAI.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
      }).then(function(res){
        if (!res.isConfirmed) return;
        Overlay.show('Updating…');
        $.ajax({
          url: "<?= base_url('transaction/updateAllStatus') ?>",
          method: "POST",
          dataType: "json",
          data: (function(){ var p={}; if(csrfName) p[csrfName]=csrfHash; return p; })(),
          complete: function(){ Overlay.hide(); },
          success: function (r) {
            if (r && r.success) {
              Swal.fire('Success!','All transactions have been updated to SELESAI.','success')
                .then(function(){ dt.ajax.reload(null,false); });
              if (r[csrfName]) csrfHash = r[csrfName]; // refresh CSRF kalau backend kirim balik
            } else {
              Swal.fire('Error!', (r && r.message) || 'An error occurred while updating data.','error');
            }
          },
          error: function(){
            Swal.fire('Error!','Failed to process the request. Please try again.','error');
          }
        });
      });
    });

    // Hapus (dipakai di kolom Action)
    window.deleteData = function(no_order){
      Swal.fire({
        title: "Are you sure?",
        text: "The transaction will be deleted and cannot be restored!",
        showDenyButton: true, confirmButtonText: "Cancel", denyButtonText: "Delete"
      }).then(function(result){
        if (result.isDenied) window.location.href = "<?= base_url('transaction/delete-transaction/') ?>" + no_order;
      });
    };

  })(jQuery);
}
</script>
