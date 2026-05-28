<?php
$data = [
  ['thang' => 1, 'di_sl' => 167382, 'di_kl' => 170249503, 'den_sl' => 263082, 'den_kl' => 197185017],
  ['thang' => 2, 'di_sl' => 95480, 'di_kl' => 81843195, 'den_sl' => 208463, 'den_kl' => 116483363],
  ['thang' => 3, 'di_sl' => 151387, 'di_kl' => 129665665, 'den_sl' => 223330, 'den_kl' => 159011931],
  ['thang' => 4, 'di_sl' => 148729, 'di_kl' => 117079989, 'den_sl' => 229993, 'den_kl' => 140780620],
];
$tong = [
  'di_sl' => array_sum(array_column($data, 'di_sl')),
  'di_kl' => array_sum(array_column($data, 'di_kl')),
  'den_sl' => array_sum(array_column($data, 'den_sl')),
  'den_kl' => array_sum(array_column($data, 'den_kl')),
];
function fmt($n, $dec = 0)
{
  return number_format($n, $dec, ',', '.');
}

$labels = json_encode(array_map(function ($r) {
  return "Tháng {$r['thang']}"; }, $data));
$di_sl = json_encode(array_column($data, 'di_sl'));
$di_kl = json_encode(array_column($data, 'di_kl'));
$den_sl = json_encode(array_column($data, 'den_sl'));
$den_kl = json_encode(array_column($data, 'den_kl'));
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Báo Cáo Bưu Gửi Theo Tháng</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    :root {
      --bg: #0f1117;
      --surface: #1a1d27;
      --card: #22263a;
      --border: #2e3352;
      --a1: #6c63ff;
      --a2: #ff6584;
      --a3: #43e97b;
      --a4: #fa8231;
      --text: #e8eaf6;
      --muted: #8891b4;
      --r: 16px;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      padding: 0 0 60px
    }

    /* HEADER */
    header {
      background: linear-gradient(135deg, #1a1d27, #12152b);
      border-bottom: 1px solid var(--border);
      padding: 32px 40px 26px;
      display: flex;
      align-items: center;
      gap: 20px;
      position: relative;
      overflow: hidden;
    }

    header::before {
      content: '';
      position: absolute;
      top: -60px;
      right: -60px;
      width: 260px;
      height: 260px;
      background: radial-gradient(circle, rgba(108, 99, 255, .22) 0%, transparent 70%);
      pointer-events: none
    }

    .hicon {
      width: 54px;
      height: 54px;
      background: linear-gradient(135deg, var(--a1), var(--a2));
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      flex-shrink: 0;
      box-shadow: 0 8px 24px rgba(108, 99, 255, .4)
    }

    header h1 {
      font-size: 1.65rem;
      font-weight: 800;
      line-height: 1.2
    }

    header p {
      font-size: .85rem;
      color: var(--muted);
      margin-top: 5px
    }

    /* TOOLBAR */
    .toolbar {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      padding: 20px 40px 0;
    }

    .tab-btn {
      background: var(--surface);
      border: 1px solid var(--border);
      color: var(--muted);
      font-family: inherit;
      font-size: .875rem;
      font-weight: 500;
      padding: 9px 20px;
      border-radius: 50px;
      cursor: pointer;
      transition: all .22s;
    }

    .tab-btn:hover {
      color: var(--text);
      border-color: var(--a1)
    }

    .tab-btn.active {
      background: linear-gradient(135deg, var(--a1), #8b7fff);
      border-color: transparent;
      color: #fff;
      box-shadow: 0 4px 16px rgba(108, 99, 255, .4);
    }

    /* UNIT SWITCHER */
    .unit-group {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 6px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 50px;
      padding: 4px 6px;
    }

    .unit-group label {
      font-size: .75rem;
      color: var(--muted);
      padding: 0 8px
    }

    .unit-btn {
      background: transparent;
      border: none;
      color: var(--muted);
      font-family: inherit;
      font-size: .8rem;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 40px;
      cursor: pointer;
      transition: all .2s;
    }

    .unit-btn:hover {
      color: var(--text)
    }

    .unit-btn.active {
      background: var(--a4);
      color: #fff;
      box-shadow: 0 2px 10px rgba(250, 130, 49, .4)
    }

    /* MAIN */
    main {
      padding: 24px 40px
    }

    .tab-panel {
      display: none
    }

    .tab-panel.active {
      display: block
    }

    /* KPI */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 16px;
      margin-bottom: 28px
    }

    .kpi-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      padding: 22px;
      position: relative;
      overflow: hidden;
      transition: transform .2s, box-shadow .2s;
    }

    .kpi-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, .4)
    }

    .kpi-card .dot {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 7px
    }

    .kpi-card .lbl {
      font-size: .75rem;
      font-weight: 700;
      letter-spacing: .05em;
      text-transform: uppercase;
      color: var(--muted)
    }

    .kpi-card .val {
      font-size: 1.75rem;
      font-weight: 800;
      margin-top: 8px;
      line-height: 1
    }

    .kpi-card .sub {
      font-size: .78rem;
      color: var(--muted);
      margin-top: 5px
    }

    .kpi-card::after {
      content: '';
      position: absolute;
      bottom: -20px;
      right: -20px;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      opacity: .12
    }

    .kpi-card.c1::after {
      background: var(--a1)
    }

    .kpi-card.c2::after {
      background: var(--a2)
    }

    .kpi-card.c3::after {
      background: var(--a3)
    }

    .kpi-card.c4::after {
      background: var(--a4)
    }

    /* TABLE CARD */
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      overflow: hidden;
      margin-bottom: 24px
    }

    .card-header {
      padding: 18px 22px;
      border-bottom: 1px solid var(--border);
      font-size: .95rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px
    }

    .badge {
      font-size: .7rem;
      padding: 3px 10px;
      border-radius: 20px;
      background: rgba(108, 99, 255, .2);
      color: var(--a1);
      font-weight: 600
    }

    table {
      width: 100%;
      border-collapse: collapse
    }

    thead tr {
      background: rgba(108, 99, 255, .1)
    }

    th {
      padding: 13px 16px;
      text-align: right;
      font-size: .76rem;
      font-weight: 700;
      letter-spacing: .05em;
      text-transform: uppercase;
      color: var(--muted);
      border-bottom: 1px solid var(--border)
    }

    th:first-child {
      text-align: center
    }

    th.g {
      text-align: center;
      border-left: 2px solid rgba(108, 99, 255, .3)
    }

    th.gd {
      border-left: 2px solid rgba(255, 101, 132, .3)
    }

    td {
      padding: 13px 16px;
      text-align: right;
      font-size: .9rem;
      border-bottom: 1px solid rgba(46, 51, 82, .5);
      transition: background .15s
    }

    td:first-child {
      text-align: center;
      font-weight: 700;
      color: var(--a1)
    }

    tbody tr:hover td {
      background: rgba(108, 99, 255, .06)
    }

    tbody tr:last-child td {
      border-bottom: none
    }

    tfoot td {
      font-weight: 800;
      font-size: .93rem;
      background: rgba(108, 99, 255, .1);
      color: var(--a3);
      border-top: 2px solid var(--a1);
      border-bottom: none
    }

    tfoot td:first-child {
      color: var(--text)
    }

    /* CHARTS */
    .chart-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 22px
    }

    .chart-wrap {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      padding: 22px
    }

    .chart-title {
      font-size: .92rem;
      font-weight: 700;
      margin-bottom: 16px
    }

    .chart-canvas-wrap {
      position: relative;
      height: 300px
    }

    @media(max-width:768px) {

      header,
      .toolbar,
      main {
        padding-left: 16px;
        padding-right: 16px
      }

      .chart-grid {
        grid-template-columns: 1fr
      }

      th,
      td {
        padding: 9px 9px;
        font-size: .78rem
      }

      .unit-group {
        margin-left: 0
      }
    }
  </style>
