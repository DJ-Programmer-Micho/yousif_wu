@php
  $locale = app()->getLocale();
  $isRtl = in_array($locale, ['ar', 'ku', 'fa', 'ur']);
  $directionClass = $isRtl ? 'is-rtl' : 'is-ltr';
@endphp

<div>
  @if($rows->isNotEmpty())
    @once
      @push('css')
        <style>
          .dashboard-newsbar-wrap{
            margin:0 0 1rem 0;
          }

          .dashboard-newsbar{
            position:relative;
            display:flex;
            align-items:center;
            gap:0;
            overflow:hidden;
            min-height:54px;
            border-radius:18px;
            border:1px solid rgba(255,255,255,.7);
            background:
              radial-gradient(circle at top right, rgba(236,72,153,.10), transparent 30%),
              radial-gradient(circle at left center, rgba(99,102,241,.13), transparent 34%),
              linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.92));
            box-shadow:0 18px 44px rgba(15,23,42,.08);
            backdrop-filter:blur(16px);
          }

          .dashboard-newsbar-label{
            position:relative;
            z-index:2;
            display:inline-flex;
            align-items:center;
            gap:.6rem;
            flex:0 0 auto;
            min-height:54px;
            padding:0 1rem 0 1.1rem;
            font-weight:800;
            color:#fff;
            background:linear-gradient(135deg, #7c3aed, #ec4899);
            box-shadow:8px 0 24px rgba(124,58,237,.18);
          }

          .dashboard-newsbar-label i{
            font-size:.95rem;
          }

          .dashboard-newsbar-track{
            position:relative;
            flex:1 1 auto;
            overflow:hidden;
            white-space:nowrap;
            min-width:0;
          }

          .dashboard-newsbar-fade-left,
          .dashboard-newsbar-fade-right{
            position:absolute;
            top:0;
            bottom:0;
            width:40px;
            z-index:2;
            pointer-events:none;
          }

          .dashboard-newsbar-fade-left{
            left:0;
            background:linear-gradient(to right, rgba(248,250,252,1), rgba(248,250,252,0));
          }

          .dashboard-newsbar-fade-right{
            right:0;
            background:linear-gradient(to left, rgba(248,250,252,1), rgba(248,250,252,0));
          }

          .dashboard-newsbar-marquee{
            display:inline-flex;
            align-items:center;
            gap:2rem;
            min-width:max-content;
            padding-inline:2rem;
            will-change:transform;
            animation-duration:35s;
            animation-timing-function:linear;
            animation-iteration-count:infinite;
          }

          .dashboard-newsbar.is-ltr .dashboard-newsbar-marquee{
            animation-name:newsTickerLtr;
          }

          .dashboard-newsbar.is-rtl .dashboard-newsbar-marquee{
            animation-name:newsTickerRtl;
          }

          .dashboard-newsbar:hover .dashboard-newsbar-marquee{
            animation-play-state:paused;
          }

          .dashboard-newsbar-item{
            display:inline-flex;
            align-items:center;
            gap:.7rem;
            color:#0f172a;
            font-weight:600;
            font-size:.95rem;
            line-height:1;
          }

          .dashboard-newsbar-dot{
            width:8px;
            height:8px;
            border-radius:999px;
            background:linear-gradient(135deg, #7c3aed, #ec4899);
            box-shadow:0 0 0 4px rgba(124,58,237,.10);
            flex:0 0 8px;
          }

          .dashboard-newsbar-date{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            padding:.3rem .55rem;
            border-radius:999px;
            background:rgba(99,102,241,.10);
            color:#4338ca;
            font-size:.76rem;
            font-weight:700;
            line-height:1;
          }

          .dashboard-newsbar-text{
            color:#1e293b;
          }

          .dashboard-newsbar-separator{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:#cbd5e1;
            font-size:1rem;
          }

          @keyframes newsTickerLtr {
            0%   { transform:translateX(0); }
            100% { transform:translateX(-50%); }
          }

          @keyframes newsTickerRtl {
            0%   { transform:translateX(-50%); }
            100% { transform:translateX(0); }
          }

          @media (max-width: 767.98px){
            .dashboard-newsbar{
              min-height:50px;
              border-radius:16px;
            }

            .dashboard-newsbar-label{
              padding:0 .8rem 0 .9rem;
              font-size:.85rem;
            }

            .dashboard-newsbar-item{
              font-size:.88rem;
            }

            .dashboard-newsbar-date{
              display:none;
            }
          }
        </style>
      @endpush
    @endonce

    <div class="dashboard-newsbar-wrap">
      <div class="dashboard-newsbar {{ $directionClass }}">
        <div class="dashboard-newsbar-label">
          <i class="fas fa-bullhorn"></i>
          <span>{{ __('News') }}</span>
        </div>

        <div class="dashboard-newsbar-track">
          <div class="dashboard-newsbar-fade-left"></div>
          <div class="dashboard-newsbar-fade-right"></div>

          <div class="dashboard-newsbar-marquee">
            @for($loopSet = 0; $loopSet < 2; $loopSet++)
              @foreach($rows as $a)
                <div class="dashboard-newsbar-item">
                  <span class="dashboard-newsbar-dot"></span>

                  @if($a->created_at)
                    <span class="dashboard-newsbar-date">
                      <i class="far fa-clock"></i>{{ $a->created_at->format('Y-m-d') }}
                    </span>
                  @endif

                  <span class="dashboard-newsbar-text">{{ trim($a->body) }}</span>
                </div>

                <span class="dashboard-newsbar-separator">
                  <i class="fas fa-circle" style="font-size:6px;"></i>
                </span>
              @endforeach
            @endfor
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
