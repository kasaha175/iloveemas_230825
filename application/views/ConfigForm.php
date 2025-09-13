<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  :root{ --blue:#074799; --dark:#001A6E; --pastel:#B1F0F7; --r:18px; --br:#e6eefc; }
  body{ position:relative; } body::before{ z-index:-1 !important; }
  .cfg-wrap, .cfg-card{ position:relative; z-index:2; }
  .cfg-wrap{ padding:clamp(16px,2.2vw,28px); margin-top:72px; }
  .cfg-card{ background:#fff; border:1px solid var(--br); border-radius:var(--r);
    box-shadow:0 18px 30px rgba(0,0,0,.08); padding:clamp(16px,2.2vw,24px); max-width:1100px; margin:auto; }
  .cfg-title{ font-weight:800; font-size:22px; margin:0 0 6px; color:#0e204a; }
  .cfg-hint{ font-size:12px; color:#6b7a99; margin:0 0 16px; }

  .cfg-tabs{ display:flex; gap:6px; margin-bottom:14px; border-bottom:2px solid #eef3ff; }
  .cfg-tab-btn{ appearance:none; background:#f7fbff; border:1px solid #dfeaff; border-bottom:0; padding:10px 16px;
    border-radius:12px 12px 0 0; font-weight:700; color:#0e204a; cursor:pointer; }
  .cfg-tab-btn.active{ background:#fff; border-color:#bcd3ff; color:var(--blue); box-shadow:0 -2px 6px rgba(0,0,0,.06); }
  .cfg-tab-content{ display:none; } .cfg-tab-content.active{ display:block; }

  .cfg-grid{ display:grid; grid-template-columns:1fr; gap:14px; }
  @media (min-width:700px){ .cfg-grid{ grid-template-columns:repeat(2,1fr); } }
  .cfg-row{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
  .cfg-field{ display:flex; flex-direction:column; gap:6px; }
  .cfg-field label{ font-weight:700; font-size:13px; color:#273451; }
  .cfg-input{ background:#fff; color:#0B0F1A; border:1px solid var(--br)!important; border-radius:12px; padding:12px; font-size:14px; }
  .cfg-input:focus{ outline:none; border-color:#bcd3ff!important; box-shadow:0 0 0 4px rgba(7,71,153,.12); }
  input[type="color"].cfg-input{ padding:4px; height:42px; }
  .preview{ display:flex; align-items:center; gap:10px; font-size:12px; color:#6b7a99; }
  .preview img{ height:40px; border-radius:8px; border:1px solid #eef3ff; }

  .cfg-actions{ margin-top:16px; display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
  .cfg-btn{ position:relative; display:inline-flex; align-items:center; gap:10px; padding:12px 18px; border:0; border-radius:12px;
    font-weight:800; letter-spacing:.2px; cursor:pointer; transition:transform .08s ease, box-shadow .2s ease, filter .2s ease, opacity .2s ease; }
  .cfg-btn-primary{ background:linear-gradient(135deg,var(--blue),var(--dark))!important; color:#fff!important;
    box-shadow:0 10px 20px rgba(0,26,110,.25), inset 0 1px 0 rgba(255,255,255,.25); }
  .cfg-btn-primary:hover{ transform:translateY(-1px); filter:brightness(1.02); box-shadow:0 14px 28px rgba(0,26,110,.28), inset 0 1px 0 rgba(255,255,255,.28); }
  .cfg-btn-soft{ background:#fff; color:#0e204a; border:1px solid #dfeaff; box-shadow:0 10px 18px rgba(0,0,0,.06); }
  .cfg-btn__ico{ width:18px; height:18px; flex:0 0 18px; } .cfg-btn__spinner{ width:18px; height:18px; flex:0 0 18px; display:none; }
  @keyframes cfgspin{ to{ transform:rotate(360deg);} }
  .cfg-btn.is-loading{ cursor:progress; opacity:.95; }
  .cfg-btn.is-loading .cfg-btn__spinner{ display:inline-block; animation:cfgspin .9s linear infinite; }
  .cfg-btn.is-loading .cfg-btn__ico{ display:none; }

  .badge-info{ display:inline-block; padding:6px 10px; border-radius:999px; border:1px solid #dfeaff; background:#fff; color:#0e2b68; font-size:12px; margin-bottom:8px; }
  .badge-ok{ border-color:#c9f6d1; background:#ecffef; color:#155724; }
  .badge-err{ border-color:#fecaca; background:#fff1f2; color:#991b1b; }

  /* area hasil tes sederhana */
  #smtpTestResult{ margin-top:10px; font-size:13px; }
</style>

<div class="cfg-wrap">
  <div class="cfg-card">
    <h1 class="cfg-title">Konfigurasi Aplikasi</h1>
    <p class="cfg-hint">Ubah aset & identitas aplikasi atau atur SMTP untuk pengiriman email.</p>

    <?php if ($this->session->flashdata('status') && $this->session->flashdata('message')): ?>
      <div class="badge-info <?= $this->session->flashdata('status')==='success' ? 'badge-ok' : 'badge-err' ?>">
        <?= html_escape($this->session->flashdata('message')) ?>
      </div>
    <?php endif; ?>

    <div class="cfg-tabs">
      <button type="button" class="cfg-tab-btn" data-tab="app">Aplikasi</button>
      <button type="button" class="cfg-tab-btn" data-tab="smtp">SMTP</button>
    </div>

    <!-- ===== Tab: APLIKASI ===== -->
    <div id="tab-app" class="cfg-tab-content">
      <form id="formApp" action="<?= base_url('config') ?>" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="cfg-grid">
          <div class="cfg-field">
            <label for="app_name">Nama Aplikasi</label>
            <input id="app_name" class="cfg-input" type="text" name="app_name"
                   value="<?= isset($config['app_name']) ? html_escape($config['app_name']) : '' ?>" placeholder="Nama aplikasi" maxlength="100">
          </div>
          <div class="cfg-field">
            <label for="app_version">Versi Aplikasi</label>
            <input id="app_version" class="cfg-input" type="text" name="app_version"
                   value="<?= isset($config['app_version']) ? html_escape($config['app_version']) : '3.0.0' ?>" placeholder="Mis: 3.0.0" maxlength="32">
          </div>
        </div>

        <div class="cfg-row" style="margin-top:8px;">
          <div class="cfg-field">
            <label for="color_primary">Warna Utama</label>
            <input id="color_primary" class="cfg-input" type="color" name="color_primary"
                   value="<?= isset($config['color_primary']) ? html_escape($config['color_primary']) : '#074799' ?>">
          </div>
          <div class="cfg-field">
            <label for="color_secondary">Warna Sekunder</label>
            <input id="color_secondary" class="cfg-input" type="color" name="color_secondary"
                   value="<?= isset($config['color_secondary']) ? html_escape($config['color_secondary']) : '#001A6E' ?>">
          </div>
          <div class="cfg-field">
            <label for="color_pastel">Warna Pastel</label>
            <input id="color_pastel" class="cfg-input" type="color" name="color_pastel"
                   value="<?= isset($config['color_pastel']) ? html_escape($config['color_pastel']) : '#B1F0F7' ?>">
          </div>
        </div>

        <div class="cfg-grid" style="margin-top:8px;">
          <div class="cfg-field">
            <label for="logo">Logo Aplikasi (png/jpg/webp)</label>
            <input id="logo" class="cfg-input" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp">
            <div class="preview"><span>Aktif:</span>
              <?php if (!empty($config['logo'])): ?>
                <img src="<?= base_url($config['logo']) ?>" alt="logo" loading="lazy">
              <?php else: ?><em>Belum diatur</em><?php endif; ?>
            </div>
          </div>
          <div class="cfg-field">
            <label for="favicon">Favicon (png/ico)</label>
            <input id="favicon" class="cfg-input" type="file" name="favicon" accept=".png,.ico">
            <div class="preview"><span>Aktif:</span>
              <?php if (!empty($config['favicon'])): ?>
                <img src="<?= base_url($config['favicon']) ?>" alt="favicon" loading="lazy">
              <?php else: ?><em>Belum diatur</em><?php endif; ?>
            </div>
          </div>
        </div>

        <div class="cfg-grid" style="margin-top:8px;">
          <div class="cfg-field">
            <label for="bg_login">Background Login (png/jpg/webp)</label>
            <input id="bg_login" class="cfg-input" type="file" name="bg_login" accept=".png,.jpg,.jpeg,.webp">
            <div class="preview"><span>Aktif:</span>
              <?php if (!empty($config['bg_login'])): ?>
                <img src="<?= base_url($config['bg_login']) ?>" alt="bg login" loading="lazy">
              <?php else: ?><em>Default</em><?php endif; ?>
            </div>
          </div>
          <div class="cfg-field">
            <label for="bg_dashboard">Background Dashboard (png/jpg/webp)</label>
            <input id="bg_dashboard" class="cfg-input" type="file" name="bg_dashboard" accept=".png,.jpg,.jpeg,.webp">
            <div class="preview"><span>Aktif:</span>
              <?php if (!empty($config['bg_dashboard'])): ?>
                <img src="<?= base_url($config['bg_dashboard']) ?>" alt="bg dashboard" loading="lazy">
              <?php else: ?><em>Default</em><?php endif; ?>
            </div>
          </div>
        </div>

        <div class="cfg-actions">
          <a href="<?= base_url('dashboard'); ?>" class="cfg-btn cfg-btn-soft" id="cfgBackBtn">
            <svg class="cfg-btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/><path d="M3 12h18"/></svg>
            <span>Kembali ke Dashboard</span>
          </a>

          <button class="cfg-btn cfg-btn-primary" id="cfgSaveBtn" type="submit">
            <svg class="cfg-btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
            <span>Simpan Perubahan</span>
            <svg class="cfg-btn__spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9.5" stroke="rgba(255,255,255,.35)" stroke-width="3"/><path d="M12 2.5a9.5 9.5 0 0 1 9.5 9.5" stroke="#fff" stroke-width="3" stroke-linecap="round"/></svg>
          </button>
        </div>
      </form>
    </div>

    <!-- ===== Tab: SMTP ===== -->
    <div id="tab-smtp" class="cfg-tab-content">
      <form id="formSmtp" action="<?= site_url('config/save-smtp') ?>" method="post" autocomplete="off">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

        <div class="cfg-grid">
          <div class="cfg-field">
            <label>Protocol</label>
            <select name="protocol" class="cfg-input" disabled><option value="smtp" selected>SMTP</option></select>
          </div>
          <div class="cfg-field">
            <label>Security</label>
            <select name="crypto" class="cfg-input" id="cryptoSelect">
              <option value="tls"  <?= (isset($smtp['crypto']) && $smtp['crypto']==='tls')?'selected':''; ?>>TLS (587)</option>
              <option value="ssl"  <?= (isset($smtp['crypto']) && $smtp['crypto']==='ssl')?'selected':''; ?>>SSL (465)</option>
              <option value="none" <?= (empty($smtp['crypto']) || $smtp['crypto']==='none')?'selected':''; ?>>None</option>
            </select>
          </div>

          <div class="cfg-field">
            <label>SMTP Host</label>
            <input type="text" class="cfg-input" name="host" value="<?= isset($smtp['host']) ? html_escape($smtp['host']) : '' ?>" required>
          </div>
          <div class="cfg-field">
            <label>SMTP Port</label>
            <input type="number" class="cfg-input" name="port" id="portInput" value="<?= isset($smtp['port']) ? (int)$smtp['port'] : 587 ?>" min="1" max="65535">
          </div>

          <div class="cfg-field">
            <label>Username</label>
            <input type="text" class="cfg-input" name="user" value="<?= isset($smtp['user']) ? html_escape($smtp['user']) : '' ?>" required>
          </div>
          <div class="cfg-field">
            <label>Password (isi untuk mengganti)</label>
            <input type="password" class="cfg-input" name="password" placeholder="••••••••">
          </div>

          <div class="cfg-field">
            <label>From Email</label>
            <input type="email" class="cfg-input" name="from_email" value="<?= isset($smtp['from_email']) ? html_escape($smtp['from_email']) : '' ?>" required>
          </div>
          <div class="cfg-field">
            <label>From Name</label>
            <input type="text" class="cfg-input" name="from_name" value="<?= isset($smtp['from_name']) ? html_escape($smtp['from_name']) : '' ?>" required>
          </div>

          <div class="cfg-field">
            <label>Timeout (detik)</label>
            <input type="number" class="cfg-input" name="timeout_sec" value="<?= isset($smtp['timeout_sec']) ? (int)$smtp['timeout_sec'] : 10 ?>" min="2" max="120">
          </div>
          <div class="cfg-field">
            <label>Status</label>
            <select name="is_active" class="cfg-input">
              <option value="1" <?= (isset($smtp['is_active']) && (int)$smtp['is_active']===1)?'selected':''; ?>>Aktif</option>
              <option value="0" <?= (isset($smtp['is_active']) && (int)$smtp['is_active']===0)?'selected':''; ?>>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="cfg-actions">
          <button class="cfg-btn cfg-btn-primary" id="smtpSaveBtn" type="submit">
            <svg class="cfg-btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
            <span>Simpan SMTP</span>
            <svg class="cfg-btn__spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9.5" stroke="rgba(255,255,255,.35)" stroke-width="3"/><path d="M12 2.5a9.5 9.5 0 0 1 9.5 9.5" stroke="#fff" stroke-width="3" stroke-linecap="round"/></svg>
          </button>
        </div>
      </form>

      <!-- Form Kirim Tes (tanpa modal, debug-friendly) -->
      <form id="smtpTestForm" action="<?= site_url('smtp/test-send') ?>" method="post" style="display:flex;gap:8px;align-items:center;margin-top:10px">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="email" name="to" class="cfg-input" placeholder="email tujuan tes" required style="min-width:260px">
        <button class="cfg-btn cfg-btn-soft" type="submit" id="smtpTestBtn">
          <svg class="cfg-btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
          <span>Kirim Tes</span>
          <svg class="cfg-btn__spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9.5" stroke="rgba(0,0,0,.25)" stroke-width="3"/><path d="M12 2.5a9.5 9.5 0 0 1 9.5 9.5" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
        </button>
      </form>
      <div id="smtpTestResult"></div>
    </div>
  </div>
</div>

<script>
/* Tabs + persist ?tab= */
(function(){
  const buttons = document.querySelectorAll('.cfg-tab-btn');
  const contents = document.querySelectorAll('.cfg-tab-content');
  function activateTab(tab){ buttons.forEach(b=>b.classList.toggle('active', b.dataset.tab===tab));
    contents.forEach(c=>c.classList.toggle('active', c.id==='tab-'+tab)); }
  const url = new URL(window.location.href);
  const tabQS = url.searchParams.get('tab') || 'app'; activateTab(tabQS);
  buttons.forEach(btn=>btn.addEventListener('click', ()=>{ activateTab(btn.dataset.tab);
    url.searchParams.set('tab', btn.dataset.tab); history.replaceState(null,'',url.toString()); }));
})();

/* Loading state submit (app + smtp save) */
(function(){
  const appForm=document.getElementById('formApp'), appBtn=document.getElementById('cfgSaveBtn');
  if(appForm&&appBtn){ appForm.addEventListener('submit', ()=>{ appBtn.classList.add('is-loading'); appBtn.setAttribute('aria-busy','true'); appBtn.disabled=true; }); }
  const smtpForm=document.getElementById('formSmtp'), smtpBtn=document.getElementById('smtpSaveBtn');
  if(smtpForm&&smtpBtn){ smtpForm.addEventListener('submit', ()=>{ smtpBtn.classList.add('is-loading'); smtpBtn.setAttribute('aria-busy','true'); smtpBtn.disabled=true; }); }
})();

/* Auto-port saat ganti Security */
(function(){
  const sel=document.getElementById('cryptoSelect'); const port=document.getElementById('portInput');
  if(sel && port){
    sel.addEventListener('change', function(){
      if(this.value==='tls') port.value=587;
      else if(this.value==='ssl') port.value=465;
    });
  }
})();

/* Kirim Tes: cek koneksi -> kirim email (tanpa modal), dengan debug ke console */
(function(){
  const form  = document.getElementById('smtpTestForm');
  const btn   = document.getElementById('smtpTestBtn');
  const out   = document.getElementById('smtpTestResult');
  if(!form) return;

  function getCsrf(){
    const input = form.querySelector('input[name="<?= $this->security->get_csrf_token_name(); ?>"]');
    return { name: input.getAttribute('name'), value: input.value, el: input };
  }
  async function post(url, payload, timeoutMs=8000){
    const csrf = getCsrf();
    const body = new URLSearchParams();
    if(payload) Object.keys(payload).forEach(k=>body.append(k, payload[k]));
    body.append(csrf.name, csrf.value);

    const ctrl = new AbortController();
    const t = setTimeout(()=>ctrl.abort(), timeoutMs);
    try{
      const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body, credentials:'same-origin', cache:'no-store', signal: ctrl.signal });
      clearTimeout(t);
      const text = await res.text();
      console.log('DEBUG raw response from', url, ':', text);
      let json = {};
      try{ json = JSON.parse(text); }catch(e){}
      if(json && json.csrf_name && json.csrf_hash){ getCsrf().el.name=json.csrf_name; getCsrf().el.value=json.csrf_hash; }
      return { ok: res.ok, status: res.status, json, text };
    }catch(e){
      clearTimeout(t);
      console.error('DEBUG fetch error', url, e);
      return { ok:false, status:0, json:{ error: (e.name==='AbortError'?'Timeout request':'Fetch error: '+e.message) } };
    }
  }

  form.addEventListener('submit', async function(ev){
    ev.preventDefault();
    const to = (new FormData(form)).get('to');
    if(!to){ alert('Masukkan email tujuan.'); return; }

    out.style.color = '#334155';
    out.textContent = '⏳ Mengecek koneksi ke SMTP…';
    btn.classList.add('is-loading'); btn.disabled = true;

    // 1) cek koneksi
    const net = await post('<?= site_url('config/smtp/check-net') ?>', {}, 6000);
    if(!net.ok){
      out.style.color = '#b91c1c';
      out.textContent = '❌ Tidak dapat memeriksa koneksi: ' + (net.json && net.json.error ? net.json.error : ('HTTP '+net.status));
      btn.classList.remove('is-loading'); btn.disabled = false;
      return;
    }
    if(!(net.json && (net.json.status==='good' || net.json.status==='ok'))){
      const msg = net.json && (net.json.error || net.json.status) ? (net.json.status.toUpperCase()+ (net.json.rtt_ms?` ~${net.json.rtt_ms}ms`:'')) : 'koneksi buruk';
      out.style.color = '#b45309';
      out.textContent = '⚠️ Kualitas internet kurang baik ('+ msg +'). Email tidak dikirim.';
      btn.classList.remove('is-loading'); btn.disabled = false;
      return;
    }

    // 2) kirim email
    out.textContent = '⏳ Koneksi OK. Mengirim email tes…';
    const send = await post(form.action, { to }, 10000);
    btn.classList.remove('is-loading'); btn.disabled = false;

    if(send.ok && send.json && send.json.ok){
      out.style.color = 'green';
      out.textContent = '✅ Email tes berhasil dikirim.';
    }else{
      out.style.color = 'red';
      out.textContent = '❌ Gagal kirim: ' + (send.json && (send.json.error || send.json.message) ? (send.json.error || send.json.message) : ('HTTP '+send.status));
    }
  });
})();
</script>
