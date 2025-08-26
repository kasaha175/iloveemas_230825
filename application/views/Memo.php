<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped only for this page ===== */
  .sk-wrap{ margin-top: calc(var(--topbar-h,72px) + 12px); padding: clamp(12px,2vw,20px); }
  .sk-container{ max-width:1260px; margin-inline:auto; }

  /* Breadcrumb chip */
  .sk-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 14px; border-radius:12px;
    background:linear-gradient(135deg, var(--blue-pastel,#B1F0F7), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a; box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:14px;
  }
  .sk-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .sk-crumbs a:hover{ color:var(--blue-dark,#001A6E); text-decoration:underline; }
  .sk-crumbs .sep{ color:#7a8eb8; }

  /* Header */
  .sk-head h1{ margin:6px 0; color:#fff; font-weight:800; letter-spacing:.2px; font-size:clamp(20px,3.2vw,28px); }
  .sk-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  /* Actions */
  .sk-actions{ margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between; }
  .btn-soft{
    display:inline-flex; align-items:center; gap:8px; font-weight:700;
    padding:10px 14px; border-radius:12px; text-decoration:none;
    border:1px solid #dfeaff; background:#fff; color:#0e204a; box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .btn-soft:hover{ border-color:#cfe0ff; }
  .btn-add{
    display:inline-flex; align-items:center; gap:10px; font-weight:800;
    padding:12px 18px; border-radius:14px; text-decoration:none; color:#fff; border:0;
    background:linear-gradient(135deg, var(--blue-light,#074799), var(--blue-dark,#001A6E));
    box-shadow:0 12px 22px rgba(0,26,110,.25); transition:transform .08s ease, filter .2s ease;
  }
  .btn-add:hover{ filter:brightness(1.05); transform:translateY(-1px); }
  .btn-add .ico{ width:28px; height:28px; display:grid; place-items:center; border-radius:50%;
                 background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.35); }

  /* Card + Table */
  .sk-card{ margin-top:12px; background:#fff; border:1px solid #e6eefc; border-radius:16px; box-shadow:0 12px 24px rgba(0,0,0,.08); overflow:hidden; }
  .sk-card-hd{ padding:14px 16px; display:flex; align-items:center; justify-content:space-between;
               background:linear-gradient(135deg,#f7fbff,#ffffff); border-bottom:1px solid #eaf0ff; }
  .sk-card-hd h2{ margin:0; font-size:16px; color:#0e204a; font-weight:800; }
  .sk-badge{ display:inline-block; padding:6px 10px; border-radius:999px; border:1px solid #dfeaff; background:#fff; color:#0e2b68; font-size:12px; }

  .sk-table-wrap{ padding:12px 14px; }
  .sk-table{ width:100%; border-collapse:separate; border-spacing:0; font-size:14px; }
  .sk-table thead th{ background:#f4f8ff; color:#345; font-weight:700; padding:10px 12px; border-bottom:1px solid #e6eefc; }
  .sk-table td{ padding:12px 12px; border-bottom:1px solid #f0f4ff; vertical-align:top; }
  .sk-table tbody tr:hover{ background:#f9fbff; }
  .sk-col-no{ width:56px; }
  .sk-col-action{ width:120px; }

  /* ===== DataTables skin (scoped) ===== */
  .sk-wrap .dataTables_wrapper .dataTables_length select,
  .sk-wrap .dataTables_wrapper .dataTables_filter input{
    height:36px; padding:6px 12px; border-radius:10px; border:1px solid #dfeaff; outline:none;
    background:#fff; box-shadow:0 6px 12px rgba(0,0,0,.04); color:#0e204a;
  }
  .sk-wrap .dataTables_wrapper .dataTables_filter input{ min-width:220px; }
  .sk-wrap .dataTables_wrapper .dataTables_filter label,
  .sk-wrap .dataTables_wrapper .dataTables_length label{ color:#0e204a; font-weight:600; }

  .sk-wrap .dataTables_wrapper .dataTables_info{ padding-top:16px; color:#456; font-weight:600; }
  .sk-wrap .dataTables_wrapper .dataTables_paginate{
    margin-top:8px; padding:6px; border-radius:12px; background:#f7fbff; border:1px solid #e6eefc;
  }
  .sk-wrap .dataTables_wrapper .dataTables_paginate .paginate_button{
    border:1px solid #dfeaff; border-radius:10px; padding:.35rem .7rem; margin:0 3px; background:#fff !important; color:#0e204a !important;
    box-shadow:0 6px 12px rgba(0,0,0,.04);
  }
  .sk-wrap .dataTables_wrapper .dataTables_paginate .paginate_button:hover{ border-color:#cfe0ff; background:#ffffff !important; }
  .sk-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.current{
    background:linear-gradient(135deg, var(--blue-pastel,#B1F0F7), #eaf6ff) !important; color:#001A6E !important; border-color:#badcff;
  }
</style>

<div class="sk-wrap">
  <div class="sk-container">

    <!-- Breadcrumb -->
    <nav class="sk-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <span class="sep">/</span>
      <a href="<?= base_url('master') ?>">Master</a>
      <span class="sep">/</span>
      <span>Master Syarat & Ketentuan</span>
    </nav>

    <!-- Head -->
    <header class="sk-head">
      <h1>Master Syarat &amp; Ketentuan</h1>
      <p>Kelola memo/S&amp;K yang tampil di aplikasi <strong>I Love Emas</strong>.</p>
    </header>

    <!-- Actions -->
    <div class="sk-actions">
      <a href="<?= base_url('master/addMemo') ?>" class="btn-add">
        <span class="ico"><i class="fas fa-plus"></i></span>
        <span>Tambah S&amp;K</span>
      </a>
      <a href="<?= base_url('master') ?>" class="btn-soft">
        <i class="fas fa-arrow-left"></i> Kembali ke Master
      </a>
    </div>

    <!-- Flash success -->
    <?php if($this->session->userdata('status')==='success'): ?>
      <div style="margin:10px 0 0;">
        <span class="sk-badge" style="border-color:#c9f6d1;background:#ecffef;color:#155724;">
          <?= html_escape($this->session->userdata('message')) ?>
        </span>
      </div>
      <?php $this->session->set_userdata(['status'=>'','message'=>'']); ?>
    <?php endif; ?>

    <!-- List card -->
    <section class="sk-card" aria-label="Syarat & Ketentuan">
      <div class="sk-card-hd">
        <h2>Data Syarat &amp; Ketentuan</h2>
        <span class="sk-badge">Total: <?= isset($memo) ? count($memo) : 0; ?></span>
      </div>
      <div class="sk-table-wrap">
        <div class="table-responsive">
          <table id="memoTable" class="table table-borderless sk-table">
            <thead>
              <tr>
                <th class="sk-col-no">No</th>
                <th class="sk-col-action">Aksi</th>
                <th>Syarat &amp; Ketentuan</th>
                <th>Prioritas</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($memo)): ?>
                <?php foreach ($memo as $i => $m): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td>
                      <a href="<?= base_url('master/detailMemo/'.$m->tm_id.'/') ?>" class="btn btn-primary btn-circle btn-sm mr-1" title="Detail">
                        <i class="fas fa-info"></i>
                      </a>
                      <a href="<?= base_url('master/deleteMemo/'.$m->tm_id.'/') ?>" class="btn btn-danger btn-circle btn-sm js-del" title="Hapus">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                    <td><?= $m->tm_value ?></td>
                    <td><?= $m->tm_priority ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </div>
</div>

<script>
(function bootMemoDT(){
  // tunggu jQuery & DataTables siap
  if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) {
    return setTimeout(bootMemoDT, 50);
  }
  var $ = jQuery;

  // CSRF
  var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
  var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

  var $tbl = $('#memoTable');

  // reset jika sudah pernah di-init
  if ($.fn.dataTable.isDataTable($tbl)) {
    $tbl.DataTable().destroy();
    $tbl.find('tbody').empty();
  }
  $.fn.dataTable.ext.errMode = 'console';

  // DataTables (server-side)
  var dt = $tbl.DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "<?= site_url('master/memo-dt'); ?>",
      type: "POST",
      data: function (d) {
        d[csrfName] = csrfHash;          // kirim token
      },
      dataSrc: function (json) {          // putar token jika ada di response
        if (json && json[csrfName]) csrfHash = json[csrfName];
        return (json && json.data) ? json.data : [];
      },
      error: function (xhr) {
        console.error('DT ajax error:', xhr.status, xhr.responseText);
      }
    },
    order: [[3, 'asc']],                  // urutkan by priority
    columns: [
      { data: 0, orderable: false },                 // No
      { data: 1, orderable: false, searchable: false }, // Aksi
      { data: 2 },                                   // S&K
      { data: 3 }                                    // Priority
    ],
    pageLength: 10,
    lengthMenu: [[10,25,50,-1],[10,25,50,"Semua"]],
    language: {
      search: "Cari:", lengthMenu: "Tampilkan _MENU_ entri",
      info: "Menampilkan _START_–_END_ dari _TOTAL_ entri",
      infoEmpty: "Tidak ada data", zeroRecords: "Tidak ditemukan data yang cocok",
      paginate: { first:"Pertama", last:"Terakhir", next:"Berikutnya", previous:"Sebelumnya" }
    }
  });

  // update badge total jika ada
  $tbl.on('xhr.dt', function (e, s, json) {
    if (json && typeof json.recordsFiltered !== 'undefined') {
      $('#badgeTotal').text('Total: ' + json.recordsFiltered);
    }
  });

  // konfirmasi hapus
  $(document).on('click', '.js-del', function(e){
    e.preventDefault();
    if (confirm('Hapus entri ini? Aksi tidak dapat dibatalkan.')) {
      location.href = this.href;
    }
  });
})();
</script>

