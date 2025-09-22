@extends('layouts.app')
@push('css')
  <style>
    :root{
      --primary:#6366f1; --info:#06b6d4; --success:#10b981; --warning:#f59e0b;
      --text:#111827; --muted:#6b7280; --surface:#f9fafb; --soft:#eef2f7;
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #914ba2 100%);
      --secondary-gradient: linear-gradient(135deg, #914ba2 0%, #f55757 100%);
      --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --card-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
      --hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      --border-radius: 16px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      --animation-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    .modern-header {
        /* background: var(--secondary-gradient); */
        position: relative;
        border-radius:16px;
        box-shadow:0 6px 20px rgba(0,0,0,.06);
        color: #fff;
        overflow: hidden;
    }
    .modern-header-bg1 {
        background: var(--primary-gradient);
    }
    .modern-header-bg2 {
        background: var(--secondary-gradient);
    }

    .modern-btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:999px;padding:.5rem 1rem;border:none;cursor:pointer;font-weight:600}
    .modern-btn.primary{background:linear-gradient(135deg,var(--primary),#8b5cf6);color:#fff}
    .modern-btn.outline{border:1px solid var(--soft);background:#fff;color:#111827}
    .modern-select{border-radius:999px;border:1px solid var(--soft);padding:.4rem .9rem;background:#fff}
    .filter-section{border-radius:16px;background:linear-gradient(180deg,#fff, var(--surface));box-shadow:0 6px 20px rgba(0,0,0,.06);padding:16px}
    .neo-card{border:none;border-radius:16px;background:linear-gradient(180deg,#fff, var(--surface));box-shadow:0 6px 20px rgba(0,0,0,.06)}
    .kpi-value{font-weight:800;letter-spacing:.2px}
    .muted{color:var(--muted)} .muted.tiny{font-size:11px}
    .trend-badge{border-radius:999px;padding:.25rem .5rem;font-size:.75rem;font-weight:700;color:#fff}
    .trend-badge.up{background:linear-gradient(135deg,#10b981,#059669)} .trend-badge.down{background:linear-gradient(135deg,#ef4444,#b91c1c)}
    .card-group-modern{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
    .card-group-modern-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
    @media (max-width: 1199.98px){.card-group-modern{grid-template-columns:repeat(2,1fr)}}
    @media (max-width: 575.98px){.card-group-modern{grid-template-columns:1fr}}
    .chart-container{border-radius:16px;background:linear-gradient(180deg,#fff, var(--surface));box-shadow:0 6px 20px rgba(0,0,0,.06)}
    .chart-header{padding:14px 16px;border-bottom:1px solid var(--soft)}
    .kpi-icon{display:flex;align-items:center;justify-content:center;border-radius:12px;background:var(--soft);color:var(--text)}
    .kpi-icon.primary{background:rgba(99,102,241,.1);color:#4338ca}
    .kpi-icon.success{background:rgba(16,185,129,.1);color:#065f46}
    .kpi-icon.warning{background:rgba(245,158,11,.1);color:#92400e}
    .badge{border-radius:999px;padding:.35rem .6rem;font-weight:700;font-size:.75rem}
    .badge-soft-primary{background:rgba(99,102,241,.15);color:#3730a3}
    .badge-soft-success{background:rgba(16,185,129,.15);color:#065f46}
    .chart-title{margin:0;font-weight:800} .chart-subtitle{margin:0;color:var(--muted);font-size:.85rem}
    .loading-skeleton{background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 37%,#f3f4f6 63%);background-size:400% 100%;animation:skeleton 1.4s ease infinite}
    @keyframes skeleton{0%{background-position:100% 50%}100%{background-position:0 50%}}
    /* Put the label exactly where JS tells it to be */
/* Center label – JS sets left/top to the real donut center */
.donut-box{height:240px;}               /* only the donut area */
.donut-box .ct-chart{height:100%;}
.pie-center{
  position:absolute; top:27%; left:50%;
  transform:translate(-50%,-50%);
  pointer-events:none; text-align:center; z-index:3;
}
.pie-center-title{margin:0;color:#94a3b8;font-size:12px}
.pie-center-value{margin:2px 0 0;font-weight:800;font-size:18px;color:#334155}

/* tooltip */
.chartist-tooltip{
  position:fixed; z-index:10000; pointer-events:none;
  background:rgba(0,0,0,.85); color:#fff; font-size:12px;
  padding:6px 8px; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,.18);
  transform:translate(-50%,-120%); white-space:nowrap; display:none;
}


    .legend-list{margin-top:10px}
    .legend-item{display:flex;align-items:center;justify-content:space-between;margin:8px 0}
    .legend-left{display:flex;align-items:center;gap:8px}
    .legend-dot{display:inline-block;width:10px;height:10px;border-radius:50%}
    .legend-label{font-weight:600;color:var(--text)}
    .legend-right{font-variant-numeric:tabular-nums;color:#374151}
    .progress{height:8px;background:#eef2f7;border-radius:999px;overflow:hidden;margin-top:6px}
    .progress-bar{height:8px;border-radius:999px;transition:width .45s ease}
    /* Chartist polish */
    .ct-series-a .ct-line{stroke-width:3px}
    .ct-series-a .ct-point{stroke-width:8px}
    .ct-area{opacity:.12}
    .chart-animate{animation:fadeIn .3s ease}
    @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
    /* Toasts */
    .toast-container{position:fixed;top:16px;right:16px;z-index:1080;display:flex;flex-direction:column;gap:10px}
    .toast{display:flex;align-items:center;gap:.6rem;background:#fff;border:1px solid var(--soft);box-shadow:0 10px 24px rgba(0,0,0,.08);padding:.6rem .9rem;border-radius:10px;opacity:0;transform:translateY(-6px);transition:.3s}
    .toast.show{opacity:1;transform:none}
    .toast.success{border-left:6px solid #16a34a}
    .toast.error{border-left:6px solid #dc2626}
    .toast.info{border-left:6px solid #2563eb}
    .toast.warning{border-left:6px solid #f59e0b}
    .loading-spinner{width:16px;height:16px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .9s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    .progress { height:8px; background:#f3f4f6; border-radius:999px; overflow:hidden; }
    .progress-bar { height:8px; border-radius:999px; box-shadow: inset 0 -1px 0 rgba(0,0,0,.06); }

    .js-rootresizer__contents .black-border-bigger-radius {
        min-height: 600px!important
    }
  </style>
@endpush
@section('app')

    <div class="container-fluid mt-3">
        @livewire('general.dashboard-livewire')
    </div>
@endsection
