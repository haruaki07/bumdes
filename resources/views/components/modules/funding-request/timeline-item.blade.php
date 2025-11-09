@props(['timeline'])

@php
  use App\Enums\FundingRequestTimelineAction;
  $action = is_string($timeline->action)
      ? FundingRequestTimelineAction::fromString($timeline->action)
      : $timeline->action;
@endphp

<div class="timeline-item">
  <div class="timeline-badge bg-{{ $action->color() }}">
    <i class="{{ $action->icon() }}"></i>
  </div>
  <div class="timeline-content">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h4 class="mb-1">{{ $action->label() }}</h4>
        <p class="text-muted mb-2">{{ $timeline->description }}</p>
        @if ($timeline->metadata)
          <div class="small text-muted">
            @if (isset($timeline->metadata['notes']) && $timeline->metadata['notes'])
              <div class="mt-1">
                <strong>Catatan:</strong> {{ $timeline->metadata['notes'] }}
              </div>
            @endif
            @if (isset($timeline->metadata['rejection_reason']))
              <div class="mt-1">
                <strong>Alasan Penolakan:</strong> {{ $timeline->metadata['rejection_reason'] }}
              </div>
            @endif
            @if (isset($timeline->metadata['amount']))
              <div class="mt-1">
                <strong>Jumlah:</strong> Rp {{ number_format($timeline->metadata['amount'], 0, ',', '.') }}
              </div>
            @endif
            @if (isset($timeline->metadata['interest_rate']))
              <div class="mt-1">
                <strong>Bunga:</strong> {{ $timeline->metadata['interest_rate'] }}% |
                <strong>Durasi:</strong> {{ $timeline->metadata['repayment_duration_months'] }} bulan
              </div>
            @endif
          </div>
        @endif
      </div>
      <span class="badge bg-{{ $action->color() }}-lt">{{ $timeline->created_at->diffForHumans() }}</span>
    </div>
    <div class="small text-muted mt-2">
      <i class="icon ti ti-sm ti-user"></i> {{ $timeline->performer->name }}
      <span class="mx-1">•</span>
      <i class="icon ti ti-sm ti-clock"></i> {{ $timeline->created_at->format('d M Y H:i') }}
    </div>
  </div>
</div>

<style>
  .timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 2rem;
  }

  .timeline-item:last-child {
    padding-bottom: 0;
  }

  .timeline-item::before {
    content: '';
    position: absolute;
    left: 0.875rem;
    top: 2.5rem;
    bottom: 0;
    width: 2px;
    background: var(--tblr-border-color);
  }

  .timeline-item:last-child::before {
    display: none;
  }

  .timeline-badge {
    position: absolute;
    left: 0;
    top: 0;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    box-shadow: 0 0 0 4px var(--tblr-body-bg);
  }

  .timeline-content {
    background: var(--tblr-card-bg);
    border: 1px solid var(--tblr-border-color);
    border-radius: var(--tblr-border-radius);
    padding: 1rem;
  }
</style>
