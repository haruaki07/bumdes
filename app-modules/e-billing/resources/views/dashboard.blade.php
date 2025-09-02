<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">E-Billing</div>
          <h2 class="page-title">Dashboard</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <div class="btn-group" role="group" aria-label="Toggle period">
              <input type="radio" class="btn-check" name="periodType" id="periodMonthly" autocomplete="off" checked>
              <label class="btn" for="periodMonthly">Bulanan</label>
              <input type="radio" class="btn-check" name="periodType" id="periodAnnual" autocomplete="off">
              <label class="btn" for="periodAnnual">Tahunan</label>
            </div>
            <div style="width:165px">
              <input type="month" class="form-control" id="monthPicker">
              <input type="number" class="form-control d-none" id="yearPicker" min="2000" max="2100">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <!-- KPIs -->
      <div class="row row-deck row-cards" id="kpiRow">
        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Pendapatan</div>
                <div class="ms-auto lh-1">
                  <span id="incomeGrowth" class="badge" title="Perubahan dibanding periode sebelumnya">0%</span>
                </div>
              </div>
              <div class="h1 mb-3" id="income">Rp0</div>
              <div class="d-flex mb-2">
                <div>Periode</div>
                <div class="ms-auto" id="periodRange">-</div>
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-primary" id="incomeProgress" style="width: 0%" role="progressbar"
                  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Invoice Lunas</div>
              </div>
              <div class="h1 mb-3" id="paidCount">0</div>
              <div class="text-muted">Terbit & dibayar pada periode</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Invoice Belum Lunas</div>
              </div>
              <div class="h1 mb-3" id="unpaidCount">0</div>
              <div class="text-muted">Total belum dibayar: <span id="unpaidTotal">Rp0</span></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Pelanggan Aktif</div>
              </div>
              <div class="h1 mb-3" id="activeCustomers">0</div>
              <div class="text-muted">Total pelanggan aktif saat ini</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="row row-deck row-cards mt-2">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Grafik Pendapatan</h3>
            </div>
            <div class="card-body">
              <div id="revenueChart"></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Paket Terlaris (Pendapatan)</h3>
            </div>
            <div class="card-body">
              <div id="packagesChart"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1"></script>
    <script>
      const rupiah = (v) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
      }).format(v || 0);

      const els = {
        income: document.getElementById('income'),
        incomeGrowth: document.getElementById('incomeGrowth'),
        incomeProgress: document.getElementById('incomeProgress'),
        paidCount: document.getElementById('paidCount'),
        unpaidCount: document.getElementById('unpaidCount'),
        unpaidTotal: document.getElementById('unpaidTotal'),
        activeCustomers: document.getElementById('activeCustomers'),
        periodRange: document.getElementById('periodRange'),
        monthPicker: document.getElementById('monthPicker'),
        yearPicker: document.getElementById('yearPicker'),
        periodMonthly: document.getElementById('periodMonthly'),
        periodAnnual: document.getElementById('periodAnnual'),
      };

      // Initialize pickers with current period
      const now = new Date();
      els.monthPicker.value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
      els.yearPicker.value = now.getFullYear();

      function togglePickers() {
        const isAnnual = els.periodAnnual.checked;
        els.monthPicker.classList.toggle('d-none', isAnnual);
        els.yearPicker.classList.toggle('d-none', !isAnnual);
      }
      els.periodMonthly.addEventListener('change', togglePickers);
      els.periodAnnual.addEventListener('change', togglePickers);
      togglePickers();

      let revenueChart, packagesChart;

      function renderCharts(data) {
        const revLabels = data.charts.revenue.labels;
        const revData = data.charts.revenue.data;

        const lineOpts = {
          chart: {
            type: "line",
            fontFamily: "inherit",
            height: 288,
            parentHeightOffset: 0,
            toolbar: {
              show: false,
            },
            animations: {
              enabled: false,
            },
          },
          stroke: {
            width: 2,
            lineCap: "round",
            curve: "smooth",
          },
          series: [{
            name: 'Pendapatan',
            data: revData
          }],
          xaxis: {
            categories: revLabels,
            type: 'category',
            labels: {
              padding: 0
            },
            tooltip: {
              enabled: false
            }
          },
          yaxis: {
            labels: {
              padding: 4,
              formatter: (v) => rupiah(v).replace('Rp', '')
            }
          },
          colors: ['color-mix(in srgb, transparent, var(--tblr-primary) 100%)'],
          fill: {
            colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 16%)"],
            type: "solid",
          },
          dataLabels: {
            enabled: false
          },
          tooltip: {
            theme: 'dark',
            y: {
              formatter: (val) => rupiah(val)
            }
          },
          grid: {
            padding: {
              top: -20,
              right: 0,
              left: -4,
              bottom: -4
            },
            strokeDashArray: 4
          },
          legend: {
            show: false
          }
        };

        if (revenueChart) {
          revenueChart.updateOptions({
            xaxis: {
              categories: revLabels
            }
          });
          revenueChart.updateSeries([{
            name: 'Pendapatan',
            data: revData
          }], true);
        } else {
          revenueChart = new ApexCharts(document.querySelector('#revenueChart'), lineOpts);
          revenueChart.render();
        }


        const pkgLabels = data.charts.packages.map(p => p.name);
        const pkgData = data.charts.packages.map(p => p.total);
        const barOpts = {
          chart: {
            type: 'bar',
            fontFamily: 'inherit',
            height: 288,
            parentHeightOffset: 0,
            toolbar: {
              show: false
            },
            animations: {
              enabled: false
            }
          },
          plotOptions: {
            bar: {
              horizontal: true,
              barHeight: '65%',
              borderRadius: 4
            }
          },
          series: [{
            name: 'Pendapatan',
            data: pkgData
          }],
          xaxis: {
            categories: pkgLabels,
            labels: {
              padding: 0,
              formatter: (v) => rupiah(v).replace('Rp', '')
            },
            tooltip: {
              enabled: false
            }
          },
          yaxis: {
            labels: {
              padding: 4
            }
          },
          colors: ['var(--tblr-green)'],
          dataLabels: {
            enabled: false
          },
          tooltip: {
            theme: 'dark',
            y: {
              formatter: (val) => rupiah(val)
            }
          },
          grid: {
            padding: {
              top: -20,
              right: 0,
              left: -4,
              bottom: -4
            },
            strokeDashArray: 4
          },
          legend: {
            show: false
          }
        };
        if (packagesChart) {
          packagesChart.updateOptions({
            xaxis: {
              categories: pkgLabels
            }
          });
          packagesChart.updateSeries([{
            name: 'Pendapatan',
            data: pkgData
          }], true);
        } else {
          packagesChart = new ApexCharts(document.querySelector('#packagesChart'), barOpts);
          packagesChart.render();
        }
      }

      function paintKpis(k) {
        els.income.textContent = rupiah(k.income);
        const g = k.incomeGrowthPct;
        els.incomeGrowth.textContent = (g === null ? 'N/A' : `${g > 0 ? '+' : ''}${g}%`);
        els.incomeGrowth.className = 'badge ' + (g === null ? 'bg-lt-secondary' : (g >= 0 ? 'bg-lt-green' : 'bg-lt-red'));
        const progress = Math.max(0, Math.min(100, g === null ? 0 : Math.abs(g)));
        els.incomeProgress.style.width = progress + '%';
        els.incomeProgress.setAttribute('aria-valuenow', progress);

        els.paidCount.textContent = k.paidCount;
        els.unpaidCount.textContent = k.unpaidCount;
        els.unpaidTotal.textContent = rupiah(k.unpaidTotal);
        els.activeCustomers.textContent = k.activeCustomers;
      }

      function paintMeta(meta) {
        const start = new Date(meta.range.start);
        const end = new Date(meta.range.end);
        const fmt = (d) => d.toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'short',
          year: 'numeric'
        });
        els.periodRange.textContent = `${fmt(start)} - ${fmt(end)}`;
      }

      async function loadData() {
        const isAnnual = els.periodAnnual.checked;
        const type = isAnnual ? 'annual' : 'monthly';
        const period = isAnnual ? els.yearPicker.value : els.monthPicker.value;
        const url = new URL("{{ route('e-billing.dashboard.metrics') }}", window.location.origin);
        url.searchParams.set('type', type);
        url.searchParams.set('period', period);

        try {
          const res = await fetch(url, {
            headers: {
              'Accept': 'application/json'
            }
          });
          if (!res.ok) throw new Error('Gagal memuat data');
          const data = await res.json();
          paintKpis(data.kpis);
          paintMeta({
            range: data.range
          });
          renderCharts(data);
        } catch (e) {
          console.error(e);
          alert('Gagal memuat data dashboard');
        }
      }

      const debounce = (func, delay) => {
        let timeout;
        return (...args) => {
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(this, args), delay);
        };
      };

      els.monthPicker.addEventListener('change', debounce(loadData, 300));
      els.yearPicker.addEventListener('change', debounce(loadData, 300));
      els.periodAnnual.addEventListener('change', debounce(loadData, 300));
      els.periodMonthly.addEventListener('change', debounce(loadData, 300));

      loadData();
    </script>
  @endpush

</x-e-billing::layouts.panel>