</head>

<body>

  <header>
    <div class="hicon">📦</div>
    <div>
      <h1>Báo Cáo Bưu Gửi Theo Tháng</h1>
      <p>Sản lượng &amp; Khối lượng · Chiều đi / Chiều đến &nbsp;·&nbsp; <em style="color:#fa8231">SL không bao gồm Cửa
          Lò</em></p>
    </div>
  </header>

  <div class="toolbar">
    <button class="tab-btn active" onclick="switchTab('bang',this)">📋 Bảng số liệu</button>
    <button class="tab-btn" onclick="switchTab('cot',this)">📊 Biểu đồ cột</button>
    <button class="tab-btn" onclick="switchTab('tron',this)">🥧 Biểu đồ tròn</button>

    <div class="unit-group">
      <label>⚖️ Đơn vị KL:</label>
      <button class="unit-btn active" onclick="setUnit('g',this)">gram</button>
      <button class="unit-btn" onclick="setUnit('kg',this)">kg</button>
      <button class="unit-btn" onclick="setUnit('t',this)">tấn</button>
    </div>
  </div>

  <main>

    <!-- KPI -->
    <div class="kpi-grid">
      <div class="kpi-card c1">
        <div class="lbl"><span class="dot" style="background:var(--a1)"></span>Tổng SL Chiều đi</div>
        <div class="val"><?= fmt($tong['di_sl']) ?></div>
        <div class="sub">bưu gửi</div>
      </div>
      <div class="kpi-card c2">
        <div class="lbl"><span class="dot" style="background:var(--a2)"></span>Tổng KL Chiều đi</div>
        <div class="val" id="kpi-di-kl"><?= fmt($tong['di_kl']) ?></div>
        <div class="sub" id="kpi-di-kl-unit">gram</div>
      </div>
      <div class="kpi-card c3">
        <div class="lbl"><span class="dot" style="background:var(--a3)"></span>Tổng SL Chiều đến</div>
        <div class="val"><?= fmt($tong['den_sl']) ?></div>
        <div class="sub">bưu gửi</div>
      </div>
      <div class="kpi-card c4">
        <div class="lbl"><span class="dot" style="background:var(--a4)"></span>Tổng KL Chiều đến</div>
        <div class="val" id="kpi-den-kl"><?= fmt($tong['den_kl']) ?></div>
        <div class="sub" id="kpi-den-kl-unit">gram</div>
      </div>
    </div>

    <!-- TAB BẢNG -->
    <div id="tab-bang" class="tab-panel active">
      <div class="card">
        <div class="card-header">
          📋 Chi tiết theo tháng
          <span class="badge">Q1–Q2 2025</span>
          <span id="tbl-unit-badge" style="margin-left:auto;font-size:.78rem;color:var(--a4);font-weight:600">Khối
            lượng: gram</span>
        </div>
        <table>
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align:middle;text-align:center">Tháng</th>
              <th colspan="2" class="g" style="color:#a89fff">Chiều đi</th>
              <th colspan="2" class="g gd" style="color:#ff8fab">Chiều đến</th>
            </tr>
            <tr>
              <th class="g">Sản lượng (bưu gửi)</th>
              <th class="g" id="th-di-kl">Khối lượng (gram)</th>
              <th class="g gd">Sản lượng (bưu gửi)</th>
              <th class="g gd" id="th-den-kl">Khối lượng (gram)</th>
            </tr>
          </thead>
          <tbody id="tbl-body">
            <?php foreach ($data as $r): ?>
              <tr>
                <td><?= $r['thang'] ?></td>
                <td><?= fmt($r['di_sl']) ?></td>
                <td class="kl-di"><?= fmt($r['di_kl']) ?></td>
                <td><?= fmt($r['den_sl']) ?></td>
                <td class="kl-den"><?= fmt($r['den_kl']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td>Tổng</td>
              <td><?= fmt($tong['di_sl']) ?></td>
              <td id="foot-di-kl" class="kl-di"><?= fmt($tong['di_kl']) ?></td>
              <td><?= fmt($tong['den_sl']) ?></td>
              <td id="foot-den-kl" class="kl-den"><?= fmt($tong['den_kl']) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <p style="color:var(--muted);font-size:.8rem;text-align:center">* SL không bao gồm Cửa Lò</p>
    </div>

    <!-- TAB CỘT -->
    <div id="tab-cot" class="tab-panel">
      <div class="chart-grid">
        <div class="chart-wrap">
          <div class="chart-title">📦 Sản lượng (bưu gửi) theo tháng</div>
          <div class="chart-canvas-wrap"><canvas id="chartBarSL"></canvas></div>
        </div>
        <div class="chart-wrap">
          <div class="chart-title" id="bar-kl-title">⚖️ Khối lượng (gram) theo tháng</div>
          <div class="chart-canvas-wrap"><canvas id="chartBarKL"></canvas></div>
        </div>
      </div>
    </div>

    <!-- TAB TRÒN -->
    <div id="tab-tron" class="tab-panel">
      <div class="chart-grid">
        <div class="chart-wrap">
          <div class="chart-title">🥧 Tỷ trọng SL Chiều đi theo tháng</div>
          <div class="chart-canvas-wrap"><canvas id="chartPieDiSL"></canvas></div>
        </div>
        <div class="chart-wrap">
          <div class="chart-title">🥧 Tỷ trọng SL Chiều đến theo tháng</div>
          <div class="chart-canvas-wrap"><canvas id="chartPieDenSL"></canvas></div>
        </div>
        <div class="chart-wrap">
          <div class="chart-title" id="pie-di-kl-title">🥧 Tỷ trọng KL Chiều đi (gram)</div>
          <div class="chart-canvas-wrap"><canvas id="chartPieDiKL"></canvas></div>
        </div>
        <div class="chart-wrap">
          <div class="chart-title" id="pie-den-kl-title">🥧 Tỷ trọng KL Chiều đến (gram)</div>
          <div class="chart-canvas-wrap"><canvas id="chartPieDenKL"></canvas></div>
        </div>
      </div>
    </div>

  </main>

  <script>
    /* ── Raw data (gram) ── */
    const labels = <?= $labels ?>;
    const di_sl_raw = <?= $di_sl ?>;
    const di_kl_raw = <?= $di_kl ?>;
    const den_sl_raw = <?= $den_sl ?>;
    const den_kl_raw = <?= $den_kl ?>;
    const tong_di_kl = <?= $tong['di_kl'] ?>;
    const tong_den_kl = <?= $tong['den_kl'] ?>;

    /* ── Unit config ── */
    const UNITS = {
      g: { label: 'gram', div: 1, dec: 0 },
      kg: { label: 'kg', div: 1000, dec: 1 },
      t: { label: 'tấn', div: 1000000, dec: 3 },
    };
    let currentUnit = 'g';

    function convertArr(arr, unit) {
      const u = UNITS[unit];
      return arr.map(v => parseFloat((v / u.div).toFixed(u.dec)));
    }
    function fmtVal(v, unit) {
      const u = UNITS[unit];
      return parseFloat((v / u.div).toFixed(u.dec)).toLocaleString('vi-VN');
    }
    function fmtNum(v) { return v.toLocaleString('vi-VN'); }

    /* ── Colors ── */
    const C = {
      di: { bar: 'rgba(108,99,255,.85)', border: '#6c63ff' },
      den: { bar: 'rgba(255,101,132,.85)', border: '#ff6584' },
    };
    const PIE_C = ['rgba(108,99,255,.92)', 'rgba(255,101,132,.92)', 'rgba(67,233,123,.92)', 'rgba(250,130,49,.92)'];
    const gridOpts = { color: 'rgba(46,51,82,.7)', drawBorder: false };

    Chart.defaults.color = '#8891b4';
    Chart.defaults.font.family = "'Inter',sans-serif";
    Chart.defaults.font.size = 12;

    /* ── Register datalabels plugin ── */
    Chart.register(ChartDataLabels);

    /* ── BAR: SẢN LƯỢNG ── */
    const chartBarSL = new Chart(document.getElementById('chartBarSL'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label: 'Chiều đi', data: di_sl_raw, backgroundColor: C.di.bar, borderColor: C.di.border, borderWidth: 2, borderRadius: 8, borderSkipped: false },
          { label: 'Chiều đến', data: den_sl_raw, backgroundColor: C.den.bar, borderColor: C.den.border, borderWidth: 2, borderRadius: 8, borderSkipped: false },
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          datalabels: { display: false },
          legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } },
          tooltip: { callbacks: { label: ctx => ` ${ctx.dataset.label}: ${fmtNum(ctx.parsed.y)} bưu gửi` } }
        },
        scales: {
          x: { grid: gridOpts },
          y: { grid: gridOpts, ticks: { callback: v => fmtNum(v) } }
        }
      }
    });

    /* ── BAR: KHỐI LƯỢNG ── */
    const chartBarKL = new Chart(document.getElementById('chartBarKL'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label: 'Chiều đi', data: convertArr(di_kl_raw, 'g'), backgroundColor: C.di.bar, borderColor: C.di.border, borderWidth: 2, borderRadius: 8, borderSkipped: false },
          { label: 'Chiều đến', data: convertArr(den_kl_raw, 'g'), backgroundColor: C.den.bar, borderColor: C.den.border, borderWidth: 2, borderRadius: 8, borderSkipped: false },
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          datalabels: { display: false },
          legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } },
          tooltip: { callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('vi-VN')} ${UNITS[currentUnit].label}` } }
        },
        scales: {
          x: { grid: gridOpts },
          y: { grid: gridOpts, ticks: { callback: v => v.toLocaleString('vi-VN') } }
        }
      }
    });

    /* ── PIE helper ── */
    function makePie(id, rawData, isKL) {
      return new Chart(document.getElementById(id), {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data: isKL ? convertArr(rawData, 'g') : rawData,
            backgroundColor: PIE_C,
            borderColor: '#22263a',
            borderWidth: 3,
            hoverOffset: 12,
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          cutout: '52%',
          plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } },
            datalabels: {
              color: '#fff',
              font: { weight: '700', size: 13 },
              formatter: (value, ctx) => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                const pct = ((value / total) * 100).toFixed(1);
                return pct + '%';
              },
              anchor: 'center', align: 'center',
              textStrokeColor: 'rgba(0,0,0,.5)',
              textStrokeWidth: 3,
            },
            tooltip: {
              callbacks: {
                label: ctx => {
                  const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                  const pct = ((ctx.parsed / total) * 100).toFixed(1);
                  const unit = isKL ? UNITS[currentUnit].label : 'bưu gửi';
                  return ` ${ctx.label}: ${ctx.parsed.toLocaleString('vi-VN')} ${unit} (${pct}%)`;
                }
              }
            }
          }
        }
      });
    }

    const chartPieDiSL = makePie('chartPieDiSL', di_sl_raw, false);
    const chartPieDenSL = makePie('chartPieDenSL', den_sl_raw, false);
    const chartPieDiKL = makePie('chartPieDiKL', di_kl_raw, true);
    const chartPieDenKL = makePie('chartPieDenKL', den_kl_raw, true);

    /* ── Update KL charts + table when unit changes ── */
    function setUnit(unit, btn) {
      if (currentUnit === unit) return;
      currentUnit = unit;
      document.querySelectorAll('.unit-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const u = UNITS[unit];
      const diKL = convertArr(di_kl_raw, unit);
      const denKL = convertArr(den_kl_raw, unit);

      // Bar chart KL
      chartBarKL.data.datasets[0].data = diKL;
      chartBarKL.data.datasets[1].data = denKL;
      chartBarKL.update();

      // Pie KL
      chartPieDiKL.data.datasets[0].data = diKL;
      chartPieDenKL.data.datasets[0].data = denKL;
      chartPieDiKL.update();
      chartPieDenKL.update();

      // Table cells
      const diCells = document.querySelectorAll('.kl-di');
      const denCells = document.querySelectorAll('.kl-den');
      [...di_kl_raw, tong_di_kl].forEach((v, i) => {
        if (diCells[i]) diCells[i].textContent = fmtVal(v, unit);
      });
      [...den_kl_raw, tong_den_kl].forEach((v, i) => {
        if (denCells[i]) denCells[i].textContent = fmtVal(v, unit);
      });

      // KPI cards
      document.getElementById('kpi-di-kl').textContent = fmtVal(tong_di_kl, unit);
      document.getElementById('kpi-di-kl-unit').textContent = u.label;
      document.getElementById('kpi-den-kl').textContent = fmtVal(tong_den_kl, unit);
      document.getElementById('kpi-den-kl-unit').textContent = u.label;

      // Titles & headers
      const lbl = u.label;
      document.getElementById('th-di-kl').textContent = `Khối lượng (${lbl})`;
      document.getElementById('th-den-kl').textContent = `Khối lượng (${lbl})`;
      document.getElementById('bar-kl-title').textContent = `⚖️ Khối lượng (${lbl}) theo tháng`;
      document.getElementById('pie-di-kl-title').textContent = `🥧 Tỷ trọng KL Chiều đi (${lbl})`;
      document.getElementById('pie-den-kl-title').textContent = `🥧 Tỷ trọng KL Chiều đến (${lbl})`;
      document.getElementById('tbl-unit-badge').textContent = `Khối lượng: ${lbl}`;
    }

    /* ── Tab switch ── */
    function switchTab(name, btn) {
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById('tab-' + name).classList.add('active');
      btn.classList.add('active');
    }
  </script>
</body>

</html>