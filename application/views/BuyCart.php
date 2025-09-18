<?php
// ===== Helper total & format =====
$total = 0;
foreach ($this->cart->contents() as $row) {
  $opt = $row['options'] ?? [];
  if (isset($opt['price_total']) && is_numeric($opt['price_total'])) {
    $total += (float)$opt['price_total'];
  } elseif (isset($opt['priceTotal']) && is_numeric($opt['priceTotal'])) {
    $total += (float)$opt['priceTotal'];
  } else {
    $total += (float)($row['subtotal'] ?? 0);
  }
}
function nominal($angka){ return number_format(floor((float)$angka), 0, ',', '.'); }

// icon & param
$iconBack = $this->config->item('iconBack') ?? 'fas fa-arrow-left';

// ambil dari controller (fallback aman jika belum ada)
$mSeg   = isset($mSeg)   ? (string)$mSeg   : (string)$this->uri->segment(3);
$tParam = isset($tParam) ? (string)$tParam : preg_replace('/[^a-z]/i','', $this->input->get('t') ?: '');

// normalisasi customer (FOKUS: gunakan idCustomer)
$customer     = isset($customer) && is_object($customer) ? $customer : null;
$nameCustomer = $customer ? ($customer->c_name ?? '-') : '-';

// c_id dari object customer → fallback ke session idCustomer → fallback ke ?cid=
$userId = $customer ? (int)($customer->c_id ?? 0) : 0;
if (!$userId) {
  $userId = (int)($this->session->userdata('idCustomer') ?: $this->input->get('cid'));
}

