@php
  $nowStr = now()->format('Y-m-d H:i');
@endphp

<div class="ann-card card border-0 shadow-sm">
  <div class="ann-card__header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <div class="ann-icon me-3">
        <i class="fas fa-bullhorn"></i>
      </div>
      <div>
        <div class="ann-title fw-700 mb-0">{{ __('Announcements') }}</div>
        <div class="ann-sub text-muted small">{{ __('Check the latest updates') }}</div>
      </div>
    </div>
    <div class="text-muted small d-none d-sm-flex align-items-center">
      <i class="far fa-clock me-1"></i>{{ $nowStr }}
    </div>
  </div>

  <div class="list-group list-group-flush">
    @forelse($rows as $a)
      @php
        $isNew = $a->created_at && $a->created_at->gte($newCutoff);
        $aid   = 'ann_'.$a->id;
      @endphp

      <div class="list-group-item ann-item" data-ann-id="{{ $aid }}">
        <div class="ann-rail"></div>

        <div class="d-flex">
          <div class="ann-avatar me-3 d-none d-sm-flex">
            <i class="fas fa-info"></i>
          </div>

          <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">

              @if($isNew)
                <span class="chip chip-new">{{ __('NEW') }}</span>
              @endif

              @if($a->show_from)
                <span class="chip chip-soft">
                  <i class="far fa-play-circle me-1"></i>{{ $a->show_from->format('Y-m-d H:i') }}
                </span>
              @endif

              @if($a->show_until)
                <span class="chip chip-soft">
                  <i class="far fa-stop-circle me-1"></i>{{ $a->show_until->format('Y-m-d H:i') }}
                </span>
              @endif

              <span class="chip chip-ghost ms-auto">
                <i class="far fa-calendar-alt me-1"></i>{{ $a->created_at?->format('Y-m-d H:i') }}
                @if($a->creator)
                  <span class="mx-1">•</span><i class="far fa-user me-1"></i>{{ $a->creator->name }}
                @endif
              </span>
            </div>

            {{-- Body (clamped) --}}
            <div class="ann-body clamp mb-2" id="{{ $aid }}_body">
              {{ $a->body }}
            </div>

            {{-- Actions --}}
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-ghost"
                      data-toggle="tooltip" title="{{ __('Copy') }}"
                      data-copy="#{{ $aid }}_body">
                <i class="far fa-copy"></i>
              </button>

              <button class="btn btn-ghost"
                      data-toggle="tooltip" title="{{ __('Expand / Collapse') }}"
                      data-expand="#{{ $aid }}_body">
                <i class="fas fa-chevron-down"></i>
              </button>

              <button class="btn btn-ghost ms-auto"
                      data-toggle="tooltip" title="{{ __('Dismiss') }}"
                      data-dismiss-ann="{{ $aid }}">
                <i class="far fa-eye-slash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="list-group-item text-center text-muted py-5">
        <i class="fas fa-inbox me-2"></i>{{ __('No announcements right now') }}
      </div>
    @endforelse
  </div>
</div>

