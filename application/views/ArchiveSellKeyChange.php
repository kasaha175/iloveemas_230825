<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
  $key = $this->input->get('key');
  $labels = [
    'lm'           => 'LM',
    'material-au'  => 'MATERIAL AU',
    'material-ag'  => 'MATERIAL AG',
    'material-ubs' => 'UBS',
  ];
  $label = $labels[$key] ?? ucwords(str_replace('-',' ',$key));
  foreach($data as $d){}; // mengikuti pola lama
?>
<style>
  /* ===== Scoped agar tidak bentrok template lain ===== */
  .archive-sell-change-scope .wrap{ margin-top: calc(var(--topbar-h, 98px) + 12px); padding: clamp(12px, 2vw, 20px); }
  .archive-sell-change-scope .container-max{ max-width: 1160px; margin-inline:auto; }

  /* Breadcrumb kapsul (match halaman lain) */
  .archive-sell-change-scope .crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .archive-sell-change-scope .crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .archive-sell-change-scope .crumbs a:hover{ color:var(--blue-light, #4a7dff); text-decoration:underline; }
  .archive-sell-change-scope .crumbs .sep{ color:#7a8eb8; }

  /* Heading */
  .archive-sell-change-scope .heading h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .archive-sell-change-scope .heading p { margin:0; color:#dbe8ff; font-size:13px; }

  /* Card */
  .archive-sell-change-scope .card{
    border-radius:16px; border:1px solid #e6eefc; background:#fff;
    box-shadow:0 10px 24px rgba(0,0,0,.08); overflow:hidden;
  }
  .archive-sell-change-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel, #e7f1ff), #e9faff);
    border-bottom:1px solid #dfeaff;
  }
  .archive-sell-change-scope .card-header h6{ margin:0; color:#0e204a; font-weight:800; }

  /* Tabel rapi & responsif */
  .archive-sell-change-scope .table-wrap{ overflow-x:auto; overflow-y:visible; }
  .archive-sell-change-scope table{
    width:100%; border-collapse:separate; border-spacing:0;
    border:1px solid #e6eefc; border-radius:12px; background:#fff;
    /* penting: biar keyboard tidak kepotong */
    overflow:visible !important;
  }
  .archive-sell-change-scope thead th{
    background:#f5fbff; color:#0e204a; font-weight:800; text-align:center;
  }
  .archive-sell-change-scope th, .archive-sell-change-scope td{
    border:1px solid #e2e8f3; padding:10px 12px; vertical-align:middle;
  }
  /* Kolom nama di layout lama pakai .bordering width:50%; tetap dipertahankan feel-nya */
  .archive-sell-change-scope td:first-child{ width:50%; }

  /* Preserve class lama .bordering agar tidak bentrok */
  .bordering{
    width:auto; border:1px solid #e2e8f3; text-align:left;
    padding-left:10px; color:#0e204a; background:#fff;
  }

  /* Input angka (tetap .input-box) */
  .archive-sell-change-scope .input-box{
    width:100%; height:42px; margin:0;
    padding:8px 10px; background:#fff;
    border:1px solid #e3e6ef; border-radius:8px;
    font-weight:700; text-align:center; outline:0; box-shadow:none;
    transition:border-color .15s ease, box-shadow .15s ease;
  }
  .archive-sell-change-scope .input-box:focus{
    border-color:#b9d6ff; box-shadow:0 0 0 3px rgba(51,136,255,.15);
  }

  /* Hilangkan spinner number (visual) */
  .archive-sell-change-scope input[type=number]::-webkit-outer-spin-button,
  .archive-sell-change-scope input[type=number]::-webkit-inner-spin-button{ -webkit-appearance:none; margin:0; }
  .archive-sell-change-scope input[type=number]{ -moz-appearance:textfield; }

  /* Aksi bawah */
  .archive-sell-change-scope .actions{ display:flex; gap:12px; flex-wrap:wrap; justify-content:center; }

  /* Pastikan keyboard jQuery tampil di atas & tidak berubah bentuk */
  .ui-keyboard{ z-index:1300 !important; }
</style>

<div class="archive-sell-change-scope">
  <div class="wrap">
    <div class="container-max">

      <!-- Breadcrumb -->
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive') ?>">Archive</a>
        <span class="sep">/</span>
        <a href="<?= base_url('archive/sell') ?>">Sell</a>
        <span class="sep">/</span>
        <span><?= html_escape($label) ?> — Ganti Potongan</span>
      </nav>

      <!-- Heading -->
      <header class="heading">
        <h3>Archive Sell / <?= html_escape($label) ?> / Ganti Potongan</h3>
        <p>Perbarui nilai potongan untuk kategori yang dipilih.</p>
      </header>

      <form action="<?= base_url('archive/sell/save/') ?>" id="myForm">
        <input type="hidden" name="key"  value="<?= html_escape($key) ?>">
        <input type="hidden" name="type" value="change">

        <div class="row">
          <!-- Kiri / Full -->
          <div class="<?= ($key==='lm') ? 'col-md-6' : 'col-md-12' ?> mb-3">
            <section class="card h-100">
              <div class="card-header py-3">
                <h6 class="m-0"><?= html_escape($label) ?></h6>
              </div>
              <div class="card-body">
                <div class="table-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th class="bordering">Name</th>
                        <th class="bordering">Value</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($key==='lm'): ?>
                        <tr>
                          <td class="bordering">LM Retro</td>
                          <td class="bordering">
                            <input class="input-box" type="number" step="any" name="a" required value="<?= $d->a ?>">
                          </td>
                        </tr>
                        <tr style="display:none">
                          <td class="bordering">LM Baru</td>
                          <td class="bordering">
                            <input class="input-box" type="number" step="any" name="b" required value="<?= $d->b ?>">
                          </td>
                        </tr>
                      <?php elseif ($key==='material-au'): ?>
                        <tr>
                          <td class="bordering">Potongan Material AU</td>
                          <td class="bordering">
                            <input class="input-box" type="number" step="any" name="a" required value="<?= $d->a ?>">
                          </td>
                        </tr>
                      <?php elseif ($key==='material-ag'): ?>
                        <tr>
                          <td class="bordering">Potongan Material AG</td>
                          <td class="bordering">
                            <input class="input-box" type="number" step="any" name="a" required value="<?= $d->a ?>">
                          </td>
                        </tr>
                      <?php elseif ($key==='material-ubs'): ?>
                        <tr>
                          <td class="bordering">Penjualan UBS</td>
                          <td class="bordering">
                            <input class="input-box" type="number" step="any" name="a" required value="<?= $d->a ?>">
                          </td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
          </div>

          <!-- Kanan: Potongan LM Certi (khusus LM) -->
          <?php if ($key==='lm'): ?>
          <div class="col-md-6 mb-3">
            <section class="card h-100">
              <div class="card-header py-3">
                <h6 class="m-0">Potongan LM Certi</h6>
              </div>
              <div class="card-body">
                <div class="table-wrap">
                  <table>
                    <thead>
                      <tr><th style="text-align:center" colspan="2" class="bordering">Material</th></tr>
                      <tr><th class="bordering">Name</th><th class="bordering">Value</th></tr>
                    </thead>
                    <tbody>
                      <?php
                        $potongan_lm = json_decode($d->potongan_lm, true);
                        $tahun = 2018; $akhir = (int)date('Y') + 1;
                        while ($tahun <= $akhir):
                      ?>
                        <tr>
                          <td class="bordering">LM Certi <?= $tahun; ?></td>
                          <td class="bordering">
                            <input class="input-box" type="number" step="any" name="potongan_lm[<?= $tahun; ?>]" required value="<?= isset($potongan_lm[$tahun]) ? $potongan_lm[$tahun] : '' ?>">
                          </td>
                        </tr>
                      <?php $tahun++; endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
          </div>
          <?php endif; ?>
        </div>

        <div class="actions mt-2">
          <a href="<?= base_url('archive/sell/?key=' . urlencode($key)) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
          </a>
          <!-- tetap pakai anchor + submit agar proses tidak berubah -->
          <a href="#" onclick="document.getElementById('myForm').submit();" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Simpan
          </a>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
  jQuery(function ($) {
    // Biarkan konfigurasi keyboard sesuai semula (tanpa perubahan proses)
    if ($.fn && $.fn.keyboard) {
      $('.input-box').keyboard({
        layout: 'num',
        restrictInput : true,
        preventPaste  : true,
        autoAccept    : true
      });
    }
    if (window.prettyPrint) prettyPrint();
  });
</script>
