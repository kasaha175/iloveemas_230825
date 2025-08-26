<!-- application/views/ConfigForm.php -->

<style>
  /* ===== Config Form (scoped) ===== */
  :root{ --blue:#074799; --dark:#001A6E; --pastel:#B1F0F7; --r:18px; --br:#e6eefc; }

  /* Fix overlay gelap dari template agar tidak menutupi konten */
  body{ position:relative; }
  body::before{ z-index:-1 !important; } /* hanya berlaku di halaman ini */
  .cfg-wrap, .cfg-card{ position:relative; z-index:2; }

  .cfg-wrap{ padding:clamp(16px,2.2vw,28px); margin-top:72px; }
  .cfg-card{
    background:#fff; border:1px solid var(--br); border-radius:var(--r);
    box-shadow:0 18px 30px rgba(0,0,0,.08);
    padding:clamp(16px,2.2vw,24px); max-width:1000px; margin:auto;
  }

  .cfg-title{ font-weight:700; font-size:20px; margin:0 0 6px; color:#0e204a; }
  .cfg-hint{ font-size:12px; color:#6b7a99; margin:0 0 14px; }

  .cfg-grid{ display:grid; grid-template-columns:1fr; gap:14px; }
  @media (min-width:700px){ .cfg-grid{ grid-template-columns:repeat(2,1fr); } }
  .cfg-row{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }

  .cfg-field{ display:flex; flex-direction:column; gap:6px; }
  .cfg-field label{ font-weight:600; font-size:13px; color:#273451; }

  .cfg-input{
    background:#fff; color:#0B0F1A;
    border:1px solid var(--br) !important; border-radius:12px;
    padding:12px; font-size:14px;
  }
  .cfg-input:focus{
    outline:none; border-color:#bcd3ff !important;
    box-shadow:0 0 0 4px rgba(7,71,153,.12);
  }
  input[type="color"].cfg-input{ padding:4px; height:42px; }

  .preview{ display:flex; align-items:center; gap:10px; font-size:12px; color:#6b7a99; }
  .preview img{ height:40px; border-radius:8px; border:1px solid #eef3ff; }

  .cfg-actions{ margin-top:16px; display:flex; gap:10px; justify-content:flex-end; }

  /* ==== Button: elevated + icon + loading ==== */
  .cfg-actions .cfg-btn{
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border: 0;
    border-radius: 12px;
    font-weight: 700;
    letter-spacing: .2px;
    cursor: pointer;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    transition: transform .08s ease, box-shadow .2s ease, filter .2s ease, opacity .2s ease;
  }
  .cfg-actions .cfg-btn.cfg-btn-primary{
    background: linear-gradient(135deg, var(--blue), var(--dark)) !important;
    color: #fff !important;
    box-shadow:
      0 10px 20px rgba(0,26,110,.25),
      inset 0 1px 0 rgba(255,255,255,.25);
  }
  .cfg-actions .cfg-btn.cfg-btn-primary:hover{
    transform: translateY(-1px);
    box-shadow:
      0 14px 28px rgba(0,26,110,.28),
      inset 0 1px 0 rgba(255,255,255,.28);
    filter: brightness(1.02);
  }
  .cfg-actions .cfg-btn.cfg-btn-primary:active{
    transform: translateY(0);
    box-shadow:
      0 8px 16px rgba(0,26,110,.22),
      inset 0 1px 0 rgba(255,255,255,.22);
  }
  .cfg-actions .cfg-btn.cfg-btn-primary:focus-visible{
    outline: none;
    box-shadow:
      0 0 0 4px rgba(7,71,153,.28),
      0 12px 24px rgba(0,26,110,.26),
      inset 0 1px 0 rgba(255,255,255,.25);
  }
  .cfg-btn__ico{ width:18px; height:18px; flex:0 0 18px; }
  .cfg-btn__label{ white-space: nowrap; }
  .cfg-btn__spinner{ width:18px; height:18px; flex:0 0 18px; display:none; }
  @keyframes cfgspin{ to{ transform: rotate(360deg); } }
  .cfg-btn.is-loading{ cursor: progress; opacity: .95; }
  .cfg-btn.is-loading .cfg-btn__spinner{ display:inline-block; animation: cfgspin .9s linear infinite; }
  .cfg-btn.is-loading .cfg-btn__ico{ display:none; }

  /* Alerts (flashdata) */
  .badge-info{
    display:inline-block; padding:6px 10px; border-radius:999px;
    border:1px solid #dfeaff; background:#fff; color:#0e2b68; font-size:12px; margin-bottom:8px;
  }
</style>

<div class="cfg-wrap">
  <div class="cfg-card">
    <h1 class="cfg-title">Konfigurasi Aplikasi</h1>
    <p class="cfg-hint">Ubah aset & identitas aplikasi. Unggahan bersifat opsional—biarkan kosong jika tidak ingin diganti.</p>

    <?php if($this->session->flashdata('success')): ?>
      <div class="badge-info"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <form action="<?= base_url('config') ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

      <!-- Nama & Versi -->
      <div class="cfg-grid">
        <div class="cfg-field">
          <label for="app_name">Nama Aplikasi</label>
          <input id="app_name" class="cfg-input" type="text" name="app_name"
                 value="<?= $config['app_name'] ?? '' ?>" placeholder="Nama aplikasi">
        </div>
        <div class="cfg-field">
          <label for="app_version">Versi Aplikasi</label>
          <input id="app_version" class="cfg-input" type="text" name="app_version"
                 value="<?= $config['app_version'] ?? '3.0.0' ?>" placeholder="Mis: 3.0.0">
        </div>
      </div>

      <!-- Warna -->
      <div class="cfg-row" style="margin-top:8px;">
        <div class="cfg-field">
          <label for="color_primary">Warna Utama</label>
          <input id="color_primary" class="cfg-input" type="color" name="color_primary"
                 value="<?= $config['color_primary'] ?? '#074799' ?>">
        </div>
        <div class="cfg-field">
          <label for="color_secondary">Warna Sekunder</label>
          <input id="color_secondary" class="cfg-input" type="color" name="color_secondary"
                 value="<?= $config['color_secondary'] ?? '#001A6E' ?>">
        </div>
        <div class="cfg-field">
          <label for="color_pastel">Warna Pastel</label>
          <input id="color_pastel" class="cfg-input" type="color" name="color_pastel"
                 value="<?= $config['color_pastel'] ?? '#B1F0F7' ?>">
        </div>
      </div>

      <!-- Logo & Favicon -->
      <div class="cfg-grid" style="margin-top:8px;">
        <div class="cfg-field">
          <label for="logo">Logo Aplikasi (png/jpg/webp/svg)</label>
          <input id="logo" class="cfg-input" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp,.svg">
          <div class="preview">
            <span>Aktif:</span>
            <?php if (!empty($config['logo'])): ?>
              <img src="<?= base_url($config['logo']) ?>" alt="logo">
            <?php else: ?>
              <em>Belum diatur</em>
            <?php endif; ?>
          </div>
        </div>
        <div class="cfg-field">
          <label for="favicon">Favicon (png/ico)</label>
          <input id="favicon" class="cfg-input" type="file" name="favicon" accept=".png,.ico">
          <div class="preview">
            <span>Aktif:</span>
            <?php if (!empty($config['favicon'])): ?>
              <img src="<?= base_url($config['favicon']) ?>" alt="favicon">
            <?php else: ?>
              <em>Belum diatur</em>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Backgrounds -->
      <div class="cfg-grid" style="margin-top:8px;">
        <div class="cfg-field">
          <label for="bg_login">Background Login (png/jpg/webp)</label>
          <input id="bg_login" class="cfg-input" type="file" name="bg_login" accept=".png,.jpg,.jpeg,.webp">
          <div class="preview">
            <span>Aktif:</span>
            <?php if (!empty($config['bg_login'])): ?>
              <img src="<?= base_url($config['bg_login']) ?>" alt="bg login">
            <?php else: ?>
              <em>Default</em>
            <?php endif; ?>
          </div>
        </div>
        <div class="cfg-field">
          <label for="bg_dashboard">Background Dashboard (png/jpg/webp)</label>
          <input id="bg_dashboard" class="cfg-input" type="file" name="bg_dashboard" accept=".png,.jpg,.jpeg,.webp">
          <div class="preview">
            <span>Aktif:</span>
            <?php if (!empty($config['bg_dashboard'])): ?>
              <img src="<?= base_url($config['bg_dashboard']) ?>" alt="bg dashboard">
            <?php else: ?>
              <em>Default</em>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="cfg-actions">
        <!-- back to dashboard -->
        <a href="<?= base_url('dashboard'); ?>" class="cfg-btn cfg-btn-soft" id="cfgBackBtn">
          <!-- ikon back -->
          <svg class="cfg-btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M15 18l-6-6 6-6"/>
            <path d="M3 12h18"/>
          </svg>
          <span class="cfg-btn__label">Kembali ke Dashboard</span>
        </a>

        <!-- save -->
        <button class="cfg-btn cfg-btn-primary" id="cfgSaveBtn" type="submit">
          <!-- ikon save -->
          <svg class="cfg-btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/>
          </svg>
          <span class="cfg-btn__label">Simpan Perubahan</span>
          <!-- spinner -->
          <svg class="cfg-btn__spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9.5" stroke="rgba(255,255,255,.35)" stroke-width="3"/>
            <path d="M12 2.5a9.5 9.5 0 0 1 9.5 9.5" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
          </svg>
        </button>
      </div>

    </form>
  </div>
</div>

<script>
/* Aktifkan state loading pada tombol saat form disubmit */
(function(){
  var form = document.querySelector('form[action*="config"]');
  var btn  = document.getElementById('cfgSaveBtn');
  if(form && btn){
    form.addEventListener('submit', function(){
      btn.classList.add('is-loading');
      btn.setAttribute('aria-busy','true');
      btn.disabled = true; // cegah double submit
    });
  }
})();
</script>
