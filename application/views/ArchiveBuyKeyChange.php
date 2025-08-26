<style>
  /* ====== Scoped ke form ini saja ====== */
  #myForm{
    --card-border:#e6eefc;
    --thead-bg:#f5fbff;
    --text-dark:#0e204a;
    --shadow:0 10px 24px rgba(0,0,0,.08);
  }

  /* Container atas (menggantikan margin-top inline yang kaku) */
  .col-md-12[style*="margin-top:110px;"]{
    margin-top: calc(var(--topbar-h,72px) + 20px) !important;
    padding-inline: clamp(12px, 4vw, 24px) !important;
  }

  /* ====== Tabel rapi & “card-like” ====== */
  #myForm table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    border:1px solid var(--card-border);
    border-radius:16px;
    background:#fff;
    box-shadow: var(--shadow);
    /* penting: jangan pangkas keyboard */
    overflow: visible !important;
  }

  /* Sudut membulat tetap rapi walau overflow visible */
  #myForm thead th:first-child{ border-top-left-radius:16px; }
  #myForm thead th:last-child { border-top-right-radius:16px; }
  #myForm tbody tr:last-child td:first-child{ border-bottom-left-radius:16px; }
  #myForm tbody tr:last-child td:last-child { border-bottom-right-radius:16px; }

  #myForm thead th{
    background: linear-gradient(135deg, var(--thead-bg), #e9faff);
    color: var(--text-dark);
    font-weight:800;
    text-align:center;
    letter-spacing:.2px;
  }
  #myForm th, #myForm td{
    border:1px solid #e2e8f3;
    padding:12px 14px;
    vertical-align:middle;
  }

  /* Pastikan seluruh bagian tabel tidak memangkas popup keyboard */
  #myForm thead, #myForm tbody, #myForm tr, #myForm th, #myForm td{
    overflow: visible !important;
    position: relative;
  }

  /* ====== Header kolom (pengganti .bordering lama) ====== */
  .bordering{
    width:auto;
    border:1px solid #e2e8f3 !important;
    text-align:left;
    padding-left:10px;
    color:var(--text-dark) !important;
    background:#fff !important;
  }

  /* ====== Input angka di sel tabel ====== */
  .input-box{
    width:100%;
    height: clamp(38px, 5.2vw, 44px);
    margin:0;
    padding:8px 10px;
    background:#fff;
    border:1px solid #e3e6ef;
    border-radius:10px;
    font-weight:700;
    text-align:center;
    outline:0;
    transition:border-color .15s ease, box-shadow .15s ease;
  }
  .input-box:focus{
    border-color:#b9d6ff;
    box-shadow:0 0 0 3px rgba(51,136,255,.15);
  }

  /* Hilangkan spinner number untuk tampilan bersih */
  #myForm input[type=number]::-webkit-outer-spin-button,
  #myForm input[type=number]::-webkit-inner-spin-button{ -webkit-appearance:none; margin:0; }
  #myForm input[type=number]{ -moz-appearance:textfield; }

  /* ====== Tombol bawah lebih rapi di mobile ====== */
  .btn.btn-icon-split.btn-lg{
    border-radius:12px;
    box-shadow: var(--shadow);
  }
  @media (max-width: 576px){
    .btn.btn-icon-split.btn-lg{
      width:100%;
      margin-bottom:10px;
    }
  }

  /* ====== Perbaiki padding kolom inline yang “350px” agar responsif ====== */
  @media (max-width: 1199.98px){
    .col-md-12[style*="padding:0px 350px;"]{ padding: 0 12px !important; }
  }
  @media (min-width: 1200px){
    .col-md-12[style*="padding:0px 350px;"]{ padding: 0 160px !important; }
  }
  @media (min-width: 1400px){
    .col-md-12[style*="padding:0px 350px;"]{ padding: 0 220px !important; }
  }

  /* ====== Judul halaman lebih modern ====== */
  h3.text-center{
    font-weight:800;
    font-size: clamp(20px, 3.4vw, 28px);
    margin-bottom: 8px;
    text-shadow: 0 2px 6px rgba(0,0,0,.2);
  }

  /* ====== Keyboard jQuery: selalu tampak & bisa diklik (tanpa ubah visual) ====== */
  .ui-keyboard{
    z-index: 3000 !important;    /* di atas card/dropdown/modal */
    pointer-events: auto;        /* memastikan bisa diklik */
  }

  /* ====== Overlay Loading saat Simpan ====== */
  .saving-overlay{
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, .35);
    backdrop-filter: saturate(110%) blur(1px);
    display:flex; align-items:center; justify-content:center;
    z-index: 5000;           /* di atas keyboard */
    opacity:0; visibility:hidden;
    transition: opacity .2s ease;
    pointer-events:none;
  }
  .saving-overlay.show{ opacity:1; visibility:visible; pointer-events:auto; }

  .saving-box{
    display:flex; flex-direction:column; align-items:center; gap:14px;
    background: rgba(255,255,255,.92);
    border: 1px solid #e6eefc; border-radius:16px;
    padding:18px 22px; box-shadow:0 12px 30px rgba(0,0,0,.18);
    min-width:220px;
  }
  .saving-text{ font-weight:800; color:#0e204a; letter-spacing:.3px; }

  /* Ikon FA berputar */
  .saving-box .fa-spin{ font-size:30px; color:#2563eb; }

  /* Fallback spinner jika Font Awesome tidak ada */
  .saving-fallback{
    width:34px; height:34px; border-radius:50%;
    border:3px solid #dbeafe; border-top-color:#2563eb;
    animation: spin 1s linear infinite;
    display:none;
  }
  @keyframes spin{ to{ transform: rotate(360deg); } }
  @media (prefers-reduced-motion: reduce){
    .saving-box .fa-spin{ animation: none !important; }
    .saving-fallback{ animation: none !important; }
  }
</style>

<?php 
    foreach($data as $d){};
    foreach($data2 as $dd){};  
?>
<form action="<?=base_url()?>archive/buy/save/" id="myForm">
<div class="col-md-12" style="margin-top:110px;">
        <h3 class="text-center" style="color:#fff">ARCHIVE BUY / 
        <?php 
        if($this->input->get("key")=="rti-au"){
            echo "RTI AU";
        }else if($this->input->get("key")=="rti-pt"){
            echo "RTI PT";
        }else if($this->input->get("key")=="rti-ag"){
            echo "RTI AG";
        }else if($this->input->get("key")=="rti-lm"){
            echo "RTI LM";
        }else if($this->input->get("key")=="rti-ru"){
            echo "RTI RU";
        }
        ?> / GANTI POTONGAN
        </h3>
    <br>
    <div class="row">

    
        <div class="<?= ($this->input->get("key")=="rti-au")?'col-md-6':'col-md-12' ?>" style="">
            <div class="row">
                <div class="col-md-12" <?= ($this->input->get("key")=="rti-au")?'':'style="padding:0px 350px;"' ?>>
                    
                    
                        <input type="hidden" name="key" required class="form-control" value="<?=$this->input->get("key")?>">
                        <input type="hidden" name="type" required class="form-control" value="change">
                        <div class="form-group text-center">
                            <label style="color:#fff;"><?php 
                                if($this->input->get("key")=="rti-au"){
                                    echo "RTI AU";
                                }else if($this->input->get("key")=="rti-pt"){
                                    echo "RTI PT";
                                }else if($this->input->get("key")=="rti-ag"){
                                    echo "RTI AG";
                                }else if($this->input->get("key")=="rti-lm"){
                                    echo "RTI LM";
                                }else if($this->input->get("key")=="rti-ru"){
                                    echo "RTI RU";
                                }
                            ?></label>
                            <table style="width:100%;border: 1px solid black;" cellspacing="3" cellpadding="3">
                                <thead>
                                <?php if($this->input->get("key")=="rti-pt" || $this->input->get("key")=="rti-ag" || $this->input->get("key")=="rti-ru"){ ?>
                                    <tr>
                                        <th style="text-align:center;" colspan="2" class="bordering">High Material</th>
                                        <th style="text-align:center;" colspan="2" class="bordering">Low Material</th>
                                    </tr>
                                    <tr>
                                        <th class="bordering">Name</th>
                                        <th class="bordering">Value</th>
                                        <th class="bordering">Name</th>
                                        <th class="bordering">Value</th>
                                    </tr>
                                <?php }else{ ?>
                                    <tr>
                                        <th style="text-align:center;" colspan="2" class="bordering">Material</th>
                                    </tr>
                                    <tr>
                                        <th class="bordering">Name</th>
                                        <th class="bordering">Value</th>
                                    </tr>
                                <?php } ?>
                                </thead>
                                <tbody>
                                <?php if($this->input->get("key")=="rti-au"){ ?>
                                    <tr>
                                        <td class="bordering">k24 (99.9)</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="h" required  value="<?=$d->h?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">k24 (99)</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="a" required  value="<?=$d->a?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">k2 k23</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="b" required  value="<?=$d->b?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">material au</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="c" required  value="<?=$d->c?>"></td>
                                    </tr>
                                    <tr style="display: none">
                                        <td class="bordering">lm baru</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="d" required  value="<?=$d->d?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">lm retro</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="e" required  value="<?=$d->e?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">cust. profesional</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="f" required  value="<?=$d->f?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">Pembelian UBS</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="g" required  value="<?=$d->g?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">Gold Bar 99</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="gb_99" required  value="<?=$d->gb_99?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="bordering">Gold Bar 99,9</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="gb_99_9" required  value="<?=$d->gb_99_9?>"></td>
                                    </tr>
                                <?php }else if($this->input->get("key")=="rti-pt"){ ?>
                                    <tr>
                                    <td class="bordering">Pt</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="a" required  value="<?=$d->a?>"></td>
                                    <td class="bordering">Pt</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="aa" required  value="<?=$dd->a?>"></td>
                                    </tr>
                                    <tr>
                                    <td class="bordering">Pd</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="b" required  value="<?=$d->b?>"></td>
                                    <td class="bordering">Pd</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="bb" required  value="<?=$dd->b?>"></td>
                                    </tr>
                                    <tr>
                                    <td class="bordering">Rh</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="c" required  value="<?=$d->c?>"></td>
                                    <td class="bordering">Rh</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="cc" required  value="<?=$dd->c?>"></td>
                                    </tr>
                                    <tr>
                                    <td class="bordering">Ir</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="d" required  value="<?=$d->d?>"></td>
                                    <td class="bordering">Ir</td>
                                    <td class="bordering"><input class="input-box" type="number" step="any" name="dd" required  value="<?=$dd->d?>"></td>
                                    </tr>
                                <?php }else if($this->input->get("key")=="rti-ag"){ ?>
                                    <tr>
                                        <td class="bordering">Potongan Ag</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="a" required  value="<?=$d->a?>"></td>
                                        <td class="bordering">Potongan Ag</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="aa" required  value="<?=$dd->a?>"></td>
                                    </tr>
                                <?php }else if($this->input->get("key")=="rti-lm"){ ?>
                                <?php }else if($this->input->get("key")=="rti-ru"){ ?>
                                    <tr>
                                        <td class="bordering">Potongan Ru</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="a" required  value="<?=$d->a?>"></td>
                                        <td class="bordering">Potongan Ru</td>
                                        <td class="bordering"><input class="input-box" type="number" step="any" name="aa" required  value="<?=$dd->a?>"></td>
                                    </tr>
                                <?php  }?>
                                </tbody>
                            </table>
                            <!-- <input type="number" step="any" name="value" required class="form-control" value="<?=$value?>"> -->
                        </div>
                    
                </div>
                
            </div>
        </div>
        <?php if($this->input->get("key")=="rti-au"){ ?>
            <div class="col-md-6">
                <div class="text-center">

                    <label style="color:#fff; text-align: center; ">Potongan LM Certi</label>
                </div>
                <table style="width:100%;border: 1px solid black;" cellspacing="3" cellpadding="3">
                    <thead>
                    
                        <tr>
                            <th style="text-align:center;" colspan="2" class="bordering">Material</th>
                        </tr>
                        <tr>
                            <th class="bordering">Name</th>
                            <th class="bordering">Value</th>
                        </tr>
                    
                    </thead>
                    <tbody>
                        <?php 
                        $potongan_lm = json_decode($d->potongan_lm, true);
                        
                        $tahun_mulai = 2018;
                        while ($tahun_mulai <= date('Y')+1) { ?>
                            <tr>
                                <td class="bordering">LM Certi <?= $tahun_mulai; ?></td>
                                <td class="bordering"><input class="input-box" type="number" step="any" name="potongan_lm[<?= $tahun_mulai; ?>]" required  value="<?= $potongan_lm[$tahun_mulai] ?>"></td>
                            </tr>
                            
                        <?php $tahun_mulai++; } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
    <div class="col-md-12 mt-3 text-center">
                <a href="<?=base_url()?>archive/buy/?key=<?=$this->input->get('key')?>" class="btn btn-primary btn-icon-split btn-lg mr-3">
                    <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                    </span>
                    <span class="text">Kembali</span>
                </a>
                <a href="#" onclick="document.getElementById('myForm').submit();" class="btn btn-success btn-icon-split btn-lg mr-3">
                    <span class="icon text-white-50">
                    <i class="fas fa-save mr-1"></i>
                    </span>
                    <span class="text"> Simpan</span>
                </a>
            </div>
</div>
</form>

<!-- Overlay Loading -->
<div class="saving-overlay" id="savingOverlay" aria-hidden="true" aria-label="Sedang menyimpan" role="status">
  <div class="saving-box" aria-live="polite">
    <!-- Pakai FA jika tersedia; ada fallback CSS -->
    <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>
    <div class="saving-fallback" aria-hidden="true"></div>
    <div class="saving-text">Menyimpan…</div>
  </div>
</div>

<script>
  (function(){
    function onReady(fn){ document.readyState!=='loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }
    onReady(function(){
      // NumPad dari jQuery Keyboard (kalau plugin tersedia) — TIDAK diubah
      if (window.jQuery && jQuery.fn && jQuery.fn.keyboard) {
        jQuery('.input-box').keyboard({
          layout: 'num',
          restrictInput: true,
          preventPaste: true,
          autoAccept: true
        });
      }

      // Overlay loading saat submit
      var form    = document.getElementById('myForm');
      var overlay = document.getElementById('savingOverlay');
      var faIcon  = overlay ? overlay.querySelector('.fa-circle-notch') : null;
      var cssSpin = overlay ? overlay.querySelector('.saving-fallback') : null;

      // Jika Font Awesome tidak tersedia, tampilkan spinner fallback
      try {
        var faLoaded = window.getComputedStyle(faIcon, '::before').getPropertyValue('content');
        if (!faLoaded || faLoaded === 'none' || faLoaded === 'normal' || faLoaded === '""') {
          cssSpin && (cssSpin.style.display = 'block');
        }
      } catch(e){ cssSpin && (cssSpin.style.display = 'block'); }

      if (form && overlay) {
        form.addEventListener('submit', function(e){
          // Biarkan validasi HTML5 jalan; hanya tampilkan overlay jika valid
          if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;
          overlay.classList.add('show');
        });
      }
    });
  })();
</script>
