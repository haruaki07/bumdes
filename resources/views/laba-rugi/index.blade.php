@extends('tablar::page')

@section('content')
  <!-- Page header -->
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Manajemen Keuangan
          </div>
          <h2 class="page-title">
            Laporan Laba Rugi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <button type="button" class="btn btn-primary" id="btnGenerate">
              <i class="icon ti ti-refresh"></i>
              Generate Laporan
            </button>
            <button type="button" class="btn btn-success" id="btnExportPdf" disabled data-bs-toggle="loading-button"
              data-bs-spinner-type="dots" data-bs-disabled-on-loading="true">
              <i class="icon ti ti-file-export"></i>
              Export PDF
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <!-- Date Range Filter -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row g-2 align-items-center">
                <div class="col-auto">
                  <label class="form-label mb-0">Periode:</label>
                </div>
                <div class="col-auto">
                  <input type="date" class="form-control" id="startDate" value="{{ $startDate }}">
                </div>
                <div class="col-auto">
                  <span>s/d</span>
                </div>
                <div class="col-auto">
                  <input type="date" class="form-control" id="endDate" value="{{ $endDate }}">
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-outline-primary" id="btnThisMonth">
                    <i class="icon ti ti-calendar-month"></i>
                    Bulan Ini
                  </button>
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-outline-primary" id="btnThisYear">
                    <i class="icon ti ti-calendar"></i>
                    Tahun Ini
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Indicator -->
      <div id="loadingIndicator" class="text-center py-5 d-none">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Memuat data...</div>
      </div>

      <!-- Report Content -->
      <div id="reportContent" class="d-none">
        <!-- Summary Cards -->
        <div class="row row-deck row-cards mb-3">
          <div class="col-md-4">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-success text-white avatar">
                      <i class="ti ti-trending-up"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">Total Pendapatan</div>
                    <div class="h2 mb-0" id="totalIncome">Rp 0</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-danger text-white avatar">
                      <i class="ti ti-trending-down"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">Total Beban</div>
                    <div class="h2 mb-0" id="totalExpense">Rp 0</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card card-sm" id="netProfitCard">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-primary text-white avatar" id="netProfitIcon">
                      <i class="ti ti-report-money"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">Laba/Rugi Bersih</div>
                    <div class="d-flex align-items-baseline">
                      <div class="h2 mb-0 me-2" id="netProfit">Rp 0</div>
                      <div class="d-inline-flex align-items-center lh-1" style="color: inherit;">
                        <span id="netProfitPercentage">0%</span>
                        <i id="netProfitTrendIcon" class="ti ti-trending-up ms-1 icon icon-2"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="row row-deck row-cards mb-3">
          <!-- Income vs Expense Chart -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Perbandingan Pendapatan & Beban</h3>
              </div>
              <div class="card-body">
                <div id="incomeExpenseChart" style="height: 250px;"></div>
              </div>
            </div>
          </div>

          <!-- Trend Chart -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Tren Periode Berjalan</h3>
              </div>
              <div class="card-body">
                <div id="trendChart" style="height: 250px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Category Breakdown Charts -->
        <div class="row row-deck row-cards mb-3">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Pendapatan per Kategori</h3>
              </div>
              <div class="card-body">
                <div id="incomeCategoryChart" style="height: 250px;"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Beban per Kategori</h3>
              </div>
              <div class="card-body">
                <div id="expenseCategoryChart" style="height: 250px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Laporan Laba Rugi</h3>
                <div class="ms-auto text-muted">
                  Periode: <span id="periodDisplay"></span>
                </div>
              </div>
              <div class="card-body">
                <!-- Income Section -->
                <div class="mb-4">
                  <h4 class="text-success">PENDAPATAN</h4>
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <tbody id="incomeTableBody">
                        <!-- Will be populated by JavaScript -->
                      </tbody>
                      <tfoot>
                        <tr class="fw-bold">
                          <td>TOTAL PENDAPATAN</td>
                          <td class="text-end text-success" id="incomeTotal">Rp 0</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                <!-- Expense Section -->
                <div class="mb-4">
                  <h4 class="text-danger">BEBAN</h4>
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <tbody id="expenseTableBody">
                        <!-- Will be populated by JavaScript -->
                      </tbody>
                      <tfoot>
                        <tr class="fw-bold">
                          <td>TOTAL BEBAN</td>
                          <td class="text-end text-danger" id="expenseTotal">Rp 0</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                <!-- Net Profit Section -->
                <div class="border-top pt-3">
                  <table class="table table-sm mb-0">
                    <tbody>
                      <tr class="fw-bold h3">
                        <td id="netProfitLabel">LABA BERSIH</td>
                        <td class="text-end" id="netProfitDisplay">Rp 0</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Data Message -->
      <div id="noDataMessage" class="d-none">
        <div class="empty">
          <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
              class="icon icon-tabler icons-tabler-outline icon-tabler-report-search">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
              <path d="M18 12v-5a2 2 0 0 0 -2 -2h-2" />
              <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
              <path d="M8 11h4" />
              <path d="M8 15h3" />
              <path d="M16.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0" />
              <path d="M18.5 19.5l2.5 2.5" />
            </svg>
          </div>
          <p class="empty-title">Belum ada data</p>
          <p class="empty-subtitle text-muted">
            Silakan pilih periode dan klik "Generate Laporan" untuk melihat data.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Export PDF Form (Hidden) -->
  <form id="exportPdfForm" action="{{ route('laba-rugi.export-pdf') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="start_date" id="exportStartDate">
    <input type="hidden" name="end_date" id="exportEndDate">
  </form>
