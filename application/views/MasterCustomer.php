<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped only for this page ===== */
  .mc-wrap{ margin-top: calc(var(--topbar-h, 72px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .mc-container{ max-width: 1260px; margin-inline:auto; }

  /* Breadcrumb chip */
  .mc-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 14px; border-radius:12px;
    background: linear-gradient(135deg, var(--blue-pastel, #B1F0F7), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a; box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .mc-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .mc-crumbs a:hover{ color:var(--blue-dark, #001A6E); text-decoration:underline; }
  .mc-crumbs .sep{ color:#7a8eb8; }

  /* Header */
  .mc-head h1{ margin:6px 0; color:#fff; font-weight:800; letter-spacing:.2px; font-size: clamp(20px, 3.2vw, 28px); }
  .mc-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Actions */
  .mc-actions{ margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between; }
  .btn-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:700;
    padding:10px 14px; border-radius:12px; text-decoration:none;
    border:1px solid #dfeaff; background:#fff; color:#0e204a; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-soft:hover{ border-color:#cfe0ff; }
  /* tombol tambah yang lebih catchy */
  .btn-add{
    display:inline-flex; align-items:center; gap:10px; font-weight:800;
    padding:12px 18px; border-radius:14px; text-decoration:none;
    background: linear-gradient(135deg, var(--blue-light, #074799), var(--blue-dark, #001A6E));
    color:#fff; box-shadow:0 12px 22px rgba(0,26,110,.25);
    transition: transform .08s ease, filter .2s ease;
  }
  .btn-add:hover{ filter:brightness(1.05); transform:translateY(-1px); }
  .btn-add .ico{
    width:28px; height:28px; display:grid; place-items:center; border-radius:50%;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.35);
  }

  /* Card + Table */
  .mc-card{ margin-top:12px; background:#fff; border:1px solid #e6eefc; border-radius:16px; box-shadow:0 12px 24px rgba(0,0,0,.08); overflow:hidden; }
  .mc-card-hd{ padding:14px 16px; display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg, #f7fbff, #ffffff); border-bottom:1px solid #eaf0ff; }
  .mc-card-hd h2{ margin:0; font-size:16px; color:#0e204a; font-weight:800; }
  .mc-badge{ display:inline-block; padding:6px 10px; border-radius:999px; border:1px solid #dfeaff; background:#fff; color:#0e2b68; font-size:12px; }

  .mc-table-wrap{ padding:12px 14px; }
  .mc-table{ width:100%; border-collapse:separate; border-spacing:0; font-size:14px; }
  .mc-table thead th{ background:#f4f8ff; color:#345; font-weight:700; padding:10px 12px; border-bottom:1px solid #e6eefc; position:sticky; top:0; z-index:1; }
  .mc-table td{ padding:12px 12px; border-bottom:1px solid #f0f4ff; vertical-align:top; }
  .mc-table tbody tr:hover{ background:#f9fbff; }
  .mc-table tbody tr:nth-child(even){ background:#fcfdff; }
  .mc-col-action{ width:92px; }
  .mc-col-no{ width:56px; }
  .mc-ellipsis{ max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  @media (max-width: 900px){ .mc-ellipsis{ max-width:200px; } }
  @media (max-width: 640px){ .mc-ellipsis{ max-width:140px; } .mc-col-action{ width:80px; } }

  /* ===== DataTables skin (scoped) ===== */
  .mc-wrap .dataTables_wrapper .dataTables_length select,
  .mc-wrap .dataTables_wrapper .dataTables_filter input{
    height:36px; padding:6px 12px; border-radius:10px; border:1px solid #dfeaff; outline:none;
    background:#fff; box-shadow:0 6px 12px rgba(0,0,0,.04); color:#0e204a;
  }
  .mc-wrap .dataTables_wrapper .dataTables_filter input{ min-width:220px; }
  .mc-wrap .dataTables_wrapper .dataTables_filter label,
  .mc-wrap .dataTables_wrapper .dataTables_length label{ color:#0e204a; font-weight:600; }

  .mc-wrap .dataTables_wrapper .dataTables_info{ padding-top:16px; color:#456; font-weight:600; }
  .mc-wrap .dataTables_wrapper .dataTables_paginate{
    margin-top:8px; padding:6px; border-radius:12px; background:#f7fbff; border:1px solid #e6eefc;
  }
  .mc-wrap .dataTables_wrapper .dataTables_paginate .paginate_button{
    border:1px solid #dfeaff; border-radius:10px; padding:.35rem .7rem; margin:0 3px; background:#fff !important; color:#0e204a !important;
    box-shadow:0 6px 12px rgba(0,0,0,.04);
  }
  .mc-wrap .dataTables_wrapper .dataTables_paginate .paginate_button:hover{ border-color:#cfe0ff; background:#ffffff !important; }
  .mc-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.current{
    background:linear-gradient(135deg, var(--blue-pastel,#B1F0F7), #eaf6ff) !important; color:#001A6E !important; border-color:#badcff;
  }
  .mc-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{ opacity:.45; }

  /* processing overlay */
  .mc-wrap .dataTables_wrapper .dataTables_processing{
    top:0; left:0; right:0; bottom:0; width:auto; height:auto; margin:0;
    display:flex; align-items:center; justify-content:center;
    border:0; background:rgba(255,255,255,.6);
  }
  .mc-wrap .dataTables_wrapper .dataTables_processing:before{
    content:""; width:22px; height:22px; margin-right:10px; border-radius:50%;
    border:3px solid #9ec7ff; border-top-color:transparent; animation:spin 1s linear infinite;
  }
  @keyframes spin{ to{ transform:rotate(360deg);} }
</style>

<div class="mc-wrap">
  <div class="mc-container">

    <!-- Breadcrumb -->
    <nav class="mc-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master') ?>">Master</a>
      <span class="sep">/</span>
      <span>Master Customer</span>
    </nav>

    <!-- Head -->
    <header class="mc-head">
      <h1>Master Customer</h1>
      <p>Kelola data pelanggan di aplikasi <strong>I Love Emas</strong>.</p>
    </header>

    <!-- Actions -->
    <div class="mc-actions">
      <a href="<?= base_url('transaction/new-customer/?key=add') ?>" class="btn-add">
        <span class="ico"><i class="fas fa-user-plus"></i></span>
        <span>Tambah Customer</span>
      </a>
      <a href="<?= base_url('master') ?>" class="btn-soft">
        <i class="fas fa-arrow-left"></i> Kembali ke Master
      </a>
    </div>

    <!-- Flash message -->
    <?php if($this->session->userdata('status')==='success'): ?>
      <div style="margin-top:10px;">
        <div class="mc-badge" style="border-color:#c9f6d1;background:#ecffef;color:#155724;">
          <?= html_escape($this->session->userdata('message')) ?>
        </div>
      </div>
      <?php $this->session->set_userdata(['status'=>'','message'=>'']); ?>
    <?php endif; ?>

    <!-- Card + Table -->
    <section class="mc-card" aria-label="Customer data">
      <div class="mc-card-hd">
        <h2>Customer Data</h2>
        <span class="mc-badge" id="badgeTotal">Total: —</span>
      </div>
      <div class="mc-table-wrap">
        <div class="table-responsive">
          <table class="table table-borderless mc-table" id="custTable">
            <thead>
              <tr>
                <th class="mc-col-no">No</th>
                <th class="mc-col-action">Aksi</th>
                <th>No Order</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Domisili</th>
                <th>Telepon</th>
                <th>Tanggal Buat</th>
                <th>Dibuat Oleh</th>
              </tr>
            </thead>
            <tbody></tbody> <!-- server-side -->
          </table>
        </div>
      </div>
    </section>

  </div>
</div>

<script>
(function waitForDeps(){
  if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return setTimeout(waitForDeps, 50);
  var $ = jQuery;

  var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
  var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

  var $tbl = $('#custTable');
  if ($.fn.dataTable.isDataTable($tbl)) { $tbl.DataTable().destroy(); $tbl.find('tbody').empty(); }
  $.fn.dataTable.ext.errMode = 'console';

  var dt = $tbl.DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "<?= site_url('master/customer-dt'); ?>",
      type: "POST",
      data: function (d) { d[csrfName] = csrfHash; },
      dataSrc: function (json) { if (json && json[csrfName]) csrfHash = json[csrfName]; return json && json.data ? json.data : []; },
      error: function (xhr){ console.error('DT ajax error:', xhr.responseText); }
    },
    order: [[2, 'asc']],
    columns: [
      { data: 0, orderable: false },
      { data: 1, orderable: false, searchable: false },
      { data: 2 }, { data: 3 }, { data: 4 },
      { data: 5 }, { data: 6 }, { data: 7 }, { data: 8 }
    ],
    scrollX: true,
    language: {
      search:"Cari:", lengthMenu:"Tampilkan _MENU_ entri",
      info:"Menampilkan _START_–_END_ dari _TOTAL_ entri",
      infoEmpty:"Tidak ada data", zeroRecords:"Tidak ditemukan data yang cocok",
      paginate:{ first:"Pertama", last:"Terakhir", next:"Berikutnya", previous:"Sebelumnya" }
    }
  });

  $tbl.on('xhr.dt', function (e, s, json) {
    if (json && typeof json.recordsFiltered !== 'undefined') {
      var $badge = $('#badgeTotal'); if ($badge.length) $badge.text('Total: ' + json.recordsFiltered);
    }
  });

  $(document).on('click', '.js-del', function(e){
    e.preventDefault();
    var nm = $(this).data('name');
    if (confirm('Hapus customer ' + (nm ? ('"'+nm+'" ') : '') + 'ini? Aksi tidak dapat dibatalkan.')) location.href = this.href;
  });
})();
</script>
