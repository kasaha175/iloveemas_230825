<?php
foreach ($data as $a) {};
function nominal($angka){ return number_format($angka, 0, ',', '.'); }
function penyebut($nilai){
  $nilai = abs($nilai);
  $huruf = array("", "satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas");
  if ($nilai < 12) return " ".$huruf[$nilai];
  else if ($nilai < 20) return penyebut($nilai - 10) . " belas";
  else if ($nilai < 100) return penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
  else if ($nilai < 200) return " seratus" . penyebut($nilai - 100);
  else if ($nilai < 1000) return penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
  else if ($nilai < 2000) return " seribu" . penyebut($nilai - 1000);
  else if ($nilai < 1000000) return penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
  else if ($nilai < 1000000000) return penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
  else if ($nilai < 1000000000000) return penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
  else if ($nilai < 1000000000000000) return penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
  return "";
}
function terbilang($nilai){ return ($nilai < 0 ? "minus " : "").trim(penyebut($nilai)).' rupiah'; }

/* ==== Tambahan: meta print (pre-check) ==== */
$printMeta = (isset($printMeta) && is_array($printMeta)) ? $printMeta : [];
/* payments yang sudah tersimpan (CASH/CREDIT/DEBIT/TRANSFER) */
$selPay = array_map('strtoupper', (array)($printMeta['payments'] ?? []));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="" xml:lang="">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="https://fonts.googleapis.com/css?family=Gothic+A1:700&display=swap" rel="stylesheet">
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <style>
    @page { margin: 0; }
    table { border-collapse: collapse; }
    body { font-family: sans-serif; margin: 0 !important; }
    input[type=checkbox] { transform: scale(1.5); }
    .no-margin p { margin: 0px !important; }
    .no-margin span { font-size: 12px !important; }
    @media print{ .no-print, .no-print * { display: none !important; } }
  </style>
  <title><?= $title ?></title>
</head>

