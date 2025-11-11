<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Laba Rugi</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #333;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 2px solid #333;
    }

    .header h1 {
      font-size: 18px;
      margin-bottom: 5px;
    }

    .header h2 {
      font-size: 16px;
      font-weight: normal;
      margin-bottom: 5px;
    }

    .header .period {
      font-size: 12px;
      color: #666;
    }

    .summary-box {
      background-color: #f5f5f5;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    .summary-box table {
      width: 100%;
    }

    .summary-box td {
      padding: 5px 10px;
    }

    .summary-box .label {
      font-weight: bold;
    }

    .summary-box .value {
      text-align: right;
      font-size: 14px;
    }

    .section {
      margin-bottom: 25px;
    }

    .section-title {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 10px;
      padding-bottom: 5px;
      border-bottom: 1px solid #999;
    }

    .section-title.income {
      color: #28a745;
    }

    .section-title.expense {
      color: #dc3545;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table td {
      padding: 6px 10px;
    }

    .category-row td {
      font-weight: bold;
      background-color: #f9f9f9;
    }

    .subcategory-row td:first-child {
      padding-left: 30px;
      font-size: 11px;
    }

    .detail-row td {
      padding-left: 40px;
      font-size: 10px;
      color: #666;
    }

    .amount {
      text-align: right;
      white-space: nowrap;
    }

    .total-row {
      border-top: 2px solid #333;
      font-weight: bold;
      font-size: 13px;
    }

    .total-row td {
      padding-top: 10px;
      padding-bottom: 10px;
    }

    .net-profit {
      margin-top: 20px;
      padding: 15px;
      background-color: #e7f5ff;
      border: 2px solid #0066cc;
    }

    .net-profit.loss {
      background-color: #ffe7e7;
      border-color: #cc0000;
    }

    .net-profit table {
      width: 100%;
    }

    .net-profit td {
      font-size: 16px;
      font-weight: bold;
      padding: 5px 10px;
    }

    .net-profit .value {
      text-align: right;
      color: #0066cc;
    }

    .net-profit.loss .value {
      color: #cc0000;
    }

    .footer {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #ccc;
      font-size: 10px;
      color: #666;
      text-align: center;
    }

    .no-data {
      text-align: center;
      color: #999;
      font-style: italic;
      padding: 10px;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <div class="header">
    <h1>{{ config('app.name') }}</h1>
    <h2>LAPORAN LABA RUGI</h2>
    <div class="period">
      Periode: {{ $data['period']['start_formatted'] }} s/d {{ $data['period']['end_formatted'] }}
    </div>
  </div>

  <!-- Summary Box -->
  <div class="summary-box">
    <table>
      <tr>
        <td class="label">Total Pendapatan:</td>
        <td class="value">{{ $data['income']['total_formatted'] }}</td>
      </tr>
      <tr>
        <td class="label">Total Beban:</td>
        <td class="value">{{ $data['expense']['total_formatted'] }}</td>
      </tr>
      <tr style="border-top: 1px solid #999;">
        <td class="label">{{ $data['net_profit']['is_profit'] ? 'Laba Bersih' : 'Rugi Bersih' }}:</td>
        <td class="value" style="color: {{ $data['net_profit']['is_profit'] ? '#28a745' : '#dc3545' }};">
          {{ $data['net_profit']['amount_formatted'] }}
        </td>
      </tr>
    </table>
  </div>

  <!-- Income Section -->
  <div class="section">
    <div class="section-title income">PENDAPATAN</div>
    <table>
      @if (count($data['income']['categories']) > 0 || $data['income']['interest']['total'] > 0)
        <!-- Regular Income Categories -->
        @foreach ($data['income']['categories'] as $category)
          <tr class="category-row">
            <td>{{ $category['name'] }}</td>
            <td class="amount">{{ $category['amount_formatted'] }}</td>
          </tr>

          <!-- Subcategories -->
          @if (isset($category['subcategories']) && count($category['subcategories']) > 0)
            @foreach ($category['subcategories'] as $sub)
              <tr class="subcategory-row">
                <td>{{ $sub['name'] }}</td>
                <td class="amount">{{ $sub['amount_formatted'] }}</td>
              </tr>
            @endforeach
          @endif
        @endforeach

        <!-- Interest Income -->
        @if ($data['income']['interest']['total'] > 0)
          <tr class="category-row">
            <td>Pendapatan Bunga</td>
            <td class="amount">{{ $data['income']['interest']['total_formatted'] }}</td>
          </tr>

          @if (isset($data['income']['interest']['details']) && count($data['income']['interest']['details']) > 0)
            @foreach ($data['income']['interest']['details'] as $detail)
              <tr class="detail-row">
                <td>{{ $detail['business_name'] }} ({{ $detail['interest_rate'] }}% × {{ $detail['days'] }} hari)
                </td>
                <td class="amount">{{ $detail['interest_formatted'] }}</td>
              </tr>
            @endforeach
          @endif
        @endif

        <!-- Total Income -->
        <tr class="total-row">
          <td>TOTAL PENDAPATAN</td>
          <td class="amount">{{ $data['income']['total_formatted'] }}</td>
        </tr>
      @else
        <tr>
          <td colspan="2" class="no-data">Tidak ada data pendapatan</td>
        </tr>
      @endif
    </table>
  </div>

  <!-- Expense Section -->
  <div class="section">
    <div class="section-title expense">BEBAN</div>
    <table>
      @if (count($data['expense']['categories']) > 0)
        @foreach ($data['expense']['categories'] as $category)
          <tr class="category-row">
            <td>{{ $category['name'] }}</td>
            <td class="amount">{{ $category['amount_formatted'] }}</td>
          </tr>

          <!-- Subcategories -->
          @if (isset($category['subcategories']) && count($category['subcategories']) > 0)
            @foreach ($category['subcategories'] as $sub)
              <tr class="subcategory-row">
                <td>{{ $sub['name'] }}</td>
                <td class="amount">{{ $sub['amount_formatted'] }}</td>
              </tr>
            @endforeach
          @endif
        @endforeach

        <!-- Total Expense -->
        <tr class="total-row">
          <td>TOTAL BEBAN</td>
          <td class="amount">{{ $data['expense']['total_formatted'] }}</td>
        </tr>
      @else
        <tr>
          <td colspan="2" class="no-data">Tidak ada data beban</td>
        </tr>
      @endif
    </table>
  </div>

  <!-- Net Profit/Loss -->
  <div class="net-profit {{ $data['net_profit']['is_profit'] ? '' : 'loss' }}">
    <table>
      <tr>
        <td>{{ $data['net_profit']['is_profit'] ? 'LABA BERSIH' : 'RUGI BERSIH' }}</td>
        <td class="value">{{ $data['net_profit']['amount_formatted'] }}</td>
      </tr>
    </table>
  </div>

  <!-- Footer -->
  <div class="footer">
    <p>Laporan ini dibuat secara otomatis pada {{ $generatedAt }}</p>
    <p>© {{ date('Y') }} {{ config('app.name') }}</p>
  </div>
</body>

</html>
