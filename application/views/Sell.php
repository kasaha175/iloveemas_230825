<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
// ====== UI config ======
$ui       = $this->config->item('ui') ?? [];
$iconBack = $this->config->item('iconBack') ?? 'fas fa-arrow-left';

// ====== CUSTOMER DATA (obj/array/null) ======
$customerObj = isset($customer) ? $customer : null;
$customerData = [
  'id'               => null,
  'name'             => null,
  'id_number'        => null,
  'phone'            => null,
  'email'            => null,
  'address'          => null,
  'resident_address' => null,
  'no_order'         => null,
  'date_created'     => null,
];

if (is_object($customerObj) || is_array($customerObj)) {
  $src = (array)$customerObj;
  $customerData['id']               = $src['c_id']               ?? null;
  $customerData['name']             = $src['c_name']             ?? null;
  $customerData['id_number']        = $src['c_id_number']        ?? null;
  $customerData['phone']            = $src['c_phone']            ?? null;
  $customerData['email']            = $src['c_email']            ?? null;
  $customerData['address']          = $src['c_address']          ?? null;
  $customerData['resident_address'] = $src['c_resident_address'] ?? null;
  $customerData['no_order']         = $src['c_no_order']         ?? null;
  $customerData['date_created']     = $src['c_date_created']     ?? null;
}

// Fallback dari session / query kalau controller belum inject
if (empty($customerData['id'])) {
  $cidSess = (int) $this->session->userdata('idCustomer');
  $cidGet  = (int) $this->input->get('cid');
  $customerData['id'] = $cidSess ?: ($cidGet ?: null);
}

$hasCust   = !empty($customerData['id']) && (int)$customerData['id'] > 0;
// Perlu AJAX bila hanya punya ID tanpa detail lain
$needsAjax = $hasCust && empty($customerData['name']) && empty($customerData['phone']) && empty($customerData['email']);

// Endpoint JSON aman
$custInfoUrl = site_url('transaction/customer-info');

// Helper URL + persist cid
$buildSellUrl = function($mId) use ($hasCust, $customerData) {
  $u = base_url('transaction/sell/'.rawurlencode($mId).'/');
  if ($hasCust) {
    $u .= (strpos($u,'?')===false ? '?' : '&').'cid='.(int)$customerData['id'];
  }
  return $u;
};

// Debug ringan
if (ENVIRONMENT !== 'production') {
  echo "<script>console.log('[Sell-Choose] idCustomer=', ".json_encode((int)($customerData['id'] ?: 0)).", 'needAjax=', ".json_encode($needsAjax).");</script>";
}
?>