// (opsional) jika proyek lama memakai $customer_id (kompatibilitas variabel)
$customer_id = $userId;
$cidQuery    = (int)$userId; // untuk appended query pada URL
?>
<style>
  :root{
    --blue-light:#074799; --blue-dark:#001A6E; --blue-pastel:#B1F0F7;
    --text:#0B0F1A; --muted:#6b7a99;
    --card-bg:#fff; --card-br:#e6eefc;
    --shadow:0 18px 30px rgba(0,0,0,.10);
    --radius:18px; --topbar-h:72px;
  }

  /* ===== Boot & Overlay ===== */
  .boot-hide{opacity:0;visibility:hidden;}
  .app-overlay{
    position:fixed; inset:0; z-index:4000;
    background: rgba(255,255,255,.45);
    backdrop-filter: blur(8px) saturate(115%);
    -webkit-backdrop-filter: blur(8px) saturate(115%);
    display:flex; align-items:center; justify-content:center;
    transition: opacity .25s ease, visibility .25s ease;
  }
  .app-overlay.hidden{ opacity:0; visibility:hidden; pointer-events:none; }
  .loader{
    display:flex; flex-direction:column; align-items:center; gap:12px;
    padding:18px 22px; border-radius:16px; background:rgba(255,255,255,.9); border:1px solid #e6eefc;
    box-shadow:0 8px 24px rgba(0,0,0,.10);
  }
  .ring{
    width:46px; height:46px; border-radius:50%;
    border:3px solid rgba(0,26,110,.25); border-top-color: var(--blue-dark);
    animation: spin 1s linear infinite;
  }
  @keyframes spin{ to { transform: rotate(360deg); } }
  .loader-label{ font-size:13px; color:#0b1f4f; font-weight:700; }

  .ilv-check{ padding:clamp(14px,2vw,24px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1200px; margin-inline:auto; }

  /* HERO */
  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(16px,3vw,28px);
    box-shadow:var(--shadow);
  }
  .ilv-crumbs{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20);
    border-radius:999px; padding:8px 12px; width:max-content;
    backdrop-filter:blur(6px);
  }
  .ilv-crumbs a{ color:#e8f2ff; text-decoration:none; font-weight:600; }
  .ilv-crumbs .sep{ color:#c9defe; opacity:.7; }
  .ilv-hero h1{ margin:8px 0 4px; font-weight:800; font-size:clamp(18px,3vw,28px); }
  .ilv-hero p{ margin:0; opacity:.95; font-size:clamp(12px,1.2vw,14px); }
  .ilv-head{ margin-top:12px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }

  /* Tombol Back */
  .ilv-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#fff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:999px; padding:7px 12px; text-decoration:none; font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition:transform .12s ease, box-shadow .2s ease, border-color .2s ease;
    white-space:nowrap;
  }
  .ilv-back:hover{ transform:translateY(-1px); box-shadow:0 14px 26px rgba(0,0,0,.12); border-color:#d7e5ff; }

  /* Layout 2 kolom */
  .ilv-grid{
    margin-top: clamp(14px,2vw,20px);
    display:grid; grid-template-columns: 1fr; gap:14px;
  }
  @media (min-width: 992px){ .ilv-grid{ grid-template-columns: 380px 1fr; } }

  /* Card */
  .ilv-card{
    background:var(--card-bg); border:1px solid var(--card-br);
    border-radius:16px; overflow:hidden; box-shadow:0 10px 24px rgba(0,0,0,.08);
  }
  .ilv-card .ilv-card-head{
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border-bottom:1px solid #dfeaff; padding:12px 16px;
  }
  .ilv-card .ilv-card-head h6{ margin:0; color:#0e204a; font-weight:800; font-size:15px; }
  .ilv-card .ilv-card-body{ padding:16px; }

  /* Form */
  .form-label{ font-weight:700; font-size:13px; color:#0e204a; margin-bottom:8px; }
  .form-control, .select2-container .select2-selection--single{
    height:46px; border-radius:12px; border:1px solid #e3e6ef; font-size:14px;
  }
  .select2-selection__rendered{ line-height:44px !important; padding-left:14px !important; }
  .select2-selection__arrow{ height:44px !important; right:10px !important; }
  .select2-container--default.select2-container--focus .select2-selection--single,
  .form-control:focus{
    border-color:#b9d6ff; box-shadow:0 0 0 3px rgba(51,136,255,.15);
  }

  .ilv-actions{ display:flex; gap:10px; flex-wrap:wrap; }

  /* Tabel */
  .table thead th{ white-space:nowrap; background:#f7fbff; border-color:#e6eefc; }
  .table td, .table th{ vertical-align:middle; }
  .table-responsive{ border:1px solid #eef3ff; border-radius:12px; overflow:hidden; }

  /* Footer actions */
  .ilv-footer-actions{ display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; }

  /* Buttons */
  .btn-soft{ border:1px solid #e6eefc; background:#fff; color:#0e204a; }
  .btn-soft:hover{ border-color:#d7e5ff; background:#fff; box-shadow:0 8px 18px rgba(0,0,0,.08); }

  /* wrapper & spinner untuk input email (dibuat via JS) */
  .input-loading { position: relative; }
  .input-loading .spinner-border {
    position: absolute; right: 10px; top: 50%;
    transform: translateY(-50%);
    width: 1rem; height: 1rem; border-width: .15rem;
    display: none;
  }
  .input-loading.loading .spinner-border { display: inline-block; }

  /* feedback invalid bootstrap look */
  #emailToInput.is-invalid { border-color: #dc3545; }
  #emailError { display:none; color:#dc3545; font-size: .8125rem; margin-top: .25rem; }
  #emailError.show { display:block; }
</style>

<!-- Overlay boot -->
<div id="pageOverlay" class="app-overlay" aria-live="polite" aria-busy="true">
  <div class="loader">
    <div class="ring" aria-hidden="true"></div>
    <div class="loader-label">Memuat komponen…</div>
  </div>
</div>

<!-- SELURUH HALAMAN dibungkus agar tidak muncul sebelum JS siap -->
<div id="appRoot" class="boot-hide">

  <div class="ilv-check">
    <div class="ilv-container">

      <!-- HERO -->
      <section class="ilv-hero" aria-label="Buy — Checkout Session">
        <nav aria-label="breadcrumb">
          <div class="ilv-crumbs">
            <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i>&nbsp;Dashboard</a>
            <span class="sep">›</span>
            <a href="<?= base_url('transaction') ?>">Transaction</a>
            <span class="sep">›</span>
            <a href="<?= base_url('transaction/buy'.($cidQuery?'?cid='.$cidQuery:'')) ?>">Buy</a>
            <span class="sep">›</span>
            <span style="opacity:.9">Cart</span>
          </div>
        </nav>

        <div class="ilv-head">
          <div>
            <h1>Buy — Checkout Session</h1>
            <p>Review item dan lanjutkan proses checkout. Customer: <strong><?= htmlspecialchars($nameCustomer ?? '-', ENT_QUOTES) ?></strong></p>
          </div>
          <a href="<?= base_url('transaction/buy'.($cidQuery?'?cid='.$cidQuery:'')) ?>" class="ilv-back" aria-label="Kembali ke daftar material">
            <i class="<?= htmlspecialchars($iconBack, ENT_QUOTES) ?>"></i> Kembali
          </a>
        </div>
      </section>

      <!-- GRID: Kiri form detail, kanan cart -->
      <div class="ilv-grid">

        <!-- KIRI: FORM INPUT MATERIAL -->
        <section class="ilv-card">
          <div class="ilv-card-head">
            <h6>MATERIAL DETAIL (<?= htmlspecialchars($materianName ?? '-', ENT_QUOTES) ?>)
              <?php if (!empty($tParam)): ?>
                <small style="font-weight:700;color:#365ea3"> — <?= strtoupper($tParam) ?> Material</small>
              <?php endif; ?>
            </h6>
          </div>
          <div class="ilv-card-body">
            <form action="<?= base_url('transaction/buy-add-to-cart/') ?>" method="post" autocomplete="off">
              <input type="hidden" name="idMaterial" value="<?= htmlspecialchars($mSeg, ENT_QUOTES) ?>">
              <input type="hidden" name="types" value="<?= htmlspecialchars($tParam, ENT_QUOTES) ?>">

              <?php if (in_array($mSeg, ["2","5","6","7","8","9","10"])): ?>
                <div class="form-group">
                  <label class="form-label">TYPE</label>
                  <select name="materialType" required class="select2 form-control" style="width:100%">
                    <?php foreach ($materialType as $m): ?>
                      <option><?= htmlspecialchars($m->mt_name, ENT_QUOTES) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>

              <?php if (in_array($mSeg, ["5"])): ?>
                <div class="form-group">
                  <label class="form-label">PRESENTASI</label>
                  <input type="number" step="any" inputmode="decimal" pattern="[0-9]*" name="carat" required class="form-control aang">
                </div>
              <?php endif; ?>

              <?php if (in_array($mSeg, ["2","23"])): ?>
                <div class="form-group">
                  <label class="form-label">CARAT</label>
                  <select name="carat" required class="select2 form-control" style="width:100%">
                    <?php foreach ($carat as $c): ?>
                      <option><?= htmlspecialchars($c->c_name, ENT_QUOTES) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>

              <?php if (in_array($mSeg, ["6","7","8","9","10","19","21"])): ?>
                <div class="form-group">
                  <label class="form-label">PERCENTAGE</label>
                  <input type="number" step="any" inputmode="decimal" pattern="[0-9]*" name="percentage" required class="form-control aang">
                </div>
              <?php endif; ?>

              <?php if ($mSeg === "1"): ?>
                <div class="form-group">
                  <label class="form-label">PRICE</label>
                  <input type="number" step="any" inputmode="decimal" pattern="[0-9]*" name="price" required class="form-control aang">
                </div>
              <?php endif; ?>

              <?php if ($mSeg === "3"): ?>
                <div class="form-group">
                  <label class="form-label">POTONGAN</label>
                  <select required class="select2 form-control" name="tahun_potongan" style="width:100%">
                    <option value="">Pilih Potongan</option>
                    <?php for ($th=2018; $th<=date('Y')+1; $th++): ?>
                      <option value="<?= $th ?>">LM Certi <?= $th ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              <?php endif; ?>

              <?php if (in_array($mSeg, ["2","3","4","5","6","7","8","9","10","17","18","19","23","21"])): ?>
                <div class="form-group">
                  <label class="form-label">WEIGHT</label>
                  <input type="number" step="any" inputmode="decimal" pattern="[0-9]*" name="weight" required class="form-control aang">
                </div>
              <?php endif; ?>

              <div class="ilv-actions">
                <button type="submit" class="btn btn-primary btn-block">Add To Cart</button>
              </div>
            </form>
          </div>
        </section>

        <!-- KANAN: CART -->
        <section class="ilv-card">
          <div class="ilv-card-head">
            <h6>BUY CART (<?= htmlspecialchars($nameCustomer ?? '-', ENT_QUOTES) ?>) — <span style="color:#0b1f4f">Rp <?= nominal($total) ?></span></h6>
          </div>
          <div class="ilv-card-body">
            <div class="table-responsive">
              <table class="table table-bordered mb-2" width="100%" cellspacing="0" style="font-size:14px;">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Material</th>
                    <th>Type</th>
                    <th>Carat</th>
                    <th>Weight</th>
                    <th>Price/Gr</th>
                    <th>Total Price</th>
                    <th>Type Material</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php $no=0; foreach($this->cart->contents() as $a): $no++; $opt = $a['options'] ?? []; ?>
                  <tr>
                    <td><?= $no ?></td>
                    <td><?= htmlspecialchars($opt['materialName'] ?? $a['name'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($opt['materialType'] ?? '', ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($opt['carat'] ?? '', ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($opt['weight'] ?? '', ENT_QUOTES) ?></td>
                    <td>
                      <?php
                        $isDiamond = (strtoupper($opt['materialName'] ?? $a['name']) === 'DIAMOND');
                        echo $isDiamond ? (float)$a['price'] : nominal($a['price']);
                      ?>
                    </td>
                    <td><?= nominal( isset($opt['priceTotal']) && is_numeric($opt['priceTotal']) ? $opt['priceTotal'] : ($a['subtotal'] ?? 0) ) ?></td>
                    <td><?= htmlspecialchars($opt['types'] ?? ($tParam ? ucwords($tParam) : '-'), ENT_QUOTES) ?></td>
                    <td class="text-center">
                      <a href="<?= base_url('transaction/buy-add-to-cart-reset/?idMaterial='.$mSeg.'&idRow='.$a['rowid'].'&t='.$tParam.($cidQuery?'&cid='.$cidQuery:'')) ?>"
                         class="btn btn-danger btn-sm" title="Remove">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <form action="<?= base_url('transaction/buy-checkout/') ?>" class="mt-3">
              <!-- user_id = c_id (idCustomer) -->
              <input type="hidden" name="user_id" id="user_id" value="<?= (int)$userId ?>">
              <input type="hidden" name="notifyEmail" id="notifyEmail" value="0">
              <input type="hidden" name="emailTo" id="emailToHidden" value="">
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label class="form-label">PLUS / MINUS BIAYA ADMIN</label>
                  <select class="form-control" name="operator">
                    <option value="+">Plus (+)</option>
                    <option value="-">Minus (-)</option>
                  </select>
                </div>
                <div class="form-group col-md-8">
                  <label class="form-label">Nominal</label>
                  <input type="number" step="any" inputmode="decimal" pattern="[0-9]*" class="form-control biayaAdmin" name="biayaAdmin" placeholder="0">
                </div>
              </div>

              <!-- Actions -->
              <div class="ilv-footer-actions">
                <a href="#" data-toggle="modal" data-target="#resetModal" class="btn btn-outline-danger">
                  <i class="fa fa-times"></i> Reset
                </a>
                <a href="#" data-toggle="modal" data-target="#checkoutModal" class="btn btn-primary">
                  <i class="fa fa-check"></i> Checkout
                </a>
              </div>

              <!-- Modal Checkout -->
              <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document" style="top:84px;">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Ready to Checkout?</h5>
                      <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <p>Pilih "Checkout" untuk menyelesaikan sesi cart.</p>

                      <!-- Opsi email -->
                      <div class="custom-control custom-radio mb-2">
                        <input type="radio" name="optEmail" id="optYes" class="custom-control-input" value="yes">
                        <label class="custom-control-label" for="optYes">Ya, kirim invoice via email</label>
                      </div>
                      <div class="custom-control custom-radio mb-2">
                        <input type="radio" name="optEmail" id="optNo" class="custom-control-input" value="no" checked>
                        <label class="custom-control-label" for="optNo">Tidak, lanjutkan saja</label>
                      </div>

                      <div id="emailFieldWrap" class="mt-2" style="display:none">
                        <label for="emailToInput" class="mb-1">Email tujuan</label>
                        <div class="input-loading">
                          <input type="email" id="emailToInput"
                                class="form-control form-control-sm"
                                placeholder="nama@email.com"
                                autocomplete="email">
                          <div class="spinner-border" role="status" aria-hidden="true"></div>
                        </div>
                        <small class="text-muted d-block mt-1" id="emailHint" style="display:none">
                          Diisi otomatis dari data customer.
                        </small>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-soft" type="button" data-dismiss="modal">Cancel</button>
                      <button type="button" id="btnModalProceed" class="btn btn-success">Checkout</button>
                    </div>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </section>
      </div>

    </div>
  </div>
</div>

<!-- Modal Reset -->
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="top:84px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ready to Reset?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">Pilih "Reset" untuk mengosongkan sesi cart.</div>
      <div class="modal-footer">
        <button class="btn btn-soft" type="button" data-dismiss="modal">Cancel</button>
        <a class="btn btn-danger" href="<?= base_url('transaction/buy-add-to-cart-reset/?idMaterial='.$mSeg.'&t='.$tParam.($cidQuery?'&cid='.$cidQuery:'')) ?>">Reset</a>
      </div>
    </div>
  </div>
</div>

<!-- JS Boot: tunggu jQuery + plugin, lalu tampilkan halaman -->
<script>
(function boot(n){
  var hasJQ = !!window.jQuery;
  if (hasJQ) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function(){ init(window.jQuery); });
    } else { init(window.jQuery); }
    return;
  }
  if (n > 80) { reveal(); return; }
  setTimeout(function(){ boot(n+1); }, 100);
})(0);

function reveal(){
  var root = document.getElementById('appRoot');
  var overlay = document.getElementById('pageOverlay');
  if (root){ root.classList.remove('boot-hide'); }
  if (overlay){ overlay.classList.add('hidden'); overlay.setAttribute('aria-busy','false'); }
}

function init($){
  // === CSRF setup (CI3) ===
  var CSRF = {
    name : "<?= $this->security->get_csrf_token_name() ?>",
    value: "<?= $this->security->get_csrf_hash() ?>",
  };
  $.ajaxSetup({
    beforeSend: function(xhr, settings){
      if (settings.type && settings.type.toUpperCase() === 'POST') {
        if (typeof settings.data === 'string') {
          var pair = encodeURIComponent(CSRF.name)+'='+encodeURIComponent(CSRF.value);
          settings.data = settings.data ? settings.data + '&' + pair : pair;
        } else if ($.isPlainObject(settings.data)) {
          settings.data[CSRF.name] = CSRF.value;
        } else if (settings.data == null) {
          settings.data = {}; settings.data[CSRF.name] = CSRF.value;
        }
      }
    },
    complete: function(xhr){
      try{
        var res = xhr.responseJSON || JSON.parse(xhr.responseText);
        if (res && res[CSRF.name]) { CSRF.value = res[CSRF.name]; }
      }catch(_){}
    }
  });

  if ($.fn.select2){ $('.select2').select2({ width:'100%' }); }

  if ($.fn.keyboard){
    var $nums = $('.aang, .biayaAdmin');
    $nums.keyboard({ layout: 'num', restrictInput:true, preventPaste:true, autoAccept:true });
    $(document).on('focus click', '.aang, .biayaAdmin', function(){
      var kb = $(this).getkeyboard && $(this).getkeyboard();
      if (kb && kb.isOpen !== true) { kb.reveal(); }
    });
  }

  (function checkoutEmailFlow(){
    var CHECK_EMAIL = '<?= base_url('transaction/check-email') ?>';
    var SAVE_EMAIL  = '<?= base_url('transaction/save-email') ?>';
    var HEALTH_URL  = '<?= base_url('health/ping') ?>';

    var DEBUG_EMAIL_SAVE = false;

    function pingOnline(){
      return $.ajax({ url: HEALTH_URL, type:'GET', dataType:'json', timeout:3000 });
    }

    var $checkoutForm = $('form[action*="transaction/buy-checkout/"]').last();
    if (!$checkoutForm.length) return;

    var $btnProceed = $('#btnModalProceed');

    if (!$('#emailError').length){
      $('<div id="emailError">Email wajib diisi dan format harus valid.</div>').insertAfter('#emailHint');
    }

    function setProceedDisabled(dis){
      $btnProceed.prop('disabled', !!dis)
                 .toggleClass('disabled', !!dis)
                 .attr('aria-disabled', !!dis);
    }

    function ensureSpinner(){
      var $inp = $('#emailToInput');
      if (!$inp.parent().hasClass('input-loading')){
        $inp.wrap('<div class="input-loading"></div>');
        $inp.after('<div class="spinner-border" role="status" aria-hidden="true"></div>');
      }
      return $inp.closest('.input-loading');
    }

    function emailLoading(on){
      var $wrap = ensureSpinner();
      var $inp  = $('#emailToInput');
      if (on){
        $wrap.addClass('loading');
        $inp.prop('disabled', true).attr('placeholder','Mengambil email…');
        $('#emailHint').hide();
      } else {
        $wrap.removeClass('loading');
        $inp.prop('disabled', false).attr('placeholder','nama@email.com');
      }
    }

    function validateEmailField(){
      var choiceYes = $('input[name="optEmail"]:checked').val() === 'yes';
      if (!choiceYes){
        $('#emailToInput').removeClass('is-invalid');
        $('#emailError').removeClass('show');
        setProceedDisabled(false);
        return true;
      }
      var email = $.trim($('#emailToInput').val() || '');
      var ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      if (!email || !ok){
        $('#emailToInput').addClass('is-invalid');
        $('#emailError').addClass('show');
        setProceedDisabled(true);
        return false;
      } else {
        $('#emailToInput').removeClass('is-invalid');
        $('#emailError').removeClass('show');
        setProceedDisabled(false);
        return true;
      }
    }

    // reset saat modal dibuka
    $('#checkoutModal').off('shown.bs.modal.checkout').on('shown.bs.modal.checkout', function(){
      $('#optNo').prop('checked', true);
      $('#emailFieldWrap').hide();
      $('#emailToInput').val('');
      $('#emailHint').hide();
      $('#emailError').removeClass('show');
      setProceedDisabled(false);
    });

    // pilih Ya/Tidak
    $('input[name="optEmail"]').off('change.checkout').on('change.checkout', function(){
      if (this.value !== 'yes'){
        $('#emailFieldWrap').hide();
        $('#emailError').removeClass('show');
        setProceedDisabled(false);
        return;
      }

      emailLoading(true);
      var jq = pingOnline();
      if (!jq || typeof jq.done !== 'function'){
        $('#emailFieldWrap').hide();
        setProceedDisabled(true);
        emailLoading(false);
        return;
      }

      jq.done(function(){
          $('#emailFieldWrap').show();
          prefillEmail();
        })
        .fail(function(){
          $('#emailFieldWrap').hide();
          setProceedDisabled(true);
        })
        .always(function(){ emailLoading(false); });
    });

    // Ambil email dari server (prefill) — KIRIMKAN user_id = c_id
    function prefillEmail(){
      emailLoading(true);
      var uid = $.trim($('#user_id', $checkoutForm).val() || '');
      $.ajax({
        url: CHECK_EMAIL,
        type: 'POST',
        dataType: 'json',
        data: { user_id: uid } // ← konsisten idCustomer
      })
      .done(function(res){
        if (res && res.ok && res.email){
          $('#emailToInput').val(res.email);
          $('#emailHint').show();
        } else {
          $('#emailToInput').val('');
          $('#emailHint').hide();
        }
      })
      .fail(function(){
        $('#emailToInput').val('');
        $('#emailHint').hide();
      })
      .always(function(){
        emailLoading(false);
        validateEmailField();
      });
    }

    // live validate
    $(document).off('input.checkout keyup.checkout blur.checkout', '#emailToInput')
               .on('input.checkout keyup.checkout blur.checkout', '#emailToInput', validateEmailField);

    // helper submit akhir
    function proceedSubmit(email){
      $('#notifyEmail', $checkoutForm).val('1');
      $('#emailToHidden', $checkoutForm).val(email);
      $('#checkoutModal').modal('hide');
      $checkoutForm.trigger('submit');
    }

    // Klik Checkout
    $btnProceed.off('click.checkout').on('click.checkout', function(){
      if ($btnProceed.is(':disabled')) return;

      var choice = $('input[name="optEmail"]:checked').val();
      var uid    = $.trim($('#user_id', $checkoutForm).val() || '');
      var email  = $.trim($('#emailToInput').val() || '');

      if (choice === 'no'){
        $('#notifyEmail', $checkoutForm).val('0');
        $('#emailToHidden', $checkoutForm).val('');
        $('#checkoutModal').modal('hide');
        $checkoutForm.trigger('submit');
        return;
      }

      if (!validateEmailField()) return;

      emailLoading(true);
      var jq = pingOnline();
      if (!jq || typeof jq.done !== 'function'){
        setProceedDisabled(true);
        emailLoading(false);
        return;
      }

      jq.done(function(){
          // SELALU update email customer (kirim user_id=c_id)
          var payload = { user_id: uid, email: email };
          $.ajax({
            url: '<?= base_url('transaction/save-email') ?>',
            type: 'POST',
            dataType: 'json',
            data: payload
          })
          .always(function(){
            // verifikasi ringan: cek ulang email tersimpan (kirim user_id=c_id)
            $.ajax({
              url: '<?= base_url('transaction/check-email') ?>',
              type: 'POST',
              dataType: 'json',
              data: { user_id: uid }
            })
            .always(function(){
              proceedSubmit(email);
              emailLoading(false);
            });
          });

        })
        .fail(function(){
          setProceedDisabled(true);
          emailLoading(false);
        });
    });
  })();

  // Semua siap → tampilkan halaman & tutup overlay
  reveal();
}
</script>
