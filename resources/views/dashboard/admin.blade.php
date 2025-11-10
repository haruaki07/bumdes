@extends('tablar::page')

@section('content')
  <!-- Page header -->
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <!-- Page pre-title -->
          <div class="page-pretitle">
            Monitoring
          </div>
          <h2 class="page-title">
            Dashboard BUMDes
          </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-12 col-md-auto ms-auto d-print-none">
          <div class="btn-list">
            <div class="btn-group" role="group">
              <input type="radio" class="btn-check" name="periodType" id="periodMonthly" value="monthly"
                autocomplete="off" checked>
              <label class="btn" for="periodMonthly">Bulanan</label>

              <input type="radio" class="btn-check" name="periodType" id="periodYearly" value="yearly"
                autocomplete="off">
              <label class="btn" for="periodYearly">Tahunan</label>
            </div>
            <div style="width:165px">
              <input type="month" class="form-control" id="monthPicker" value="{{ now()->format('Y-m') }}">
              <input type="number" class="form-control d-none" id="yearPicker" min="2020" max="2100"
                value="{{ now()->format('Y') }}">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-deck row-cards">
        <!-- Period Info -->
        <div class="col-12">
          <div class="card bg-blue-lt">
            <div class="card-body py-2">
              <div class="d-flex align-items-center">
                <div class="subheader">Periode:</div>
                <div class="ms-2 fw-bold" id="periodLabel">Loading...</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Metric Cards -->
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-success text-white avatar">
                    <i class="ti ti-building-store"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    <span id="activeBusinessesTotal">0</span> Usaha Aktif
                  </div>
                  <div class="text-secondary">
                    +<span id="activeBusinessesNew">0</span> baru periode ini
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-primary text-white avatar">
                    <i class="ti ti-cash"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    <span id="ongoingFundingCount">0</span> Pendanaan Berjalan
                  </div>
                  <div class="text-secondary">
                    <span id="ongoingFundingAmount">Rp0</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-indigo text-white avatar">
                    <i class="ti ti-chart-line"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    Total Dicairkan
                  </div>
                  <div class="text-secondary">
                    <span id="totalDisbursed">Rp0</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-teal text-white avatar">
                    <i class="ti ti-receipt"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    Total Terbayar
                  </div>
                  <div class="text-secondary">
                    <span id="totalRepaid">Rp0</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Cards Row 2 -->
        <div class="col-sm-6 col-lg-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Sisa Pinjaman</div>
                <div class="ms-auto lh-1">
                  <span id="outstandingBadge" class="badge bg-warning text-warning-fg">0%</span>
                </div>
              </div>
              <div class="h2 mb-2" id="outstanding">Rp0</div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-warning" id="outstandingProgress" style="width: 0%" role="progressbar">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Tingkat Pembayaran</div>
                <div class="ms-auto lh-1">
                  <span id="repaymentRateBadge" class="badge bg-success text-white">0%</span>
                </div>
              </div>
              <div class="h2 mb-2" id="repaymentRate">0%</div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-success" id="repaymentProgress" style="width: 0%" role="progressbar">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Pendanaan Selesai</div>
              </div>
              <div class="d-flex align-items-baseline">
                <div class="h2 mb-0 me-2" id="completedCount">0</div>
                <div class="text-secondary">pendanaan lunas</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pertumbuhan Usaha</h3>
              <div class="card-actions">
                <div class="text-secondary">
                  Jumlah usaha baru per hari
                </div>
              </div>
            </div>
            <div class="card-body">
              <div id="chartBusinessGrowth" style="height: 280px;"></div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Usaha per Jenis</h3>
            </div>
            <div class="card-body">
              <div id="chartBusinessByType" style="height: 280px;"></div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Status Pendanaan</h3>
              <div class="card-actions">
                <div class="text-secondary">
                  Distribusi berdasarkan status
                </div>
              </div>
            </div>
            <div class="card-body">
              <div id="chartFundingDistribution" style="height: 280px;"></div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Kondisi Cicilan</h3>
              <div class="card-actions">
                <div class="text-secondary">
                  Status pembayaran cicilan
                </div>
              </div>
            </div>
            <div class="card-body">
              <div id="chartRepaymentStatus" style="height: 280px;"></div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Tren Nominal Pendanaan</h3>
              <div class="card-actions">
                <div class="text-secondary">
                  Total pengajuan pendanaan per hari
                </div>
              </div>
            </div>
            <div class="card-body">
              <div id="chartFundingAmountTrend" style="height: 280px;"></div>
            </div>
          </div>
        </div>

        <!-- Recent Data Tables -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Usaha Terbaru
              </h3>
            </div>
            <div class="table-responsive">
              <table class="table table-vcenter card-table table-hover" id="recentBusinessesTable">
                <thead>
                  <tr>
                    <th>Usaha & Jenis</th>
                    <th class="text-center">Status</th>
                    <th>Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="3" class="text-center text-muted py-5">
                      <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                      Memuat data...
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Pendanaan Terbaru
              </h3>
            </div>
            <div class="table-responsive">
              <table class="table table-vcenter card-table table-hover" id="recentFundingTable">
                <thead>
                  <tr>
                    <th>Usaha & Nominal</th>
                    <th class="text-center">Status</th>
                    <th>Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="3" class="text-center text-muted py-5">
                      <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                      Memuat data...
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    let charts = {
      businessGrowth: null,
      businessByType: null,
      fundingDistribution: null,
      repaymentStatus: null,
      fundingAmountTrend: null
    };

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadDashboardData();

      // Period type change
      document.querySelectorAll('input[name="periodType"]').forEach(radio => {
        radio.addEventListener('change', function() {
          if (this.value === 'monthly') {
            document.getElementById('monthPicker').classList.remove('d-none');
            document.getElementById('yearPicker').classList.add('d-none');
          } else {
            document.getElementById('monthPicker').classList.add('d-none');
            document.getElementById('yearPicker').classList.remove('d-none');
          }
          loadDashboardData();
        });
      });

      // Date change
      document.getElementById('monthPicker').addEventListener('change', loadDashboardData);
      document.getElementById('yearPicker').addEventListener('change', loadDashboardData);
    });

    function loadDashboardData() {
      const periodType = document.querySelector('input[name="periodType"]:checked').value;
      const date = periodType === 'monthly' ?
        document.getElementById('monthPicker').value :
        document.getElementById('yearPicker').value;

      fetch(`{{ route('dashboard.metrics') }}?period=${periodType}&date=${date}`)
        .then(response => response.json())
        .then(data => {
          updateMetrics(data);
          updateCharts(data.charts);
          updateTables(data.recent_data);
        })
        .catch(error => {
          console.error('Error loading dashboard data:', error);
        });
    }

    function updateMetrics(data) {
      // Period info
      document.getElementById('periodLabel').textContent = data.period_info.label;

      // Active businesses
      document.getElementById('activeBusinessesTotal').textContent = data.active_businesses.total;
      document.getElementById('activeBusinessesNew').textContent = data.active_businesses.new_this_period;

      // Ongoing funding
      document.getElementById('ongoingFundingCount').textContent = data.ongoing_funding.ongoing_count;
      document.getElementById('ongoingFundingAmount').textContent = formatRupiah(data.ongoing_funding
        .total_amount);

      // Funding summary
      document.getElementById('totalDisbursed').textContent = formatRupiah(data.funding_summary
        .total_disbursed);
      document.getElementById('totalRepaid').textContent = formatRupiah(data.funding_summary.total_repaid);
      document.getElementById('outstanding').textContent = formatRupiah(data.funding_summary.outstanding);
      document.getElementById('completedCount').textContent = data.funding_summary.completed_count;

      // Repayment rate
      const repaymentRate = Math.round(data.funding_summary.repayment_rate);
      document.getElementById('repaymentRate').textContent = repaymentRate + '%';
      document.getElementById('repaymentProgress').style.width = repaymentRate + '%';
      document.getElementById('repaymentRateBadge').textContent = repaymentRate + '%';
      document.getElementById('repaymentRateBadge').className = 'badge text-white ' + (repaymentRate >= 80 ?
        'bg-success' :
        repaymentRate >= 50 ? 'bg-warning' : 'bg-danger');

      // Outstanding
      const outstandingPercent = data.funding_summary.total_disbursed > 0 ?
        Math.round((data.funding_summary.outstanding / data.funding_summary.total_disbursed) * 100) : 0;
      document.getElementById('outstandingProgress').style.width = outstandingPercent + '%';
      document.getElementById('outstandingBadge').textContent = outstandingPercent + '%';
    }

    function updateCharts(chartData) {
      // Business growth chart
      if (charts.businessGrowth) charts.businessGrowth.destroy();
      charts.businessGrowth = new ApexCharts(document.getElementById('chartBusinessGrowth'), {
        chart: {
          type: 'area',
          fontFamily: 'inherit',
          height: 280,
          toolbar: {
            show: false
          },
          animations: {
            enabled: true
          }
        },
        dataLabels: {
          enabled: false
        },
        series: [{
          name: 'Usaha Baru',
          data: chartData.business_growth.data
        }],
        xaxis: {
          categories: chartData.business_growth.labels,
          type: 'datetime',
          labels: {
            format: 'dd MMM'
          }
        },
        fill: {
          opacity: 0.2,
          type: 'gradient',
          gradient: {
            shade: 'light',
            type: 'vertical',
            opacityFrom: 0.4,
            opacityTo: 0.1,
          }
        },
        stroke: {
          curve: 'smooth',
          width: 3
        },
        colors: ['#16a34a'],
        grid: {
          strokeDashArray: 4
        },
        tooltip: {
          theme: 'dark'
        }
      });
      charts.businessGrowth.render();

      // Business by type chart
      if (charts.businessByType) charts.businessByType.destroy();
      charts.businessByType = new ApexCharts(document.getElementById('chartBusinessByType'), {
        chart: {
          type: 'donut',
          fontFamily: 'inherit',
          height: 280
        },
        series: chartData.business_by_type.data,
        labels: chartData.business_by_type.labels,
        colors: [
          "color-mix(in srgb, transparent, var(--tblr-red) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-orange) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-yellow) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-green) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-teal) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-cyan) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-blue) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-primary) 100%)", // biru (tema)
          "color-mix(in srgb, transparent, var(--tblr-azure) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-indigo) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-purple) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-pink) 100%)",

        ],
        plotOptions: {
          pie: {
            donut: {
              size: '50%'
            }
          }
        },
        legend: {
          position: 'bottom',
          fontSize: '12px'
        },
        tooltip: {
          theme: 'dark',
          fillSeriesColor: false,
        },
        dataLabels: {
          dropShadow: {
            enabled: false
          }
        }
      });
      charts.businessByType.render();

      // Funding distribution chart
      if (charts.fundingDistribution) charts.fundingDistribution.destroy();
      charts.fundingDistribution = new ApexCharts(document.getElementById('chartFundingDistribution'), {
        chart: {
          type: 'bar',
          fontFamily: 'inherit',
          height: 280,
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            borderRadius: 6,
            horizontal: false,
            columnWidth: '40%',
            dataLabels: {
              position: 'top'
            }
          }
        },
        dataLabels: {
          enabled: true,
          offsetY: 20,
        },
        series: [{
          name: 'Jumlah',
          data: chartData.funding_distribution.data
        }],
        xaxis: {
          categories: chartData.funding_distribution.labels,
          labels: {
            rotate: -45,
            style: {
              fontSize: '11px'
            }
          }
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              return Math.round(val);
            }
          }
        },
        colors: ['#206bc4'],
        grid: {
          strokeDashArray: 4
        },
        tooltip: {
          theme: 'dark'
        }
      });
      charts.fundingDistribution.render();

      // Repayment status chart
      if (charts.repaymentStatus) charts.repaymentStatus.destroy();
      charts.repaymentStatus = new ApexCharts(document.getElementById('chartRepaymentStatus'), {
        chart: {
          type: 'pie',
          fontFamily: 'inherit',
          height: 280
        },
        series: chartData.repayment_status.data,
        labels: chartData.repayment_status.labels,
        colors: [
          "color-mix(in srgb, transparent, var(--tblr-primary) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-warning) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-success) 100%)",
        ],
        legend: {
          position: 'bottom',
          fontSize: '13px'
        },
        plotOptions: {
          pie: {
            dataLabels: {
              offset: -25,
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function(val, opts) {
            return opts.w.config.series[opts.seriesIndex]
          },
          dropShadow: {
            enabled: false
          }
        },
        tooltip: {
          theme: 'dark',
          fillSeriesColor: false,
        }
      });
      charts.repaymentStatus.render();

      // Funding amount trend chart
      if (charts.fundingAmountTrend) charts.fundingAmountTrend.destroy();
      charts.fundingAmountTrend = new ApexCharts(document.getElementById('chartFundingAmountTrend'), {
        chart: {
          type: 'area',
          fontFamily: 'inherit',
          height: 280,
          toolbar: {
            show: false
          },
          sparkline: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        series: [{
          name: 'Nominal Pendanaan',
          data: chartData.funding_amount_trend.data
        }],
        xaxis: {
          categories: chartData.funding_amount_trend.labels,
          type: 'datetime',
          labels: {
            format: 'dd MMM'
          }
        },
        fill: {
          opacity: 0.2,
          type: 'gradient',
          gradient: {
            shade: 'light',
            type: 'vertical',
            opacityFrom: 0.5,
            opacityTo: 0.1,
          }
        },
        stroke: {
          curve: 'smooth',
          width: 3
        },
        colors: ['#206bc4'],
        grid: {
          strokeDashArray: 4
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              return 'Rp' + (val / 1000000).toFixed(0) + 'jt';
            }
          }
        },
        tooltip: {
          theme: 'dark',
          y: {
            formatter: function(val) {
              return formatRupiah(val);
            }
          }
        }
      });
      charts.fundingAmountTrend.render();
    }

    function updateTables(recentData) {
      // Update recent businesses table
      const businessesTable = document.querySelector('#recentBusinessesTable tbody');
      if (recentData.businesses && recentData.businesses.length > 0) {
        businessesTable.innerHTML = recentData.businesses.map(business => `
          <tr>
            <td>
              <a href="${business.url}" class="text-reset fw-bold">${business.name}</a>
              <div class="text-muted small">${business.type}</div>
            </td>
            <td class="text-center">
              <span class="badge bg-${getStatusColor(business.status)}-lt text-${getStatusColor(business.status)}">
                ${business.status_label}
              </span>
            </td>
            <td class="text-muted">
              ${business.created_at}
            </td>
          </tr>
        `).join('');
      } else {
        businessesTable.innerHTML =
          '<tr><td colspan="3" class="text-center text-muted py-5"><i class="ti ti-inbox fs-1 mb-2 d-block"></i>Tidak ada data usaha</td></tr>';
      }

      // Update recent funding table
      const fundingTable = document.querySelector('#recentFundingTable tbody');
      if (recentData.funding && recentData.funding.length > 0) {
        fundingTable.innerHTML = recentData.funding.map(funding => `
          <tr>
            <td>
              <a href="${funding.url}" class="text-reset fw-bold">${funding.business_name}</a>
              <div class="text-muted small">${funding.amount_formatted}</div>
            </td>
            <td class="text-center">
              <span class="badge bg-${getFundingStatusColor(funding.status)}-lt text-${getFundingStatusColor(funding.status)}">
                ${funding.status_label}
              </span>
            </td>
            <td class="text-muted">
              ${funding.created_at}
            </td>
          </tr>
        `).join('');
      } else {
        fundingTable.innerHTML =
          '<tr><td colspan="3" class="text-center text-muted py-5"><i class="ti ti-inbox fs-1 mb-2 d-block"></i>Tidak ada data pendanaan</td></tr>';
      }
    }

    function getStatusColor(status) {
      const colors = {
        'active': 'success',
        'inactive': 'secondary',
        'pending': 'warning',
        'rejected': 'danger'
      };
      return colors[status] || 'secondary';
    }

    function getFundingStatusColor(status) {
      const colors = {
        'submitted': 'info',
        'approved': 'primary',
        'mou_signed': 'azure',
        'ready_to_disburse': 'purple',
        'disbursed': 'indigo',
        'repaying': 'warning',
        'completed': 'success',
        'rejected': 'danger',
        'cancelled': 'secondary'
      };
      return colors[status] || 'secondary';
    }
  </script>
@endpush
