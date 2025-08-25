<?php
foreach ($data as $a) { /* ambil header ke $a */ }

function nominal($angka){ return number_format($angka, 0, ',', '.'); }
function penyebut($nilai){
  $nilai = abs($nilai);
  $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
  if ($nilai < 12) return " ".$huruf[$nilai];
  if ($nilai < 20) return penyebut($nilai - 10) . " belas";
  if ($nilai < 100) return penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
  if ($nilai < 200) return " seratus" . penyebut($nilai - 100);
  if ($nilai < 1000) return penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
  if ($nilai < 2000) return " seribu" . penyebut($nilai - 1000);
  if ($nilai < 1000000) return penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
  if ($nilai < 1000000000) return penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
  if ($nilai < 1000000000000) return penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
  if ($nilai < 1000000000000000) return penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
  return "";
}
function terbilang($nilai){ return ($nilai < 0 ? "minus " : "") . trim(penyebut($nilai)) . ' rupiah'; }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="" xml:lang="">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <?php if (empty($for_pdf)): ?>
    <link href="https://fonts.googleapis.com/css?family=Gothic+A1:700&display=swap" rel="stylesheet">
  <?php endif; ?>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    @page { margin: 0; }
    table { border-collapse: collapse; }
    body { font-family: sans-serif; margin: 0 !important; }
    input[type=checkbox] { transform: scale(1.5); }
    .no-margin p { margin: 0 !important; }
    .no-margin span { font-size: 12px !important; }
    @media print { .no-print, .no-print * { display: none !important; } }
  </style>
  <title><?= $title ?></title>
</head>

