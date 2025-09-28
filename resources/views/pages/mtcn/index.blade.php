@extends('layouts.app')

@push('css')
<style>
  .webview-container{
    border:1px solid #ccc; height: calc(100vh - 160px); overflow:hidden; position:relative;
    border-radius: 8px; background: #fff;
  }
  .webview-fallback{
    position:absolute; inset:0; display:flex; flex-direction:column;
    align-items:center; justify-content:center; text-align:center; padding:24px;
  }
  .webview-fallback .spinner{
    width:2rem; height:2rem; border-radius:50%;
    border:3px solid #ddd; border-top-color:#666; animation:spin 1s linear infinite; margin-bottom:12px;
  }
  @keyframes spin{to{transform:rotate(360deg)}}
  iframe{width:100%; height:100%; border:none; display:block;}
</style>
@endpush

@section('app')
  <div class="page-breadcrumb">
    <div class="row">
      <div class="col-12 align-self-center">
        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('MTCN Section') }}</h4>
        <div class="d-flex align-items-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 p-0">
              <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
              <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('MTCN') }}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid mt-3">
    <div class="webview-container">
      <div id="fallback" class="webview-fallback">
        {{-- Popup button --}}
        <a id="openWuPopup" class="btn btn-primary" href="#">
          {{ __('Open Tracker (Popup)') }}
        </a>

        {{-- Hidden fallback: will be clicked if popup is blocked --}}
        <a id="openWuTab" class="btn btn-outline-secondary d-none" target="_blank" rel="noopener"
           href="https://www.westernunion.com/web/global-service/track-transfer">
          {{ __('Open in New Tab') }}
        </a>

        <p class="text-muted mt-3 small">
          {{ __('Your browser may block popups; if so, we’ll open it in a new tab automatically.') }}
        </p>
      </div>

      {{-- Keeping the original iframe (it will not render due to their headers, but harmless) --}}
      {{-- <iframe id="wuFrame" src="https://www.westernunion.com/web/global-service/track-transfer"></iframe> --}}
    </div>
  </div>
@endsection

@push('scripts')
<script>
(function () {
  const url = "https://www.westernunion.com/web/global-service/track-transfer";
  const btnPopup = document.getElementById('openWuPopup');
  const btnTab   = document.getElementById('openWuTab');
  const iframe   = document.getElementById('wuFrame');
  const fallback = document.getElementById('fallback');

  // Optional: after 3s, clarify why embedding fails
  setTimeout(() => {
    const hdr = fallback.querySelector('h5');
    if (hdr && hdr.textContent.includes('Loading')) {
      hdr.textContent = "{{ __('Embedding is blocked by Western Union') }}";
    }
  }, 3000);

  function openCenteredPopup(u, name, w, h) {
    const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    const dualScreenTop  = window.screenTop  !== undefined ? window.screenTop  : window.screenY;

    const width  = window.innerWidth  || document.documentElement.clientWidth  || screen.width;
    const height = window.innerHeight || document.documentElement.clientHeight || screen.height;

    const systemZoom = width / window.screen.availWidth;
    const left = (width  - w) / 2 / systemZoom + dualScreenLeft;
    const top  = (height - h) / 2 / systemZoom + dualScreenTop;

    const features = [
      "popup=yes",
      "toolbar=no",
      "location=no",
      "status=no",
      "menubar=no",
      "scrollbars=yes",
      "resizable=yes",
      `width=${w}`,
      `height=${h}`,
      `top=${Math.max(0, top)}`,
      `left=${Math.max(0, left)}`
    ].join(",");

    // Use a fixed name to reuse the same popup window
    const win = window.open(u, name, features);
    return win;
  }

  btnPopup.addEventListener('click', function (e) {
    e.preventDefault();
    // Must be from a user gesture for popups to be allowed
    const win = openCenteredPopup(url, "WU_Tracker", 960, 720);
    if (win && !win.closed) {
      try { win.focus(); } catch(_) {}
    } else {
      // Popup blocked → reveal & trigger the new-tab fallback
      btnTab.classList.remove('d-none');
      btnTab.click();
    }
  });

})();
</script>
@endpush
