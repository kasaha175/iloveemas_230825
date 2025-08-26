<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
function nominal($angka){ return number_format((float)$angka, 2, ',', '.'); }
$selMat = $this->input->get('material') ?: '';
$selYear= $this->input->get('year')     ?: date('Y');

// siapkan data chart (server -> JS)
$pointsTotal = [];
foreach ($data as $d) {
  $pointsTotal[] = ['label' => (string)$d->m_name, 'y' => (float)$d->priceTotal];
}

// dataCustomer biasanya berisi month (1-12) dan countTransaction
$monthMap = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
$pointsCust = [];
foreach ($dataCustomer as $d) {
  $m = (int)($d['month'] ?? 0);
  $pointsCust[] = ['label' => $monthMap[$m] ?? (string)$m, 'y' => (int)($d['countTransaction'] ?? 0)];
}
?>
<style>
  /* ===== SCOPED: tidak bentrok UserTemplate ===== */
  .sell-graph-scope .master-wrap{ margin-top: calc(var(--topbar-h) + 12px); padding: clamp(12px, 2vw, 20px); }
  .sell-graph-scope .master-container{ max-width: 1280px; margin-inline:auto; }

  .sell-graph-scope .master-crumbs{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:10px 16px; border-radius:9999px;
    background: linear-gradient(135deg, var(--blue-pastel), #dff5ff);
    border:1px solid #dfeaff; color:#0e204a;
    box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:14px;
  }
  .sell-graph-scope .master-crumbs a{ color:#0e204a; text-decoration:none; font-weight:700; font-size:13px; }
  .sell-graph-scope .master-crumbs a:hover{ color:var(--blue-light); text-decoration:underline; }
  .sell-graph-scope .master-crumbs .sep{ color:#7a8eb8; }

  .sell-graph-scope .master-head h3{ margin:8px 0 6px; color:#fff; font-weight:800; font-size:clamp(20px,3.2vw,28px); }
  .sell-graph-scope .master-head p{ margin:0; color:#dbe8ff; font-size:13px; }

  .sell-graph-scope .card{ border-radius:16px; border:1px solid #e6eefc; }
  .sell-graph-scope .card-header{
    background: linear-gradient(135deg, var(--blue-pastel), #e9faff);
    border-bottom-color:#dfeaff; border-top-left-radius:16px; border-top-right-radius:16px;
  }
  .sell-graph-scope .card-header h6{ color:#0e204a; font-weight:800; }

  .sell-graph-scope .chart-box{ height: 360px; width:100%; }
  .sell-graph-scope .master-actions{ margin-top:12px; }
  .sell-graph-scope .btn-back{
    display:inline-flex; align-items:center; gap:8px; background:#fff; color:#0e204a; font-weight:700;
    border:1px solid #dfeaff; padding:10px 14px; border-radius:12px; text-decoration:none;
    box-shadow:0 10px 18px rgba(0,0,0,.06);
  }
  .sell-graph-scope .btn-back:hover{ border-color:#cfe0ff; }
</style>

<div class="sell-graph-scope">
  <div class="master-wrap">
    <div class="master-container">

      <!-- Breadcrumb -->
      <nav class="master-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
        <span class="sep">/</span>
        <a href="<?= base_url('report') ?>">Report</a>
        <span class="sep">/</span>
        <span>Sell Graph</span>
      </nav>

      <!-- Header -->
      <header class="master-head">
        <h3>Report</h3>
        <p>Transaction Sell Graph</p>
      </header>

      <!-- Filter -->
      <section class="mb-3">
        <div class="card shadow-sm">
          <a href="#filterCollapse" class="d-block card-header py-3" data-toggle="collapse" role="button"
             aria-expanded="true" aria-controls="filterCollapse">
            <h6 class="m-0 font-weight-bold">Filter Data</h6>
          </a>
          <div class="collapse show" id="filterCollapse">
            <div class="card-body">
              <form action="<?= base_url('report/sell-graph/') ?>" method="get">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="material">Material</label>
                    <select id="material" class="form-control select2" name="material">
                      <option value="">ALL</option>
                      <?php foreach($materialData as $y): ?>
                        <option value="<?= html_escape($y->m_name) ?>" <?= $selMat===$y->m_name?'selected':'' ?>>
                          <?= html_escape($y->m_name) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="year">Year</label>
                    <select id="year" class="form-control select2" name="year">
                      <option value="">ALL</option>
                      <?php foreach($yearData as $y): ?>
                        <option value="<?= (int)$y->y_name ?>" <?= (string)$selYear===(string)$y->y_name?'selected':'' ?>>
                          <?= (int)$y->y_name ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>

      <!-- Chart: Transaction Data -->
      <section>
        <div class="card shadow-sm">
          <a href="#txCollapse" class="d-block card-header py-3" data-toggle="collapse" role="button"
             aria-expanded="true" aria-controls="txCollapse">
            <h6 class="m-0 font-weight-bold">Transaction Data</h6>
          </a>
          <div class="collapse show" id="txCollapse">
            <div class="card-body">
              <div id="chartTotal" class="chart-box"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Chart: Customer Data -->
      <section class="mt-3">
        <div class="card shadow-sm">
          <a href="#custCollapse" class="d-block card-header py-3" data-toggle="collapse" role="button"
             aria-expanded="true" aria-controls="custCollapse">
            <h6 class="m-0 font-weight-bold">Customer Data</h6>
          </a>
          <div class="collapse show" id="custCollapse">
            <div class="card-body">
              <div id="chartCustomer" class="chart-box"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Back -->
      <div class="master-actions">
        <a href="<?= base_url('report') ?>" class="btn-back">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Kembali ke Report
        </a>
      </div>

    </div>
  </div>
</div>

<!-- CanvasJS (CDN) -->
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<script>
(function SellGraph(){
  // data dari PHP
  const pointsTotal = <?= json_encode($pointsTotal, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  const pointsCust  = <?= json_encode($pointsCust,  JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

  function renderCharts(){
    // Total per material
    $("#chartTotal").CanvasJSChart({
      backgroundColor: "transparent",
      animationEnabled: true,
      exportEnabled: true,
      theme: "light2",
      axisX: { interval: 1, labelMaxWidth: 90, labelWrap: true },
      axisY: { prefix: "IDR ", includeZero: true },
      data: [{
        type: "column",
        indexLabel: "{y}",
        indexLabelFontSize: 12,
        indexLabelPlacement: "outside",
        dataPoints: pointsTotal
      }]
    });

    // Customer by month (count)
    $("#chartCustomer").CanvasJSChart({
      backgroundColor: "transparent",
      animationEnabled: true,
      exportEnabled: true,
      theme: "light2",
      axisX: { interval: 1 },
      axisY: { title: "Jumlah Transaksi", includeZero: true },
      data: [{
        type: "line",
        markerSize: 6,
        indexLabel: "{y}",
        indexLabelFontSize: 12,
        dataPoints: pointsCust
      }]
    });
  }

  // render saat siap + saat resize
  $(function(){ renderCharts(); });
  let resizeTimer = null;
  $(window).on('resize', function(){
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(renderCharts, 200);
  });
})();
</script>
