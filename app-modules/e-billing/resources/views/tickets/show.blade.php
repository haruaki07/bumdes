<x-e-billing::layouts.panel>
  @push('css')
    <style>
      .dropzone-area {
        background: var(--tblr-bg-surface);
      }
    </style>
  @endpush

  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Layanan</div>
          <h2 class="page-title">Detail Tiket</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.tickets.index') }}" class="btn btn-secondary">Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-cards">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Percakapan</h3>
            </div>
            <div class="card-body scrollable">
              <div class="chat">
                <div class="chat-bubbles">
                  @forelse ($ticket->messages as $msg)
                    <div class="chat-item">
                      <div class="row align-items-end">
                        <div class="col">
                          <div class="chat-bubble {{ $msg->user->id === auth('ebil')->id() ? 'chat-bubble-me' : '' }}">
                            <div class="chat-bubble-title">
                              <div class="row">
                                <div class="col chat-bubble-author">
                                  {{ $msg->user?->name ?? ($msg->author_name ?? 'Pengguna') }}</div>
                                <div class="col-auto chat-bubble-date">
                                  {{ $msg->created_at->copy()->locale('id')->translatedFormat('d F Y \p\u\k\u\l H.i \W\I\B') }}
                                </div>
                              </div>
                            </div>
                            <div class="chat-bubble-body">{!! $msg->message !!}</div>
                            @if ($msg->hasMedia('attachments'))
                              <div class="mt-3 border-top pt-3">
                                <p class="mb-3 fw-bold">Lampiran</p>
                                <div class="row g-2">
                                  @foreach ($msg->getMedia('attachments') as $att)
                                    <div class="col-12">
                                      <a href="{{ $att->getUrl() }}" class="btn btn-sm btn-light" target="_blank"
                                        rel="noopener" download>
                                        <i class="ti ti-xs ti-paperclip icon"></i>
                                        {{ $att->filename }}.{{ $att->extension }} ({{ human_filesize($att->size) }})
                                      </a>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="text-muted">Belum ada pesan.</div>
                  @endforelse
                </div>
              </div>
            </div>
            <div class="card-footer">
              <form action="{{ route('e-billing.tickets.messages.store', $ticket) }}" name="replyForm" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <textarea id="messageInput" name="message" rows="3" class="form-control" placeholder="Tulis balasan...">{{ old('message') }}</textarea>
                  @error('message')
                    <div class="text-danger mb-3">{{ $message }}</div>
                  @enderror
                </div>
                <div class="mb-3">
                  <label class="form-label">Lampiran (jika ada)</label>
                  <input type="file" class="form-control form-dropzone" id="messageAttachment" name="attachments[]"
                    multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                </div>
                <button class="btn btn-primary">Kirim</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ringkasan</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Kode</div>
                  <div class="datagrid-content">{{ $ticket->code }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Subjek</div>
                  <div class="datagrid-content">{{ $ticket->subject }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Pelanggan</div>
                  <div class="datagrid-content">{{ $ticket->customer->name }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Prioritas</div>
                  <div class="datagrid-content"><x-common.badge :color="$ticket->priority->color()" :label="$ticket->priority->label()" /></div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content"><x-common.badge :color="$ticket->status->color()" :label="$ticket->status->label()" /></div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Dibuat</div>
                  <div class="datagrid-content">
                    <span data-bs-toggle="tooltip"
                      title="{{ $ticket->created_at->copy()->locale('id')->translatedFormat('d F Y \p\u\k\u\l H.i \W\I\B') }}"
                      data-bs-placement="top">
                      {{ $ticket->created_at->copy()->locale('id')->diffForHumans() }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h3 class="card-title">Ubah Status</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('e-billing.tickets.update', $ticket) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-12">
                  <label class="form-label">Prioritas</label>
                  <select class="form-select" name="priority" required>
                    @foreach (Modules\EBilling\Enums\TicketPriority::cases() as $p)
                      <option value="{{ $p->value }}" @selected($p === $ticket->priority)>{{ $p->label() }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Status</label>
                  <select class="form-select" name="status" required>
                    @foreach (Modules\EBilling\Enums\TicketStatus::cases() as $s)
                      <option value="{{ $s->value }}" @selected($s === $ticket->status)>{{ $s->label() }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Petugas</label>
                  <select class="form-select" name="assigned_to">
                    <option value="">-- Tidak ada --</option>
                    @foreach ($users as $u)
                      <option value="{{ $u->id }}" @selected($ticket->assigned_to === $u->id)>{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Subjek</label>
                  <input type="text" class="form-control" name="subject" required maxlength="255"
                    value="{{ $ticket->subject }}">
                </div>
                <div class="col-12">
                  <button class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('js')
    @vite(['resources/js/hugerte.js'])
    <script type="module">
      let options = {
        selector: '#messageInput',
        placeholder: 'Tulis balasan...',
        language: 'id',
        language_url: '{{ asset('assets/js/tinymce/langs/id.js') }}',
        height: 300,
        menubar: false,
        statusbar: false,
        plugins: 'lists autosave',
        skin_url: 'default',
        content_css: 'default',
        toolbar: 'undo redo | bold italic backcolor | alignleft aligncenter | alignright alignjustify | bullist numlist outdent indent | removeformat restoredraft',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }',
        setup: function(editor) {
          editor.on('change', function() {
            hugerte.triggerSave();
          });
        }
      }

      hugerte.init(options);

      new Dropzone("#messageAttachment", {
        maxFileSize: 5 * 1024 * 1024,
        multiple: true,
      });
    </script>
  @endpush
</x-e-billing::layouts.panel>
