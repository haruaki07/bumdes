<div class="col-12 col-md-3 border-end">
  <div class="card-body py-0">
    <div class="list-group list-group-transparent">
      @foreach (Modules\EBilling\Enums\SettingsGroup::cases() as $group)
        @if (auth('ebil')->user()->can('read-system-settings') || $group === Modules\EBilling\Enums\SettingsGroup::ACCOUNT)
          <a href="{{ route('e-billing.settings.show', ['group' => $group->value]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center @if (request()->routeIs('e-billing.settings.show') && request()->route('group') === $group->value) active @endif">{{ $group->label() }}</a>
        @endif
      @endforeach
    </div>
  </div>
</div>
