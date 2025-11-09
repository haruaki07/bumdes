@props(['fundingRequest'])

<div class="card mb-3">
  <div class="card-header">
    <h3 class="card-title">Riwayat Pembayaran Cicilan</h3>
  </div>
  @if ($fundingRequest->repayments->isEmpty())
    <div class="card-body">
      <div class="empty">
        <div class="empty-icon">
          <i class="ti ti-coins"></i>
        </div>
        <p class="empty-title">Belum ada pembayaran cicilan</p>
        <p class="empty-subtitle text-muted">
          Pembayaran cicilan akan muncul di sini setelah warga melakukan pembayaran
        </p>
      </div>
    </div>
  @else
    <div class="card-body">
      @php
        $progress =
            $fundingRequest->disbursed_amount > 0
                ? ($fundingRequest->getTotalRepaidAttribute() / $fundingRequest->disbursed_amount) * 100
                : 0;
      @endphp
      <div class="mb-2 d-flex justify-content-between">
        <span class="text-muted">Progress Pembayaran</span>
        <span class="fw-bold">{{ number_format($progress, 1) }}%</span>
      </div>
      <div class="progress progress-sm">
        <div class="progress-bar bg-{{ $progress >= 100 ? 'success' : 'primary' }}" style="width: {{ $progress }}%"
          role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter card-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th class="text-end">Jumlah</th>
            <th class="text-center">Metode</th>
            <th class="text-center">Bukti</th>
            <th>Status</th>
            <th>Catatan</th>
            <th class="w-1">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($fundingRequest->repayments as $repayment)
            <tr>
              <td>{{ $repayment->payment_date->format('d M Y') }}</td>
              <td class="text-end text-orange fw-bold">
                Rp {{ number_format($repayment->amount, 0, ',', '.') }}
              </td>
              <td class="text-center">
                <x-common.badge color="azure" light :label="ucfirst($repayment->payment_method)" />
              </td>
              <td class="text-center">
                @if ($repayment->proof_document)
                  <a href="{{ Storage::url($repayment->proof_document) }}" target="_blank" class="btn btn-sm btn-link">
                    <i class="icon ti ti-xs ti-file"></i> Lihat
                  </a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if ($repayment->isVerified())
                  <x-common.badge color="success" label="Terverifikasi" />
                @else
                  <x-common.badge color="warning" label="Menunggu Verifikasi" />
                @endif
              </td>
              <td>
                <span class="text-muted">{{ $repayment->notes ?: '-' }}</span>
              </td>
              <td>
                @if (!$repayment->isVerified())
                  @can('verifyRepayment', $fundingRequest)
                    <form action="{{ route('repayments.verify', $repayment) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success"
                        onclick="return confirm('Verifikasi pembayaran ini?')">
                        <i class="icon ti ti-sm ti-check"></i> Verifikasi
                      </button>
                    </form>
                  @endcan
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          @php
            $hasRemaining = $fundingRequest->getRemainingAmountAttribute() > 0;
          @endphp
          <tr class="fw-bold">
            <td>Total Terbayar</td>
            <td class="text-end">
              <span class="text-{{ $hasRemaining ? 'danger' : 'success' }}">
                Rp {{ number_format($fundingRequest->getTotalRepaidAttribute(), 0, ',', '.') }}
              </span>
            </td>
            <td colspan="5"></td>
          </tr>
          <tr class="fw-bold">
            <td>Sisa Pembayaran</td>
            <td class="text-end">
              <span class="text-{{ $hasRemaining ? 'danger' : 'success' }}">
                Rp {{ number_format($fundingRequest->getRemainingAmountAttribute(), 0, ',', '.') }}
              </span>
            </td>
            <td colspan="5"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  @endif
</div>