<body vlink="blue" link="blue" style="background-color:#A0A0A0;">
<div style="width:918px;min-height:1188px;background-color:#fff;">

  <?php if (empty($for_pdf)): ?>
  <div class="no-print" style="padding:5px;margin:0px;" id="printHide">
    <a id="doPrint" href="#!"><img src="<?= base_url() ?>assets/offline/print.png" alt="" style="width:30px;float:right;" /></a>
    <img src="<?= base_url() ?>assets/offline/back.png" alt="" style="width:30px;float:right;cursor:pointer;" onclick="clickBack();" ontouchstart="clickBack();" />
  </div>
  <?php endif; ?>

  <div style="padding:45px;margin:0px;" id="printNow">
    <div style="width:100%">
      <div style="padding-right:5px; width:100%; display:inline-block;vertical-align:text-top;">
        <div style="border:1px #000 solid">
          <p style="margin:5px; font-weight:bold; font-size:14px; text-align:center;">
            PT Muara Logam Indonesia
          </p>
        </div>

        <!-- DAFTAR CABANG: struktur <table> rapi untuk Dompdf -->
        <div style="padding-left:5px; padding-top:1px; border-bottom:1px #000 solid;border-left:1px #000 solid;border-right:1px #000 solid;">
          <table style="width:100%">
            <tbody>
            <?php
              $this->db->order_by('urutan_cabang', 'ASC');
              $this->db->where('status', 'ENABLE');
              $cabang = $this->db->get('tb_cabang')->result();
              $i = 0;
              foreach ($cabang as $value):
                if ($i % 2 === 0) echo '<tr>';
            ?>
              <td style="width:5%">
                <p style="font-size:12px; margin:0;">
                  <input type="checkbox"
                         class="chk-cabang"
                         name="cabang[]"
                         value="<?= htmlspecialchars($value->nama_cabang, ENT_QUOTES) ?>"
                         data-address="<?= htmlspecialchars($value->alamat_cabang, ENT_QUOTES) ?>">
                </p>
              </td>
              <td style="width:45%">
                <p style="font-size:12px; margin:0;">
                  <?= $value->nama_cabang ?> : <?= $value->alamat_cabang ?>
                </p>
              </td>
            <?php
                if ($i % 2 === 1) echo '</tr>';
                $i++;
              endforeach;
              if ($i % 2 === 1) { // jika ganjil, lengkapi sel & tutup baris
                echo '<td></td><td></td></tr>';
              }
            ?>
            </tbody>
          </table>
          <p style="text-align:center; font-size:10px;">
            <a href="https://www.iloveemas.co.id/" style="text-decoration:none; color:black">www.iloveemas.co.id</a>
          </p>
        </div>
      </div>

      <div style="padding-right:5px; width:48%; display:inline-block;vertical-align:text-top;margin-top:15px;">
        <div style="border:1px #000 solid; min-height:172px;">
          <p style="margin:5px; text-align:center; font-weight:bold; font-size:14px">Vendor</p>
          <span style="display:inline-block;width:100%;border-top:1px solid black; margin-bottom:10px;"></span>
          <p style="margin:5px; font-size:12px">Name : <?= ucwords(strtolower($a->nameCustomer)) ?></p>
          <p style="margin:5px; font-size:12px">Id Number : <?= strtoupper($a->c_id_number) ?></p>
          <p style="margin:5px; font-size:12px ">Address : <?= ucwords(strtolower($a->c_address)) ?></p>
          <p style="margin:5px; font-size:12px ;">Resident Address : <?= $a->c_resident_address ?></p>
          <p style="margin:5px; font-size:12px ">Phone Number : <?= $a->c_phone ?></p>
        </div>
      </div>

      <div style="margin-left: 15px; padding-right:5px; padding-top:15px;width:48%; display:inline-block;vertical-align:text-top;">
        <div><p style="margin:5px; font-weight:bold; font-size:14px; text-align:center;"><b>Purchase Payment</b></p></div>
        <span style="display:inline-block;width:100%;border-top:1px solid black; margin-bottom:10px;"></span>
        <div>
          <div style="padding-right:5px; width:48%; display:inline-block;">
            <div style="border:1px #000 solid">
              <p style="margin:5px; font-weight:bold; text-align:center; font-size:14px">Payment Date</p>
            </div>
            <div style="text-align:center; min-height:20px; padding:30px 0; border:1px #000 solid; border-top:none;">
              <p style="padding-top:5px; font-size:12px;"><?= date('Y-m-d', strtotime($a->t_date_created)) ?></p>
            </div>
          </div>
          <div style="width:49%; display:inline-block;">
            <div style="border:1px #000 solid">
              <p style="margin:5px; font-weight:bold; text-align:center; font-size:14px">Invoice Number</p>
            </div>
            <div style="text-align:center; min-height:30px; padding:30px 0; border:1px #000 solid; border-top:none;">
              <p style="padding-top:5px; font-size:12px;"><?= $a->t_no_order ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TABEL DETAIL (PAGE 1) -->
    <div style="width:100%;margin-top:15px;">
      <table style="padding-right:5px; width:100%; page-break-inside:auto">
        <thead>
          <tr>
            <td style="padding:10px 0; width:15px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">No</td>
            <td style="padding-left:5px; min-width:120px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">Material</td>
            <td style="padding-left:5px; min-width:110px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">Type</td>
            <td style="padding-left:5px; min-width:50px;  border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">Carat / Percentage</td>
            <td style="padding-left:5px; min-width:100px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">Weight (gr)</td>
            <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">Price/gr (Rp)</td>
            <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">Amount (Rp)</td>
          </tr>
        </thead>
        <tbody>
        <?php $no=0; foreach ($detail as $d) { $no++; if ($no>27) break; ?>
          <tr>
            <td style="padding-left:5px; min-width:30px;  border:1px solid #000; font-size:14px"><?= $no ?></td>
            <td style="padding-left:5px; min-width:125px; border:1px solid #000; font-size:14px">
              <?= ($d->ti_material=='Cust. Profesion') ? 'Gold' : $d->ti_material ?>
            </td>
            <td style="padding-left:5px; min-width:110px; border:1px solid #000; font-size:14px"><?= $d->ti_material_type ?></td>
            <td style="padding-left:5px; min-width:50px;  border:1px solid #000; font-size:14px"><?= $d->ti_carat ?></td>
            <td style="padding-left:5px; min-width:100px; border:1px solid #000; font-size:14px"><?= $d->ti_weight ?></td>
            <td style="padding-left:5px; min-width:145px; border:1px solid #000; font-size:14px">
              <?= ($d->ti_price!='-') ? nominal($d->ti_price) : $d->ti_price ?>
            </td>
            <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right; font-size:14px"><?= nominal($d->ti_price_total) ?></td>
          </tr>
        <?php } ?>
          <tr>
            <td style="padding-left:5px; min-width:30px; border:1px solid #000; font-size:14px">#</td>
            <td style="padding-left:5px; border:1px solid #000; font-size:14px" colspan="5">ADMIN</td>
            <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right; font-size:14px">
              <?= nominal($a->t_price_admin) ?>
            </td>
          </tr>
          <tr>
            <td style="border:1px solid #000; font-size:14px" colspan="5">
              <!-- kirim sebagai array payments[] -->
              <span><input class="pay-type" name="payments[]" value="CASH"     style="margin:10px 5px 10px 5px;"  type="checkbox"><span>Cash</span></span>
              <span><input class="pay-type" name="payments[]" value="CREDIT"   style="margin:10px 5px 10px 65px;" type="checkbox"><span>Credit</span></span>
              <span><input class="pay-type" name="payments[]" value="DEBIT"    style="margin:10px 5px 10px 65px;" type="checkbox"><span>Debit</span></span>
              <span><input class="pay-type" name="payments[]" value="TRANSFER" style="margin:10px 5px 10px 65px;" type="checkbox"><span>Transfer</span></span>
            </td>
            <td style="min-width:145px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px">TOTAL</td>
            <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right; font-weight:bold; font-size:14px">
              <?= nominal($a->t_price_total + $a->t_price_admin) ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <?php if (count($detail) > 27): ?>
      <!-- PAGE 2 -->
      <div style="width:100%;margin-top:0px;">
        <table style="padding-right:5px; width:100%;">
          <tbody>
          <?php $no=0; foreach ($detail as $d) { $no++; if ($no>=74) break; if ($no<=27) continue; ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border:1px solid #000;"><?= $no ?></td>
              <td style="padding-left:5px; min-width:125px; border:1px solid #000;"><?= ($d->ti_material=='Cust. Profesion') ? 'Gold' : $d->ti_material ?></td>
              <td style="padding-left:5px; min-width:110px; border:1px solid #000;"><?= $d->ti_material_type ?></td>
              <td style="padding-left:5px; min-width:50px;  border:1px solid #000;"><?= $d->ti_carat ?></td>
              <td style="padding-left:5px; min-width:100px; border:1px solid #000;"><?= $d->ti_weight ?></td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000;"><?= ($d->ti_price!='-') ? nominal($d->ti_price) : $d->ti_price ?></td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right;"><?= nominal($d->ti_price_total) ?></td>
            </tr>
          <?php } ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border:1px solid #000;">#</td>
              <td style="padding-left:5px; border:1px solid #000;" colspan="5">ADMIN</td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right;"><?= nominal($a->t_price_admin) ?></td>
            </tr>
            <tr>
              <td style="border:1px solid #000;" colspan="5">
                <span><input class="pay-type" name="payments[]" value="CASH"     style="margin:10px 5px 10px 5px;"  type="checkbox"><span>Cash</span></span>
                <span><input class="pay-type" name="payments[]" value="CREDIT"   style="margin:10px 5px 10px 65px;" type="checkbox"><span>Credit</span></span>
                <span><input class="pay-type" name="payments[]" value="DEBIT"    style="margin:10px 5px 10px 65px;" type="checkbox"><span>Debit</span></span>
                <span><input class="pay-type" name="payments[]" value="TRANSFER" style="margin:10px 5px 10px 65px;" type="checkbox"><span>Transfer</span></span>
              </td>
              <td style="min-width:145px; border:1px solid #000; text-align:center;">TOTAL</td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right;"><?= nominal($a->t_price_total + $a->t_price_admin) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <?php if (count($detail) > 74): ?>
      <!-- PAGE 3 -->
      <div style="width:100%;margin-top:0px;">
        <table style="padding-right:5px; width:96%;">
          <tbody>
          <?php $no=0; foreach ($detail as $d) { $no++; if ($no<74) continue; ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border:1px solid #000;"><?= $no ?></td>
              <td style="padding-left:5px; min-width:125px; border:1px solid #000;"><?= ($d->ti_material=='Cust. Profesion') ? 'Gold' : $d->ti_material ?></td>
              <td style="padding-left:5px; min-width:110px; border:1px solid #000;"><?= $d->ti_material_type ?></td>
              <td style="padding-left:5px; min-width:50px;  border:1px solid #000;"><?= $d->ti_carat ?></td>
              <td style="padding-left:5px; min-width:100px; border:1px solid #000;"><?= $d->ti_weight ?></td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000;"><?= ($d->ti_price!='-') ? nominal($d->ti_price) : $d->ti_price ?></td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right;"><?= nominal($d->ti_price_total) ?></td>
            </tr>
          <?php } ?>
            <tr>
              <td style="padding-left:5px; min-width:30px; border:1px solid #000;">#</td>
              <td style="padding-left:5px; border:1px solid #000;" colspan="5">ADMIN</td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right;"><?= nominal($a->t_price_admin) ?></td>
            </tr>
            <tr>
              <td style="border:1px solid #000;" colspan="5">
                <span><input class="pay-type" name="payments[]" value="CASH"     style="margin:10px 5px 10px 5px;"  type="checkbox"><span>Cash</span></span>
                <span><input class="pay-type" name="payments[]" value="CREDIT"   style="margin:10px 5px 10px 65px;" type="checkbox"><span>Credit</span></span>
                <span><input class="pay-type" name="payments[]" value="DEBIT"    style="margin:10px 5px 10px 65px;" type="checkbox"><span>Debit</span></span>
                <span><input class="pay-type" name="payments[]" value="TRANSFER" style="margin:10px 5px 10px 65px;" type="checkbox"><span>Transfer</span></span>
              </td>
              <td style="min-width:145px; border:1px solid #000; text-align:center;">TOTAL</td>
              <td style="padding-left:5px; min-width:145px; border:1px solid #000; text-align:right;"><?= nominal($a->t_price_total + $a->t_price_admin) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <!-- SYARAT & TTD -->
    <?php if(count($detail) > 10){ ?>
      <div style="page-break-inside: avoid">
    <?php } else { ?>
      <div style="width:100%;margin-top:5px;">
    <?php } ?>
        <table style="padding-right:5px; width:100%; margin-top:10px; page-break-inside:auto">
          <tbody>
          <tr>
            <td style="padding:10px 0 10px 10px; min-width:465px; border:1px solid #000; text-align:center; font-weight:bold; font-size:14px;" colspan="5">
              Syarat & Ketentuan
            </td>
          </tr>
          <tr>
            <td style="padding:10px; min-width:465px; border:1px solid #000;" colspan="5">
              <?php
              $this->db->order_by('tm_priority', 'asc');
              $memo = $this->db->get('tb_memo')->result(); ?>
              <?php foreach ($memo as $key => $value) : ?>
                <div class="no-margin" style="text-align:justify; font-size:12px"><?= $value->tm_value ?></div>
              <?php endforeach ?>
            </td>
          </tr>
          </tbody>
        </table>
        <table style="padding-right:5px; width:100%;">
          <tbody>
          <tr>
            <td style="width:20%"></td>
            <td style="vertical-align:top; padding-left:5px; min-width:145px;">
              <p style="text-align:center; font-size:14px">Received By</p>
              <p style="text-align:center;margin-top:100px; font-size:14px"><?= strtoupper(strtolower($a->nameCustomer)) ?></p>
              <p style="text-align:center;margin-top:-25px; font-size:14px">--------------</p>
            </td>
            <td style="vertical-align:top; padding-left:5px; min-width:145px;">
              <p style="text-align:center; font-size:14px">Paid By</p>
              <p style="text-align:center;margin-top:100px; font-size:14px">I LOVE EMAS</p>
              <p style="text-align:center;margin-top:-25px; font-size:14px">--------------</p>
            </td>
            <td style="width:20%"></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php if (empty($for_pdf)): ?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
    const idTransaction = "<?= $this->uri->segment(3) ?>";
    const printType = "BUY"; // GANTI "SELL" jika ini view PrintSell

    // CSRF support (kalau aktif)
    const CSRF_ENABLED   = <?= config_item('csrf_protection') ? 'true' : 'false' ?>;
    const CSRF_TOKENNAME = "<?= $this->security->get_csrf_token_name() ?>";
    const CSRF_HASH      = "<?= $this->security->get_csrf_hash() ?>";

    function collectInput(){
      const cabang=[], payments=[];
      $('.chk-cabang:checked').each(function(){
        cabang.push({ nama: $(this).val(), alamat: $(this).data('address') || '' });
      });
      $('.pay-type:checked').each(function(){
        payments.push($(this).val());
      });
      return { cabang, payments };
    }

    function endpoint(){
      return (printType === 'BUY')
        ? "<?= base_url('report/buy-print-action/') ?>"+idTransaction
        : "<?= base_url('report/sell-print-action/') ?>"+idTransaction;
    }

    // fallback kirim form-encoded kalau JSON di-block (mis. CSRF)
    function sendFormEncoded(url, dataObj, done){
      const postData = { payload: JSON.stringify(dataObj) };
      if (CSRF_ENABLED) postData[CSRF_TOKENNAME] = CSRF_HASH;
      $.ajax({
        url: url, type: 'POST', dataType: 'json',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        data: postData, success: done, error: done
      });
    }

    $('#doPrint').on('click touchstart', function(e){
      e.preventDefault();
      $('#doPrint').css('pointer-events','none');

      const dataInput = collectInput();
      const url = endpoint();

      $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(dataInput),
        success: handleSuccess,
        error: function(){
          // Fallback ke form-encoded + CSRF
          sendFormEncoded(url, dataInput, handleSuccess);
        },
        complete: function(){ setTimeout(function(){ $('#doPrint').css('pointer-events','auto'); }, 800); }
      });

      function handleSuccess(res){
        if(res && res.ok && res.pdf_url){
          // Print PDF hasil generate
          const $f = $('<iframe>',{
            id:'pdfFrame', src:res.pdf_url,
            style:'visibility:hidden;position:fixed;right:0;bottom:0;width:0;height:0;border:0;'
          }).appendTo('body');
          $f.on('load', function(){
            try { this.contentWindow.focus(); this.contentWindow.print(); } catch(e){}
            setTimeout(function(){ ajaxdestroy(); $f.remove(); }, 1000);
          });
        } else {
          // fallback: print HTML
          window.print();
          ajaxdestroy();
        }
      }
    });

    function ajaxdestroy() {
      $.ajax({
        url: '<?= base_url('transaction/chart-destroy') ?>',
        complete: function() { window.location.href = "<?= base_url('dashboard') ?>"; }
      });
    }
    function clickBack(){ window.location.href = "<?= base_url('dashboard') ?>"; }
  </script>
  <?php endif; ?>
</div>
</body>
</html>