<style>
  :root{
    --blue-light:#074799; --blue-dark:#001A6E; --blue-pastel:#B1F0F7;
    --text:#0B0F1A; --muted:#6b7a99;
    --card-bg:#ffffff; --card-br:#e6eefc;
    --shadow:0 18px 30px rgba(0,0,0,.10);
    --radius:18px; --topbar-h:72px;
    --grid-min: clamp(150px, 16vw, 180px);
  }

  .ilv-sell{ padding:clamp(16px,2.2vw,28px); margin-top:var(--topbar-h); }
  .ilv-container{ max-width:1200px; margin-inline:auto; }

  /* HERO */
  .ilv-hero{
    position:relative; overflow:hidden; border-radius:var(--radius);
    background:linear-gradient(135deg,var(--blue-dark),var(--blue-light));
    color:#fff; padding:clamp(18px,3.2vw,28px);
    box-shadow: var(--shadow);
  }
  .ilv-crumbs{
    display:flex; gap:10px; align-items:center; width:max-content;
    background: rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22);
    padding:8px 12px; border-radius:9999px; margin-bottom:8px;
    backdrop-filter: blur(6px);
  }
  .ilv-crumbs a{ color:#e8f2ff; text-decoration:none; font-weight:700; font-size:13px; }
  .ilv-crumbs .sep{ color:#c9defe; opacity:.8; }

  .ilv-hero h1{ margin:0 0 4px; font-weight:800; font-size:clamp(20px,3.2vw,30px); }
  .ilv-hero p { margin:0; color:#dbe8ff; font-size:13px; }

  .ilv-head{
    margin-top:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;      /* <-- tambahkan baris ini */
    gap:12px;
    flex-wrap:wrap;
  }

  .ilv-back{
    display:inline-flex; align-items:center; gap:8px;
    background:#ffffff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:999px; padding:7px 12px; text-decoration:none; font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .ilv-back:hover{ transform: translateY(-1px); box-shadow:0 14px 26px rgba(0,0,0,.12); border-color:#d7e5ff; }

  /* CUSTOMER CARD (seragam dengan Buy) */
  .cust-card{
    margin-top:12px; background:#fff; color:#0b1f4f; border:1px solid #e6eefc;
    border-radius:16px; padding:12px 14px; box-shadow:0 10px 24px rgba(0,0,0,.08); max-width:820px;
  }
  .cust-title{ display:flex; align-items:center; gap:10px; margin:0 0 8px; }
  .cust-title .avatar{
    width:36px; height:36px; border-radius:50%;
    background:linear-gradient(135deg,#e6f1ff,#f6fbff); border:1px solid #dfeaff;
    display:grid; place-items:center; color:#2454a6;
  }
  .cust-title h6{ margin:0; font-weight:800; color:#0e204a; font-size:15px; }
  .cust-title small{ color:#6b7a99; font-weight:600; }

  .cust-grid{ display:grid; grid-template-columns:1fr; gap:6px 14px; }
  @media (min-width: 680px){ .cust-grid{ grid-template-columns:repeat(2, minmax(0,1fr)); } }
  .cust-row{ font-size:13.5px; color:#2a3d6b; display:flex; gap:8px; align-items:center; min-height:24px; }
  .cust-row b{ color:#0e204a; white-space:nowrap; }

  .cust-actions{ display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
  .btn-chip{
    display:inline-flex; align-items:center; gap:8px; border:1px solid #dfeaff; background:#fff;
    color:#0e204a; padding:6px 10px; border-radius:999px; font-weight:700; text-decoration:none;
  }

  /* GRID MATERIAL */
  .ilv-grid{
    margin-top: clamp(18px, 2.5vw, 24px);
    display:grid; grid-template-columns: repeat(auto-fit, minmax(var(--grid-min), 1fr));
    gap:16px;
  }
  .ilv-item{
    display:flex; flex-direction:column; text-decoration:none; color:var(--text);
    background:var(--card-bg); border:1px solid var(--card-br);
    border-radius:14px; overflow:hidden; min-height:180px;
    box-shadow:0 6px 14px rgba(0,0,0,.06);
    transition: transform .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
  }
  .ilv-item:hover{ transform: translateY(-2px); box-shadow:0 14px 28px rgba(0,0,0,.12); border-color:#d7e5ff; background:#fff; }
  .ilv-item.disabled{ opacity:.6; filter:saturate(.5) grayscale(.1); cursor:not-allowed; }
  .ilv-thumb{ display:grid; place-items:center; min-height:120px; padding:14px 12px 8px; background:linear-gradient(180deg,rgba(241,248,255,.65),#fff); }
  .ilv-thumb img{ width:80%; max-height:100px; object-fit:contain; border-radius:10px; }
  .ilv-caption{ text-align:center; padding:10px 12px; font-weight:800; color:#001A6E; border-top:1px solid var(--card-br); font-size:14px; }

  /* Footer */
  .ilv-meta{ margin-top:12px; display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between; font-size:12px; color:#26406e; }
  .ilv-badge{ background:#ffffff; color:#0e2b68; border:1px solid #dfeaff; padding:6px 10px; border-radius:999px; }

  @media (prefers-reduced-motion: reduce){
    .ilv-item, .ilv-back{ transition:none; }
  }
</style>

<div class="ilv-sell">
  <div class="ilv-container">
    <section class="ilv-hero" aria-label="Sell — Choose Material">
      <!-- Breadcrumb -->
      <nav class="ilv-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>" class="fa fa-home" aria-label="Home"></a>
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= base_url('transaction') ?>">Transaction</a>
        <span class="sep">›</span>
        <span>Sell</span>
      </nav>

      <header>
        <h1>Sell — Choose Material</h1>
        <p>Pilih jenis material untuk memulai transaksi penjualan.</p>
      </header>

      <div class="ilv-head">
        <?php if ($hasCust): ?>
          <div id="custCard" class="cust-card"
               data-cid="<?= (int)$customerData['id'] ?>"
               data-needs="<?= $needsAjax ? '1':'0' ?>">
            <?php if (!$needsAjax): ?>
              <div class="cust-title">
                <div class="avatar"><i class="fas fa-user"></i></div>
                <h6><?= htmlspecialchars($customerData['name'] ?? 'Customer', ENT_QUOTES) ?></h6>
                <small>(ID: <?= (int)$customerData['id'] ?>)</small>
              </div>
              <div class="cust-grid">
                <div class="cust-row"><b>No. Identitas:</b> <?= htmlspecialchars($customerData['id_number'] ?? '-', ENT_QUOTES) ?></div>
                <div class="cust-row"><b>No. Order:</b>     <?= htmlspecialchars($customerData['no_order']   ?? '-', ENT_QUOTES) ?></div>
                <div class="cust-row"><b>Phone:</b>         <?= htmlspecialchars($customerData['phone']     ?? '-', ENT_QUOTES) ?></div>
                <div class="cust-row"><b>Email:</b>         <?= htmlspecialchars($customerData['email']     ?? '-', ENT_QUOTES) ?></div>
                <div class="cust-row"><b>Alamat KTP:</b>    <?= htmlspecialchars($customerData['address']   ?? '-', ENT_QUOTES) ?></div>
                <div class="cust-row"><b>Domisili:</b>      <?= htmlspecialchars($customerData['resident_address'] ?? '-', ENT_QUOTES) ?></div>
              </div>
              <div class="cust-actions">
                <?php if (!empty($customerData['phone'])): ?>
                  <a class="btn-chip" href="tel:<?= htmlspecialchars($customerData['phone'], ENT_QUOTES) ?>"><i class="fas fa-phone"></i> Telp</a>
                <?php endif; ?>
                <?php if (!empty($customerData['email'])): ?>
                  <a class="btn-chip" href="mailto:<?= htmlspecialchars($customerData['email'], ENT_QUOTES) ?>"><i class="fas fa-envelope"></i> Email</a>
                <?php endif; ?>
                <a class="btn-chip" href="<?= base_url('transaction') ?>"><i class="fas fa-sync-alt"></i> Ganti Customer</a>
              </div>
            <?php else: ?>
              <div class="cust-title">
                <div class="avatar"><i class="fas fa-user"></i></div>
                <h6>Memuat data customer…</h6>
                <small>(ID: <?= (int)$customerData['id'] ?>)</small>
              </div>
              <div class="cust-grid"><div class="cust-row">Mohon tunggu sebentar.</div></div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div style="background:#fff4f4;border:1px solid #ffd7d7;color:#9a1a1a;border-radius:10px;padding:8px 10px;">
            Customer belum dipilih. <a href="<?= base_url('transaction') ?>" style="font-weight:700;">Pilih customer</a> terlebih dahulu.
          </div>
        <?php endif; ?>

        <a href="<?= base_url('transaction') ?>" class="ilv-back" aria-label="Kembali ke Transaction">
          <i class="<?= htmlspecialchars($iconBack, ENT_QUOTES) ?>"></i> Kembali
        </a>
      </div>
    </section>

    <!-- Grid items -->
    <div class="ilv-grid" aria-label="Pilihan Material">
      <?php foreach (($data ?? []) as $d): ?>
        <?php
          $href  = $hasCust ? $buildSellUrl($d->m_id) : 'javascript:void(0)';
          $class = 'ilv-item'.($hasCust ? '' : ' disabled');
          $attr  = $hasCust ? '' : 'onclick="alert(\'Pilih customer dulu ya.\');" aria-disabled="true"';
        ?>
        <a class="<?= $class ?>" href="<?= $href ?>" <?= $attr ?> aria-label="Pilih <?= htmlspecialchars($d->m_name,ENT_QUOTES) ?>">
          <div class="ilv-thumb">
            <img src="<?= base_url('assets/offline/'.$d->m_img) ?>" alt="<?= htmlspecialchars($d->m_name,ENT_QUOTES) ?>" loading="lazy" decoding="async">
          </div>
          <div class="ilv-caption"><?= htmlspecialchars($d->m_name,ENT_QUOTES) ?></div>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Footer Meta (versi/brand) -->
    <div class="ilv-meta">
      <span class="ilv-badge">Mode Transaksi — Sell</span>
      <span>© <?= date('Y') ?> • I Love Emas</span>
    </div>
  </div>
</div>

<?php if ($hasCust): ?>
<script>
(function(){
  var card = document.getElementById('custCard');
  if (!card) return;
  if (card.getAttribute('data-needs') !== '1') return;

  var cid = card.getAttribute('data-cid');
  fetch("<?= $custInfoUrl ?>?id=" + encodeURIComponent(cid), { headers: { 'Accept':'application/json' } })
  .then(function(r){
    var ct = r.headers.get('content-type') || '';
    if (ct.indexOf('application/json') !== -1) return r.json();
    return r.text().then(function(t){ throw new Error('Not JSON:\\n' + t.slice(0,250)); });
  })
  .then(function(j){
    if (!j || !j.ok || !j.data) return;
    var c = j.data;
    card.innerHTML =
      '<div class="cust-title">'
      +   '<div class="avatar"><i class="fas fa-user"></i></div>'
      +   '<h6>' + esc(c.c_name || 'Customer') + '</h6>'
      +   '<small>(ID: ' + esc(c.c_id || cid) + ')</small>'
      + '</div>'
      + '<div class="cust-grid">'
      +   row('No. Identitas:', c.c_id_number)
      +   row('No. Order:',     c.c_no_order)
      +   row('Phone:',         c.c_phone)
      +   row('Email:',         c.c_email)
      +   row('Alamat KTP:',    c.c_address)
      +   row('Domisili:',      c.c_resident_address)
      + '</div>'
      + '<div class="cust-actions">'
      +   (c.c_phone ? '<a class="btn-chip" href="tel:'+esc(c.c_phone)+'"><i class="fas fa-phone"></i> Telp</a>' : '')
      +   (c.c_email ? '<a class="btn-chip" href="mailto:'+esc(c.c_email)+'"><i class="fas fa-envelope"></i> Email</a>' : '')
      +   '<a class="btn-chip" href="<?= base_url('transaction') ?>"><i class="fas fa-sync-alt"></i> Ganti Customer</a>'
      + '</div>';
    function row(label,val){ return '<div class="cust-row"><b>'+esc(label)+'</b> '+esc(val||'-')+'</div>'; }
    function esc(s){ return String(s).replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  })
  .catch(function(e){ console.warn('customer-info error', e); });
})();
</script>
<?php endif; ?>