<body vlink="blue" link="blue" style="background-color:#A0A0A0;">
  <div style="width:918px;min-height:1188px;background-color:#fff;">
    <div class="no-print" style="padding:5px;margin:0px;" id="printHide">
      <a id="doPrint" href="#!"><img src="<?= base_url() ?>assets/offline/print.png" alt="" style="width:30px;float:right;" /></a>
      <img src="<?= base_url() ?>assets/offline/back.png" alt="" style="width:30px;float:right;cursor: pointer;" onclick="clickBack();" ontouchstart="clickBack();" />
    </div>

    <div style="padding:45px;margin:0px;" id="printNow">
      <div style="width:100%">
        <div style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;">
          <div style="border:1px #000 solid">
            <p style="margin:5px; font-weight: bold; font-size:14px;; text-align:center;">PT Muara Logam Indonesia</p>
          </div>

          <div style="padding-left:5px; padding-top:1px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;border-bottom:1px #000 solid">
            <table style="width: 100%">
              <tr>
              <?php
                $this->db->order_by('urutan_cabang', 'ASC');
                $this->db->where('status', 'ENABLE');
                $cabang = $this->db->get('tb_cabang')->result();

                /* siapkan pre-checked id cabang dari $printMeta['cabang'] */
                $selCabIds = [];
                if (!empty($printMeta['cabang'])) {
                  foreach ((array)$printMeta['cabang'] as $cb) {
                    if (is_array($cb) && isset($cb['id'])) $selCabIds[] = (string)$cb['id'];
                    else $selCabIds[] = (string)$cb;
                  }
                }

                foreach ($cabang as $key => $value):
                  $cid   = isset($value->id_cabang) ? $value->id_cabang : (isset($value->id) ? $value->id : ($key+1));
                  $label = trim($value->nama_cabang.' : '.$value->alamat_cabang);
                  $checked = in_array((string)$cid, $selCabIds, true) ? 'checked' : '';
                  if($key%2 == 0){ echo '</tr><tr>'; }
              ?>
                <td style="width: 5%">
                  <p style="font-size:12px; margin: 0px;">
                    <!-- Tambahan: value, data-label, class untuk diambil JS -->
                    <input class="cb-cabang" type="checkbox"
                           value="<?= htmlspecialchars($cid) ?>"
                           data-label="<?= htmlspecialchars($label) ?>"
                           <?= $checked ?> />
                  </p>
                </td>
                <td style="width: 45%">
                  <p style="font-size:12px; margin: 0px;"><?= $label ?></p>
                </td>
              <?php endforeach; ?>
              </tr>
            </table>
            <p style="text-align:center; font-size: 10px;">
              <a href="https://www.iloveemas.co.id/" style="text-decoration:none; color:black">www.iloveemas.co.id</a>
            </p>
          </div>
        </div>

        <div style="padding-right:5px; width:48%; display:inline-block;vertical-align:text-top;margin-top:15px;">
          <div style="border:1px #000 solid; min-height:172px;">
            <p style="margin:5px; text-align:center; font-weight: bold; font-size:14px">Vendor</p>
            <span style="display: inline-block;width: 100%;border-top: 1px solid black; margin-bottom: 10px;"></span>
            <p style="margin:5px; font-size:12px">Name : <?= ucwords(strtolower($a->nameCustomer)) ?></p>
            <p style="margin:5px; font-size:12px">Id Number : <?= strtoupper($a->c_id_number) ?></p>
            <p style="margin:5px; font-size:12px ">Address : <?= ucwords(strtolower($a->c_address)) ?></p>
            <p style="margin:5px; font-size:12px ;">Resident Address : <?= $a->c_resident_address ?></p>
            <p style="margin:5px; font-size:12px ">Phone Number : <?= $a->c_phone ?></p>
          </div>
        </div>

        <div style="margin-left: 15px; padding-right:5px; padding-top:15px;width:48%; display:inline-block;vertical-align:text-top;">
          <div><p style="margin:5px; font-weight:bold; font-size:14px; text-align:center;"><b>Purchase Payment</b></p></div>
          <span style="display: inline-block;width: 100%;border-top: 1px solid black; margin-bottom:10px;"></span>
          <div>
            <div style="padding-right:5px; width:48%; display:inline-block;">
              <div style="border:1px #000 solid">
                <p style="margin:5px; font-weight:bold; text-align:center; font-size:14px">Payment Date</p>
              </div>
              <div style="text-align:center; min-height:20px; padding-top:30px; padding-bottom:30px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;border-bottom:1px #000 solid">
                <p style="padding-top:5px; font-size: 12px;"><?= date('Y-m-d', strtotime($a->t_date_created)) ?></p>
              </div>
            </div>
            <div style="width:49%; display:inline-block;">
              <div style="border:1px #000 solid">
                <p style="margin:5px; font-weight:bold; text-align:center; font-size:14px">Invoice Number</p>
              </div>
              <div style="text-align:center; min-height:30px; padding-top:30px; padding-bottom:30px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;border-bottom:1px #000 solid">
                <p style="padding-top:5px; font-size: 12px;"><?= $a->t_no_order ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div style="width:100%;margin-top:15px;">
        <table style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top; page-break-inside:auto">
          <tr>
            <td style="padding:10px 0px; width:15px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">No</td>
            <td style="padding-left:5px; min-width:120px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">Material</td>
            <td style="padding-left:5px; min-width:110px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">Type</td>
            <td style="padding-left:5px; min-width:50px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">Carat / Percentage</td>
            <td style="padding-left:5px; min-width:100px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">Weight (gr)</td>
            <td style="padding-left:5px; min-width:145px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">Price/gr (Rp)</td>
            <td style="padding-left:5px; min-width:145px; border: 1px solid black !important; text-align:center; font-weight: bold; font-size: 14px">Amount (Rp)</td>
          </tr>
          <?php $no = 0; foreach ($detail as $d){ $no++; if ($no > 27) { break; } ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border: 1px solid black !important; font-size: 14px"><?= $no ?></td>
              <td style="padding-left:5px; min-width:125px; border: 1px solid black !important; font-size: 14px">
                <?= ($d->ti_material == 'Cust. Profesion') ? 'Gold' : $d->ti_material ?>
              </td>
              <td style="padding-left:5px; min-width:110px; border: 1px solid black !important; font-size: 14px"><?= $d->ti_material_type ?></td>
              <td style="padding-left:5px; min-width:50px; border: 1px solid black !important; font-size: 14px"><?= $d->ti_carat ?></td>
              <td style="padding-left:5px; min-width:100px; border: 1px solid black !important; font-size: 14px"><?= $d->ti_weight ?></td>
              <td style="padding-left:5px; min-width:145px; border: 1px solid black !important; font-size: 14px">
                <?= ($d->ti_price != '-') ? nominal($d->ti_price) : $d->ti_price ?>
              </td>
              <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right; font-size: 14px"><?= nominal($d->ti_price_total) ?></td>
            </tr>
          <?php } ?>
          <tr>
            <td style="padding-left:5px; min-width:30px; border: 1px solid black !important; font-size: 14px">#</td>
            <td style="padding-left:5px; border: 1px solid black !important; font-size: 14px" colspan="5">ADMIN</td>
            <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;" font-size: 14px><?= nominal($a->t_price_admin) ?></td>
          </tr>
          <tr>
            <!-- Tambahan: value & pre-checked, tapi layout tetap -->
            <td style="border: 1px solid black !important; font-size: 14px" colspan="5">
              <span>
                <input class="cb-pay" style="margin:10px 5px 10px 5px;" type="checkbox" value="CASH"     <?= in_array('CASH',$selPay,true)?'checked':''; ?> ><span>Cash</span>
              </span>
              <span>
                <input class="cb-pay" style="margin:10px 5px 10px 65px;" type="checkbox" value="CREDIT"  <?= in_array('CREDIT',$selPay,true)?'checked':''; ?> ><span>Credit</span>
              </span>
              <span>
                <input class="cb-pay" style="margin:10px 5px 10px 65px;" type="checkbox" value="DEBIT"   <?= in_array('DEBIT',$selPay,true)?'checked':''; ?> ><span>Debit</span>
              </span>
              <span>
                <input class="cb-pay" style="margin:10px 5px 10px 65px;" type="checkbox" value="TRANSFER"<?= in_array('TRANSFER',$selPay,true)?'checked':''; ?> ><span>Transfer</span>
              </span>
            </td>
            <td style="min-width:145px; border: 1px solid black !important;text-align:center; font-weight: bold; font-size: 14px">TOTAL</td>
            <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right; font-weight: bold;  font-size: 14px"><?= nominal($a->t_price_total + $a->t_price_admin) ?></td>
          </tr>
        </table>
      </div>

      <?php if (count($detail) > 27) { ?>
        <div style="width:100%;margin-top:0px;">
          <table style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;">
            <?php $no = 0; foreach ($detail as $d){ $no++; if ($no >= 74) { break; } if ($no <= 27) { continue; } ?>
              <tr>
                <td style="padding-left:5px; min-width:30px; border: 1px solid black !important;"><?= $no ?></td>
                <td style="padding-left:5px; min-width:125px; border: 1px solid black !important;"><?= ($d->ti_material == 'Cust. Profesion') ? 'Gold' : $d->ti_material ?></td>
                <td style="padding-left:5px; min-width:110px; border: 1px solid black !important;"><?= $d->ti_material_type ?></td>
                <td style="padding-left:5px; min-width:50px; border: 1px solid black !important;"><?= $d->ti_carat ?></td>
                <td style="padding-left:5px; min-width:100px; border: 1px solid black !important;"><?= $d->ti_weight ?></td>
                <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;"><?= ($d->ti_price != '-') ? nominal($d->ti_price) : $d->ti_price ?></td>
                <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;"><?= nominal($d->ti_price_total) ?></td>
              </tr>
            <?php } ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border: 1px solid black !important;">#</td>
              <td style="padding-left:5px; border: 1px solid black !important;" colspan="5">ADMIN</td>
              <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;"><?= nominal($a->t_price_admin) ?></td>
            </tr>
            <tr>
              <!-- Tambahan: value & pre-checked -->
              <td style="border: 1px solid black !important;" colspan="5">
                <span><input class="cb-pay" style="margin:10px 5px 10px 5px;"   type="checkbox" value="CASH"     <?= in_array('CASH',$selPay,true)?'checked':''; ?> ><span>Cash</span></span>
                <span><input class="cb-pay" style="margin:10px 5px 10px 65px;"  type="checkbox" value="CREDIT"   <?= in_array('CREDIT',$selPay,true)?'checked':''; ?> ><span>Credit</span></span>
                <span><input class="cb-pay" style="margin:10px 5px 10px 65px;"  type="checkbox" value="DEBIT"    <?= in_array('DEBIT',$selPay,true)?'checked':''; ?> ><span>Debit</span></span>
                <span><input class="cb-pay" style="margin:10px 5px 10px 65px;"  type="checkbox" value="TRANSFER" <?= in_array('TRANSFER',$selPay,true)?'checked':''; ?> ><span>Transfer</span></span>
              </td>
              <td style="min-width:145px; border: 1px solid black !important;text-align:center;">TOTAL</td>
              <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;"><?= nominal($a->t_price_total + $a->t_price_admin) ?></td>
            </tr>
          </table>
        </div>
      <?php } ?>

      <?php if (count($detail) > 74) { ?>
        <div style="width:100%;margin-top:0px;">
          <table style="padding-right:5px; width:96%; display:inline-block;vertical-align:text-top;">
            <?php $no = 0; foreach ($detail as $d){ $no++; if ($no < 74) { continue; } ?>
              <tr>
                <td style="padding-left:5px; min-width:30px; border: 1px solid black !important;"><?= $no ?></td>
                <td style="padding-left:5px; min-width:125px; border: 1px solid black !important;"><?= ($d->ti_material == 'Cust. Profesion') ? 'Gold' : $d->ti_material ?></td>
                <td style="padding-left:5px; min-width:110px; border: 1px solid black !important;"><?= $d->ti_material_type ?></td>
                <td style="padding-left:5px; min-width:50px; border: 1px solid black !important;"><?= $d->ti_carat ?></td>
                <td style="padding-left:5px; min-width:100px; border: 1px solid black !important;"><?= $d->ti_weight ?></td>
                <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;"><?= ($d->ti_price != '-') ? nominal($d->ti_price) : $d->ti_price ?></td>
                <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;"><?= nominal($d->ti_price_total) ?></td>
              </tr>
            <?php } ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border: 1px solid black !important;">#</td>
              <td style="padding-left:5px; border: 1px solid black !important;" colspan="5">ADMIN</td>
              <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;"><?= nominal($a->t_price_admin) ?></td>
            </tr>
            <tr>
              <!-- Tambahan: value & pre-checked -->
              <td style="border: 1px solid black !important;" colspan="5">
                <span><input class="cb-pay" style="margin:10px 5px 10px 5px;"   type="checkbox" value="CASH"     <?= in_array('CASH',$selPay,true)?'checked':''; ?> ><span>Cash</span></span>
                <span><input class="cb-pay" style="margin:10px 5px 10px 65px;"  type="checkbox" value="CREDIT"   <?= in_array('CREDIT',$selPay,true)?'checked':''; ?> ><span>Credit</span></span>
                <span><input class="cb-pay" style="margin:10px 5px 10px 65px;"  type="checkbox" value="DEBIT"    <?= in_array('DEBIT',$selPay,true)?'checked':''; ?> ><span>Debit</span></span>
                <span><input class="cb-pay" style="margin:10px 5px 10px 65px;"  type="checkbox" value="TRANSFER" <?= in_array('TRANSFER',$selPay,true)?'checked':''; ?> ><span>Transfer</span></span>
              </td>
              <td style="min-width:145px; border: 1px solid black !important;text-align:center;">TOTAL</td>
              <td style="padding-left:5px; min-width:145px; border: 1px solid black !important;text-align:right;"><?= nominal($a->t_price_total + $a->t_price_admin) ?></td>
            </tr>
          </table>
        </div>
      <?php } ?>

      <?php if(count($detail) > 10){ ?>
        <div style="page-break-inside: avoid">
      <?php }else{ ?>
        <div style="width:100%;margin-top:5px;">
      <?php } ?>
          <table style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;margin-top:10px; page-break-inside:auto">
            <tr>
              <td style="padding:10px 0px 10px 10px; min-width:465px; border: 1px solid black !important; text-align: center; font-weight: bold; font-size:14px;" colspan="5">Syarat & Ketentuan</td>
            </tr>
            <tr>
              <td style="padding:10px 10px 10px 10px; min-width:465px; border: 1px solid black !important;" colspan="5">
                <?php $this->db->order_by('tm_priority', 'asc'); $memo = $this->db->get('tb_memo')->result(); ?>
                <?php foreach ($memo as $key => $value) : ?>
                  <div class="no-margin" style="text-align: justify; font-size:12px"><?= $value->tm_value ?></div>
                <?php endforeach ?>
              </td>
            </tr>
          </table>

          <table style="padding-right:5px; width:100%;vertical-align:text-top;">
            <tr>
              <td style="width: 20%"></td>
              <td style="vertical-align:top; padding-left:5px; min-width:145px;">
                <p style="text-align:center; font-size: 14px">Received By</p>
                <p style="text-align:center;margin-top:100px; font-size: 14px"><?= strtoupper(strtolower($a->nameCustomer)) ?></p>
                <p style="text-align:center;margin-top:-25px; font-size: 14px">--------------</p>
              </td>
              <td style="vertical-align:top; padding-left:5px; min-width:145px;">
                <p style="text-align:center; font-size: 14px">Paid By</p>
                <p style="text-align:center;margin-top:100px; font-size: 14px">I LOVE EMAS</p>
                <p style="text-align:center;margin-top:-25px; font-size: 14px">--------------</p>
              </td>
              <td style="width: 20%"></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Tambahan: variabel global untuk AJAX simpan PDF -->
    <script>
      window.__TRANS_TYPE = 'buy';  /* view ini untuk BUY */
      window.__TRANS_ID   = <?= (int)($a->t_id ?? 0) ?>;
      window.__BASE_URL   = "<?= base_url() ?>";
      window.__CSRF_NAME  = "<?= $this->security->get_csrf_token_name(); ?>";
      window.__CSRF_HASH  = "<?= $this->security->get_csrf_hash(); ?>";
    </script>

    <script>
      // Handler baru: simpan meta & generate PDF di server, lalu (opsional) print + destroy
      (function($){
        function collectCabang(){
          var out = [];
          $('.cb-cabang:checked').each(function(){
            out.push({ id: $(this).val(), label: $(this).data('label') });
          });
          return out;
        }
        function collectPayments(){
          var out = [];
          $('.cb-pay:checked').each(function(){ out.push($(this).val()); });
          return out;
        }
        function savePrintThenNext(opts){
          var url = window.__BASE_URL + 'transaction/savePrint/' + window.__TRANS_TYPE + '/' + window.__TRANS_ID;
          var payload = {
            cabang: collectCabang(),
            payments: collectPayments(),
            paper: 'A4',
            orientation: 'portrait',
            rawHtml: document.getElementById('printNow').outerHTML // simpan juga HTML final (sesuai instruksi)
          };
          payload[window.__CSRF_NAME] = window.__CSRF_HASH;

          $('#doPrint').css('opacity',.6).css('pointer-events','none');

          $.ajax({ url:url, type:'POST', dataType:'json', data:payload })
          .done(function(r){
            // refresh token jika ada
            if (r && r[window.__CSRF_NAME]) { window.__CSRF_HASH = r[window.__CSRF_NAME]; }
            if (r && r.ok){
              if (opts && opts.browserPrint === true) { setTimeout(function(){ window.print(); }, 120); }
              if (opts && opts.finish === true) { ajaxdestroy(); }
              else { $('#doPrint').css('opacity',1).css('pointer-events','auto'); }
            } else {
              alert((r && r.msg) ? r.msg : 'Gagal menyimpan PDF');
              $('#doPrint').css('opacity',1).css('pointer-events','auto');
            }
          })
          .fail(function(){
            alert('Gagal terhubung ke server.');
            $('#doPrint').css('opacity',1).css('pointer-events','auto');
          });
        }

        // Gantikan handler lama: sekarang simpan dulu, lalu print+destroy (tetap mempertahankan flow kamu)
        $('#doPrint').on('click', function(e){
          e.preventDefault();
          savePrintThenNext({ browserPrint:true, finish:true });
        });
      })(jQuery);

      // Fungsi existing: dipertahankan
      function ajaxdestroy() {
        jQuery.ajax({
          url: '<?= base_url('transaction/chart-destroy') ?>',
          success: function(){ window.location.href = "<?= base_url('dashboard') ?>"; },
        });
      }
      function clickBack() {
        window.location.href = "<?= base_url('dashboard') ?>";
      }
    </script>
  </div>
</body>
</html>
