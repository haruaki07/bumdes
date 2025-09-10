<!doctype html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Pengingat Tagihan {{ $invoice->invoice_number }}</title>
</head>

<body>
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
      <td>&nbsp;</td>
      <td class="container">
        <div class="content">

          <!-- START CENTERED WHITE CONTAINER -->
          <span class="preheader">Halo {{ $customer->name }}, ini adalah pengingat bahwa tagihan Anda dengan nomor
            {{ $invoice->invoice_number }} sudah jatuh tempo.</span>
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main">

            <!-- START MAIN CONTENT AREA -->
            <tr>
              <td class="wrapper">
                <h2>Pengingat Tagihan</h2>
                <p>Halo {{ $customer->name }},</p>
                <p>
                  Ini adalah pengingat bahwa tagihan Anda dengan nomor <strong>{{ $invoice->invoice_number }}</strong>
                  sudah jatuh tempo.
                </p>

                <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom: 10px;">
                  <tbody>
                    <tr>
                      <td width="165">Paket</td>
                      <td><strong>{{ $package?->name ?? data_get($invoice->package_detail, 'name') }}</strong></td>
                    </tr>
                    <tr>
                      <td width="165">Jumlah</td>
                      <td class="amount">Rp{{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <td width="165">Bulan Pemakaian</td>
                      <td>{{ $invoice->period_start_date->format('M Y') }}</td>
                    </tr>
                    <tr>
                      <td width="165">Tanggal Jatuh Tempo</td>
                      <td>{{ $invoice->due_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                      <td width="165">Akhir Masa Tenggang</td>
                      <td>{{ $invoice->grace_period_end_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                      <td width="165">Status</td>
                      <td>{{ $invoice->status->label() }}</td>
                    </tr>
                  </tbody>
                </table>

                <p>Anda dapat melihat dan membayar tagihan dengan menekan tombol di bawah ini:</p>

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn">
                  <tbody>
                    <tr>
                      <td align="left">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr>
                              <td> <a href="{{ $invoice->public_url }}" target="_blank">Lihat Tagihan</a> </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <p>Jika Anda sudah melakukan pembayaran, abaikan email ini.</p>

                <p>Terima kasih atas kepercayaan Anda,<br>{{ $settings->name }}</p>
              </td>
            </tr>

            <!-- END MAIN CONTENT AREA -->
          </table>

          <!-- START FOOTER -->
          <div class="footer">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="content-block">
                  <span class="apple-link">Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</span>
                </td>
              </tr>
            </table>
          </div>

          <!-- END FOOTER -->

          <!-- END CENTERED WHITE CONTAINER -->
        </div>
      </td>
      <td>&nbsp;</td>
    </tr>
  </table>
</body>

</html>