@push('css')
<style>
  :root{
    --ann-accent: #6366f1;        /* indigo */
    --ann-accent-2: #8b5cf6;      /* violet */
    --ann-soft: #f8fafc;          /* slate-50 */
    --ann-rail: linear-gradient(180deg, var(--ann-accent), var(--ann-accent-2));
  }
  .ann-card__header{ padding:14px 16px; background:#fff; border-bottom:1px solid #f0f2f6; }
  .ann-title{ font-size:16px; letter-spacing:.1px; }
  .ann-sub{ margin-top:2px; }

  .ann-icon{
    width:40px;height:40px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#eef2ff,#f5f3ff);
    color:var(--ann-accent);
    box-shadow: 0 2px 8px rgba(99,102,241,0.12) inset;
  }

  .ann-item{
    position:relative; padding:16px 16px 14px 16px;
    border:0; border-bottom:1px solid #f3f4f6;
    background:#fff;
  }
  .ann-item:last-child{ border-bottom:0; }

  .ann-rail{
    content:""; position:absolute; left:0; top:0; bottom:0; width:4px;
    background:var(--ann-rail); border-top-left-radius:10px; border-bottom-left-radius:10px;
    opacity:.9;
  }

  .ann-avatar{
    width:36px;height:36px;border-radius:10px;
    background:#f1f5f9;color:var(--ann-accent);
    display:flex;align-items:center;justify-content:center;flex:0 0 36px;
  }

  .chip{
    display:inline-flex; align-items:center; gap:6px;
    padding:4px 8px; border-radius:999px; font-size:12px; line-height:1; white-space:nowrap;
  }
  .chip-soft{ background:#f3f4f6; color:#374151; }
  .chip-ghost{ background:transparent; color:#6b7280; border:1px dashed #e5e7eb; }
  .chip-new{ background:#dcfce7; color:#166534; font-weight:600; }

  .btn-ghost{
    --hover-bg:#f3f4f6;
    border:1px solid #e5e7eb; background:#fff; color:#111827;
    padding:6px 10px; border-radius:10px; line-height:1; display:inline-flex; align-items:center; gap:8px;
  }
  .btn-ghost:hover{ background:var(--hover-bg); }
  .btn-ghost i{ font-size:14px; }

  /* Clamp body text to 3 lines (toggle via JS) */
  .ann-body{
    font-size:14.5px; color:#1f2937;
  }
  .ann-body.clamp{
    display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
  }

  /* Dark mode (optional, if your layout toggles) */
  .dark .ann-card__header,
  .dark .ann-item{ background:#0f172a;border-color:#1f2a44; }
  .dark .ann-title{ color:#e2e8f0; }
  .dark .ann-sub, .dark .ann-body{ color:#cbd5e1; }
  .dark .chip-soft{ background:#1f2937; color:#e5e7eb; }
  .dark .chip-ghost{ border-color:#334155; color:#94a3b8; }
  .dark .btn-ghost{ background:#0f172a; border-color:#1f2937; color:#e2e8f0; }
  .dark .btn-ghost:hover{ background:#111827; }
</style>
@endpush

@push('scripts')
<script>
(function () {
  // Bootstrap v4 tooltips
  if (window.$ && $.fn.tooltip) {
    $('[data-toggle="tooltip"]').tooltip({container:'body'});
  }

  // LocalStorage helpers for dismiss
  const KEY = 'dismissed_announcements_v1';
  const getDismissed = () => { try { return JSON.parse(localStorage.getItem(KEY)||'[]'); } catch { return []; } };
  const setDismissed = (arr) => localStorage.setItem(KEY, JSON.stringify(arr));

  // Hide any dismissed ones on load
  const dismissed = new Set(getDismissed());
  document.querySelectorAll('[data-ann-id]').forEach(el => {
    if (dismissed.has(el.getAttribute('data-ann-id'))) el.remove();
  });

  // Delegated clicks
  document.addEventListener('click', async (e) => {
    // Copy
    const copyBtn = e.target.closest('[data-copy]');
    if (copyBtn) {
      const sel = copyBtn.getAttribute('data-copy');
      const node = document.querySelector(sel);
      if (!node) return;
      try {
        await navigator.clipboard.writeText(node.textContent.trim());
        window.dispatchEvent(new CustomEvent('toast', {detail:{message:'{{ __("Copied") }}'}}));
      } catch(_) {}
    }

    // Expand / Collapse
    const expBtn = e.target.closest('[data-expand]');
    if (expBtn) {
      const sel = expBtn.getAttribute('data-expand');
      const node = document.querySelector(sel);
      if (!node) return;
      node.classList.toggle('clamp');
      const icon = expBtn.querySelector('i');
      icon?.classList.toggle('fa-chevron-down');
      icon?.classList.toggle('fa-chevron-up');
    }

    // Dismiss (client-side)
    const dBtn = e.target.closest('[data-dismiss-ann]');
    if (dBtn) {
      const id = dBtn.getAttribute('data-dismiss-ann');
      const row = document.querySelector(`[data-ann-id="${id}"]`);
      if (row) row.remove();
      const list = getDismissed();
      if (!list.includes(id)) { list.push(id); setDismissed(list); }
    }
  });
})();
</script>
@endpush
