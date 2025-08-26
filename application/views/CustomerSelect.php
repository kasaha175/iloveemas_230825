<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  /* ===== Scoped agar tidak bentrok ===== */
  .tx-select-scope .wrap{ margin-top: calc(var(--topbar-h,72px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .tx-select-scope .container-max{ max-width: 760px; margin-inline:auto; }

  /* Breadcrumb pil */
  .tx-select-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .tx-select-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .tx-select-scope .crumbs a:hover{ color:var(--blue-light, #4a7dff); text-decoration:underline; }
  .tx-select-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .tx-select-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .tx-select-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Card */
  .tx-select-scope .card{
    border-radius:16px; border:1px solid #e6eefc; overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.08); background:#fff;
  }
  .tx-select-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #e9faff);
    border-bottom:1px solid #dfeaff;
  }
  .tx-select-scope .card-header h6{ margin:0; color:#0e204a; font-weight:800; }

  /* Select2 look & feel */
  .tx-select-scope .select2-container{ width:100%!important; }
  .tx-select-scope .select2-container .select2-selection--single{ height:48px; border-radius:12px; border:1px solid #e3e6ef; }
  .tx-select-scope .select2-selection__rendered{ line-height:46px !important; padding-left:14px !important; }
  .tx-select-scope .select2-selection__arrow{ height:46px !important; right:10px !important; }
  .tx-select-scope .select2-container .select2-selection--single:focus,
  .tx-select-scope .select2-container--default.select2-container--focus .select2-selection--single{
    border-color:#b9d6ff; box-shadow:0 0 0 3px rgba(51,136,255,.15);
  }

  /* Tombol bawah */
  .tx-select-scope .actions{ display:grid; grid-template-columns:1fr 1fr; gap:12px; }
  @media (max-width:576px){ .tx-select-scope .actions{ grid-template-columns:1fr; } }

  /* Overlay Loading kecil */
  .tx-select-overlay{
    position: fixed; inset: 0; background: rgba(15,23,42,.35);
    display:flex; align-items:center; justify-content:center;
    z-index: 5000; opacity:0; visibility:hidden; pointer-events:none;
    transition:.2s; backdrop-filter: saturate(110%) blur(1px);
  }
  .tx-select-overlay.show{ opacity:1; visibility:visible; pointer-events:auto; }
  .tx-select-overlay .box{
    background:#fff; border:1px solid #e6eefc; border-radius:14px;
    padding:16px 20px; min-width:220px; display:flex; gap:12px; align-items:center;
    box-shadow:0 12px 30px rgba(0,0,0,.18);
  }
  .tx-select-overlay .box i{ font-size:22px; color:#2563eb; }
  .tx-select-overlay .txt{ font-weight:700; color:#0e204a; }
</style>

<div class="tx-select-scope">
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
        <p>Pilih customer terlebih dahulu untuk memulai transaksi.</p>
      </header>

      <!-- Card -->
      <section class="card mt-3">
        <div class="card-header py-3">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="m-0">Buat Transaksi Baru</h6>
            <a href="<?= base_url('transaction/new-customer') ?>" class="btn btn-info btn-sm">
              <i class="fas fa-user-plus mr-1"></i> Tambah Customer
            </a>
          </div>
        </div>

        <div class="card-body">
          <div class="form-group">
            <label class="font-weight-bold" for="u_name">- Pilih Customer -</label>
            <select id="u_name" class="form-control select2" style="width:100%">
              <option value="" disabled selected>Please select customer...</option>
            </select>
            <small class="form-text text-muted">Ketik minimal 2 huruf untuk mencari.</small>
          </div>

          <div class="actions">
            <a id="sell" href="#!" class="btn btn-warning btn-lg shadow-sm">
              <i class="fas fa-tag mr-1"></i> Sell
            </a>
            <a id="buy" href="#!" class="btn btn-success btn-lg shadow-sm">
              <i class="fas fa-shopping-bag mr-1"></i> Buy
            </a>
          </div>

          <div class="mt-3">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-block">
              <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
          </div>
        </div>
      </section>

    </div>
  </div>
</div>

<!-- Overlay -->
<div class="tx-select-overlay" id="txOverlay" role="status" aria-live="polite" aria-hidden="true">
  <div class="box">
    <i class="fas fa-circle-notch fa-spin"></i>
    <span class="txt" id="txOverlayTxt">Loading…</span>
  </div>
</div>

<script>
(function waitLib(n){
  var ok = !!(window.jQuery && jQuery.fn && jQuery.fn.select2);
  if (ok) { init(); return; }
  if (n > 60) { console.error('jQuery/Select2 tidak tersedia'); return; }
  setTimeout(function(){ waitLib(n+1); }, 100);
})(0);

function init(){ (function ($) {
  'use strict';

  var ENDPOINT = "<?= site_url('transaction/customers') ?>";
  var baseUrl  = "<?= base_url() ?>";
  var $sel     = $('#u_name');

  console.log('%c[BOOT] jQuery & Select2 siap', 'color:#16a34a');

  // ============== DEBUG MANUAL PING SEKALI (sanity check) =================
  $.get(ENDPOINT, {search:'ab', page:1})
    .done(function(d){ console.log('[PING] GET', ENDPOINT, '-> OK:', d); })
    .fail(function(xhr){ console.warn('[PING] GET', ENDPOINT, '-> FAIL', xhr.status, xhr.responseText); });

  // ====================== SELECT2 + DEBUG LENGKAP ==========================
  $sel.select2({
    width: '100%',
    placeholder: 'Please select customer...',
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
      url: ENDPOINT,
      dataType: 'json',
      delay: 250,
      data: function (params) {
        console.log('[S2->data] term="%s" page=%s', params.term, params.page || 1);
        return {
          search: params.term || '',
          page: params.page || 1
        };
      },
      processResults: function (data, params) {
        console.log('[S2<-resp] raw:', data);
        params.page = params.page || 1;

        // normalisasi array
        var list = Array.isArray(data && data.results)
          ? data.results
          : (data && data.results ? Object.values(data.results) : []);

        var items = list.map(function (r) {
          return {
            id: String(r.c_id),
            text: (r.c_id ?? '') + ' - ' + (r.c_name ?? '') + ' - ' + (r.c_id_number ?? '')
          };
        });

        return {
          results: items,
          pagination: { more: !!(data && data.pagination && data.pagination.more) }
        };
      },
      cache: true
    }
  });

  // log ketika dropdown dibuka + apa yang diketik user
  $sel.on('select2:open', function(){
    console.log('[S2] open');
    setTimeout(function(){
      var $input = $('.select2-container--open .select2-search__field');
      $input.off('input.__dbg').on('input.__dbg', function(){
        console.log('[S2] typing:', this.value);
      });
    }, 0);
  });

  // log saat user memilih satu baris
  $sel.on('select2:select', function(e){
    console.log('[S2] selected:', e.params.data);
    // kirim ke server untuk simpan ke session
    $.post(baseUrl + "transaction/updateLive", { id: e.params.data.id })
      .done(function(){ console.log('[updateLive] OK'); })
      .fail(function(xhr){ console.warn('[updateLive] FAIL', xhr.status, xhr.responseText); });
  });

  // tombol
  function mustPick(){
    if (!$sel.val()) { alert('Please select customer!'); return true; }
    return false;
  }
  $('#sell').on('click', function(e){ e.preventDefault(); if (mustPick()) return; window.location.href = baseUrl + "transaction/sell"; });
  $('#buy').on('click',  function(e){ e.preventDefault(); if (mustPick()) return; window.location.href = baseUrl + "transaction/buy";  });

})(jQuery); }
</script>