@endsection

@push('js')
  <script type="module">
    let colors = {
      success: "color-mix(in srgb, transparent, var(--tblr-success) 100%)",
      danger: "color-mix(in srgb, transparent, var(--tblr-danger) 100%)",
    }
    let charts = {
      incomeExpense: null,
      trend: null,
      incomeCategory: null,
      expenseCategory: null
    };

    let currentReportData = null;

    document.addEventListener('DOMContentLoaded', function() {
      // Initialize with no data message
      document.getElementById('noDataMessage').classList.remove('d-none');

      // Set default dates to current month
      setCurrentMonth();

      // Button click handlers
      document.getElementById('btnGenerate').addEventListener('click', generateReport);
      document.getElementById('btnExportPdf').addEventListener('click', exportPdf);
      document.getElementById('btnThisMonth').addEventListener('click', setCurrentMonth);
      document.getElementById('btnThisYear').addEventListener('click', setCurrentYear);

      // Date change handler
      document.getElementById('startDate').addEventListener('change', function() {
        document.getElementById('btnExportPdf').disabled = true;
      });
      document.getElementById('endDate').addEventListener('change', function() {
        document.getElementById('btnExportPdf').disabled = true;
      });
    });

    function setCurrentMonth() {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const firstDay = `${year}-${month}-01`;
      const lastDay = new Date(year, now.getMonth() + 1, 0).getDate();
      const lastDayStr = `${year}-${month}-${String(lastDay).padStart(2, '0')}`;

      document.getElementById('startDate').value = firstDay;
      document.getElementById('endDate').value = lastDayStr;
    }

    function setCurrentYear() {
      const year = new Date().getFullYear();
      document.getElementById('startDate').value = `${year}-01-01`;
      document.getElementById('endDate').value = `${year}-12-31`;
    }

    function generateReport() {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;

      if (!startDate || !endDate) {
        alert('Harap pilih tanggal mulai dan tanggal akhir');
        return;
      }

      // Show loading
      document.getElementById('loadingIndicator').classList.remove('d-none');
      document.getElementById('reportContent').classList.add('d-none');
      document.getElementById('noDataMessage').classList.add('d-none');
      document.getElementById('btnGenerate').disabled = true;
      document.getElementById('btnExportPdf').disabled = true;

      fetch('{{ route('laba-rugi.generate') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            start_date: startDate,
            end_date: endDate
          })
        })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            currentReportData = response.data;
            displayReport(response.data, response.charts);
            document.getElementById('btnExportPdf').disabled = false;
          } else {
            alert('Gagal membuat laporan: ' + (response.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat membuat laporan');
        })
        .finally(() => {
          document.getElementById('loadingIndicator').classList.add('d-none');
          document.getElementById('btnGenerate').disabled = false;
        });
    }

    function displayReport(data, chartsData) {
      // Update summary cards
      document.getElementById('totalIncome').textContent = data.income.total_formatted;
      document.getElementById('totalExpense').textContent = data.expense.total_formatted;
      document.getElementById('netProfit').textContent = data.net_profit.amount_formatted;
      document.getElementById('netProfitPercentage').textContent = data.net_profit.percentage.toFixed(2) + '%';

      // Update net profit card styling
      const netProfitCard = document.getElementById('netProfitCard');
      const netProfitDiv = document.querySelector('#netProfit + div');
      const netProfitIcon = document.getElementById('netProfitIcon');
      const netProfitTrendIcon = document.getElementById('netProfitTrendIcon');

      if (data.net_profit.is_profit) {
        netProfitCard.classList.remove('border-danger');
        netProfitCard.classList.add('border-success');
        netProfitDiv.classList.remove('text-danger');
        netProfitDiv.classList.add('text-success');
        netProfitIcon.classList.remove('bg-danger');
        netProfitIcon.classList.add('bg-success');
        netProfitTrendIcon.classList.remove('ti-trending-down');
        netProfitTrendIcon.classList.add('ti-trending-up');
      } else {
        netProfitCard.classList.remove('border-success');
        netProfitCard.classList.add('border-danger');
        netProfitDiv.classList.remove('text-success');
        netProfitDiv.classList.add('text-danger');
        netProfitIcon.classList.remove('bg-success');
        netProfitIcon.classList.add('bg-danger');
        netProfitTrendIcon.classList.remove('ti-trending-up');
        netProfitTrendIcon.classList.add('ti-trending-down');
      }

      // Update period display
      document.getElementById('periodDisplay').textContent = data.period.start_formatted + ' - ' + data.period
        .end_formatted;

      // Populate income table
      populateIncomeTable(data.income);

      // Populate expense table
      populateExpenseTable(data.expense);

      // Update totals in detailed section
      document.getElementById('incomeTotal').textContent = data.income.total_formatted;
      document.getElementById('expenseTotal').textContent = data.expense.total_formatted;
      document.getElementById('netProfitDisplay').textContent = data.net_profit.amount_formatted;
      document.getElementById('netProfitLabel').textContent = data.net_profit.is_profit ? 'LABA BERSIH' : 'RUGI BERSIH';

      const netProfitDisplay = document.getElementById('netProfitDisplay');
      if (data.net_profit.is_profit) {
        netProfitDisplay.classList.add('text-success');
        netProfitDisplay.classList.remove('text-danger');
      } else {
        netProfitDisplay.classList.add('text-danger');
        netProfitDisplay.classList.remove('text-success');
      }

      // Render charts
      renderCharts(chartsData);

      // Show report content
      document.getElementById('reportContent').classList.remove('d-none');

      // Update export form
      document.getElementById('exportStartDate').value = document.getElementById('startDate').value;
      document.getElementById('exportEndDate').value = document.getElementById('endDate').value;
    }

    function populateIncomeTable(income) {
      let html = '';

      // Add regular income categories
      income.categories.forEach(category => {
        html += `<tr class="fw-bold">
          <td>${category.name}</td>
          <td class="text-end">${category.amount_formatted}</td>
        </tr>`;

        // Add subcategories
        if (category.subcategories && category.subcategories.length > 0) {
          category.subcategories.forEach(sub => {
            html += `<tr>
              <td class="ps-4">${sub.name}</td>
              <td class="text-end">${sub.amount_formatted}</td>
            </tr>`;
          });
        }
      });

      // Add interest income
      if (income.interest && income.interest.total > 0) {
        html += `<tr class="fw-bold">
          <td>Pendapatan Bunga</td>
          <td class="text-end">${income.interest.total_formatted}</td>
        </tr>`;

        // Add interest details
        if (income.interest.details && income.interest.details.length > 0) {
          income.interest.details.forEach(detail => {
            html += `<tr>
              <td class="ps-4 small text-muted">${detail.business_name} (${detail.interest_rate}% × ${detail.days} hari)</td>
              <td class="text-end small">${detail.interest_formatted}</td>
            </tr>`;
          });
        }
      }

      document.getElementById('incomeTableBody').innerHTML = html ||
        '<tr><td colspan="2" class="text-muted text-center">Tidak ada data</td></tr>';
    }

    function populateExpenseTable(expense) {
      let html = '';

      expense.categories.forEach(category => {
        html += `<tr class="fw-bold">
          <td>${category.name}</td>
          <td class="text-end">${category.amount_formatted}</td>
        </tr>`;

        // Add subcategories
        if (category.subcategories && category.subcategories.length > 0) {
          category.subcategories.forEach(sub => {
            html += `<tr>
              <td class="ps-4">${sub.name}</td>
              <td class="text-end">${sub.amount_formatted}</td>
            </tr>`;
          });
        }
      });

      document.getElementById('expenseTableBody').innerHTML = html ||
        '<tr><td colspan="2" class="text-muted text-center">Tidak ada data</td></tr>';
    }

    function renderCharts(chartsData) {
      // Destroy existing charts
      Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
      });

      // Income vs Expense Bar Chart
      charts.incomeExpense = new ApexCharts(document.getElementById('incomeExpenseChart'), {
        chart: {
          type: 'bar',
          fontFamily: 'inherit',
          height: 350
        },
        plotOptions: {
          bar: {
            distributed: true,
            borderRadius: 6,
            horizontal: false,
            columnWidth: '50%',
            dataLabels: {
              position: 'top'
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function(val) {
            return formatRupiah(val);
          },
          style: {
            fontSize: '12px'
          }
        },
        series: [{
          name: 'Jumlah',
          data: chartsData.income_expense.data
        }],
        xaxis: {
          categories: chartsData.income_expense.labels
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              return formatRupiah(val);
            }
          }
        },
        colors: [colors.success, colors.danger],
        grid: {
          strokeDashArray: 4
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
      charts.incomeExpense.render();

      // Trend Line Chart
      charts.trend = new ApexCharts(document.getElementById('trendChart'), {
        chart: {
          type: 'line',
          fontFamily: 'inherit',
          height: 350,
          animations: {
            enabled: true
          }
        },
        dataLabels: {
          enabled: false
        },
        series: chartsData.trend.datasets.map((dataset, index) => ({
          name: dataset.label,
          data: dataset.data
        })),
        xaxis: {
          categories: chartsData.trend.labels,
          tickAmount: 8
        },
        stroke: {
          curve: 'smooth',
          width: 3
        },
        colors: [colors.success, colors.danger],
        grid: {
          strokeDashArray: 4
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              return formatRupiah(val);
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
        },
        legend: {
          show: true,
        }
      });
      charts.trend.render();

      // Income Category Pie Chart
      if (chartsData.income_categories.data.length > 0) {
        charts.incomeCategory = new ApexCharts(document.getElementById('incomeCategoryChart'), {
          chart: {
            type: 'pie',
            fontFamily: 'inherit',
            height: 250,
            toolbar: {
              show: true,
              tools: {
                download: true
              }
            }
          },
          series: chartsData.income_categories.data,
          labels: chartsData.income_categories.labels,
          colors: generateColors(chartsData.income_categories.data.length, false),
          legend: {
            position: 'right',
            fontSize: '12px'
          },
          dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
              return formatRupiah(opts.w.config.series[opts.seriesIndex]);
            },
            style: {
              fontSize: '12px'
            }
          },
          tooltip: {
            theme: 'dark',
            fillSeriesColor: false,
            y: {
              formatter: function(val) {
                return formatRupiah(val);
              }
            }
          }
        });
        charts.incomeCategory.render();
      }

      // Expense Category Pie Chart
      if (chartsData.expense_categories.data.length > 0) {
        charts.expenseCategory = new ApexCharts(document.getElementById('expenseCategoryChart'), {
          chart: {
            type: 'pie',
            fontFamily: 'inherit',
            height: 250,
            toolbar: {
              show: true,
              tools: {
                download: true
              }
            }
          },
          series: chartsData.expense_categories.data,
          labels: chartsData.expense_categories.labels,
          colors: generateColors(chartsData.expense_categories.data.length, true),
          legend: {
            position: 'right',
            fontSize: '12px'
          },
          dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
              return formatRupiah(opts.w.config.series[opts.seriesIndex]);
            },
            style: {
              fontSize: '12px'
            }
          },
          tooltip: {
            theme: 'dark',
            fillSeriesColor: false,
            y: {
              formatter: function(val) {
                return formatRupiah(val);
              }
            }
          }
        });
        charts.expenseCategory.render();
      }
    }

    function generateColors(count, isExpense = false) {
      const colors = [];
      const baseHue = isExpense ? 0 : 131; // red or green
      for (let i = 0; i < count; i++) {
        const saturation = 70 - (i * 10 / count); // slightly less saturated for lighter tones
        const lightness = 45 + (i * 25 / count); // start mid, lighten up to ~70%
        colors.push(`hsla(${baseHue}, ${saturation}%, ${lightness}%, 1)`);
      }
      return colors;
    }


    function exportPdf() {
      document.getElementById('btnExportPdf')._loadingButtonInstance.start();
      if (!document.getElementById('exportStartDate').value || !document.getElementById('exportEndDate').value) {
        alert('Silakan generate laporan terlebih dahulu');
        return;
      }
      document.getElementById('exportPdfForm').submit();

      setTimeout(() => {
        document.getElementById('btnExportPdf')._loadingButtonInstance.stop();
      }, 1000);
    }
  </script>
@endpush
