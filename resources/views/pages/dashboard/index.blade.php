@extends('layouts.app')
@push('css')
  <style>
    :root{
      --primary:#6366f1; --info:#06b6d4; --success:#10b981; --warning:#f59e0b;
      --text:#111827; --muted:#6b7280; --surface:#f9fafb; --soft:#eef2f7;
      --primary-gradient: linear-gradient(135deg, #ea9066 0%, #4ba24b 100%);
      --secondary-gradient: linear-gradient(135deg, #4ba24b 0%, #ebf557 100%);
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
    .gap-2{gap:.5rem}
    .gap-3{gap:1rem}
    .modern-select{border-radius:999px;border:1px solid var(--soft);padding:.4rem .9rem;background:#fff}
    .filter-section{border-radius:16px;background:linear-gradient(180deg,rgba(255,255,255,.92), rgba(249,250,251,.88));border:1px solid rgba(255,255,255,.72);backdrop-filter:blur(16px);box-shadow:0 12px 30px rgba(99,102,241,.08);padding:16px}
    .neo-card{border:none;border-radius:16px;background:linear-gradient(180deg,rgba(255,255,255,.94), rgba(249,250,251,.9));border:1px solid rgba(255,255,255,.72);backdrop-filter:blur(16px);box-shadow:0 12px 30px rgba(99,102,241,.08)}
    .kpi-value{font-weight:800;letter-spacing:.2px}
    .muted{color:var(--muted)} .muted.tiny{font-size:11px}
    .trend-badge{border-radius:999px;padding:.25rem .5rem;font-size:.75rem;font-weight:700;color:#fff}
    .trend-badge.up{background:linear-gradient(135deg,#10b981,#059669)} .trend-badge.down{background:linear-gradient(135deg,#ef4444,#b91c1c)}
    .card-group-modern{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
    .card-group-modern-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
    @media (max-width: 1199.98px){.card-group-modern{grid-template-columns:repeat(2,1fr)}}
    @media (max-width: 575.98px){.card-group-modern{grid-template-columns:1fr}}
    .chart-container{border-radius:24px;background:linear-gradient(180deg,rgba(255,255,255,.94), rgba(249,250,251,.9));border:1px solid rgba(255,255,255,.72);backdrop-filter:blur(18px);box-shadow:0 18px 40px rgba(99,102,241,.12);overflow:hidden}
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
.donut-box{height:240px;}
.donut-box .ct-chart{height:100%;}
.pie-center{
  position:absolute;
  top:50%;
  left:50%;
  width:136px;
  height:136px;
  transform:translate(-50%,-50%);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:18px;
  border-radius:50%;
  pointer-events:none;
  text-align:center;
  z-index:3;
  background:linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.74));
  border:1px solid rgba(255,255,255,.78);
  backdrop-filter:blur(18px);
  box-shadow:0 18px 32px rgba(148,163,184,.18), inset 0 1px 0 rgba(255,255,255,.92);
}
.pie-center-title{
  margin:0;
  color:#64748b;
  font-size:.66rem;
  font-weight:800;
  letter-spacing:.1em;
  text-transform:uppercase;
}
.pie-center-value{
  margin:6px 0 0;
  font-weight:900;
  font-size:1.4rem;
  line-height:1.05;
  color:#0f172a;
}

/* tooltip */
.chartist-tooltip{
  position:fixed; z-index:10000; pointer-events:none;
  background:rgba(0,0,0,.85); color:#fff; font-size:12px;
  padding:6px 8px; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,.18);
  transform:translate(-50%,-120%); white-space:normal; display:none;
  min-width:132px; max-width:220px; line-height:1.35;
}


    .legend-list{
      display:flex;
      flex-direction:column;
      gap:12px;
      margin-top:2px;
    }
    .legend-item{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin:0;
    }
    .legend-item-card{
      padding:12px 14px;
      border-radius:18px;
      background:linear-gradient(180deg, rgba(255,255,255,.84), rgba(248,250,252,.72));
      border:1px solid rgba(148,163,184,.14);
      box-shadow:0 12px 28px rgba(15,23,42,.05);
      transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }
    .analytics-pie-card:hover .legend-item-card{
      transform:translateY(-1px);
      box-shadow:0 16px 30px rgba(99,102,241,.09);
      border-color:rgba(124,58,237,.16);
    }
    .legend-left{
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
      flex:1;
    }
    .legend-rank{
      width:28px;
      height:28px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      flex:0 0 auto;
      border-radius:10px;
      background:linear-gradient(135deg, rgba(124,58,237,.14), rgba(236,72,153,.12));
      color:#5b21b6;
      font-size:.76rem;
      font-weight:900;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.85);
    }
    .legend-dot{
      display:inline-block;
      width:10px;
      height:10px;
      border-radius:50%;
      box-shadow:0 0 0 4px rgba(255,255,255,.65), 0 10px 16px rgba(15,23,42,.12);
      flex:0 0 auto;
    }
    .legend-copy{
      display:flex;
      flex-direction:column;
      min-width:0;
      gap:2px;
    }
    .legend-label{
      display:block;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      font-weight:800;
      color:var(--text);
    }
    .legend-meta{
      color:#64748b;
      font-size:.73rem;
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:.05em;
    }
    .legend-right{
      flex:0 0 auto;
      font-size:.94rem;
      font-weight:900;
      font-variant-numeric:tabular-nums;
      color:#0f172a;
      white-space:nowrap;
    }
    .progress{height:8px;background:#eef2f7;border-radius:999px;overflow:hidden;margin-top:6px}
    .progress-bar{height:8px;border-radius:999px;transition:width .45s ease}
    .progress-soft{
      height:6px;
      margin:-4px 12px 0;
      background:linear-gradient(90deg, rgba(226,232,240,.9), rgba(241,245,249,.95));
      box-shadow:none;
    }
    .progress-soft .progress-bar{
      height:6px;
      border-radius:999px;
      box-shadow:0 8px 16px rgba(99,102,241,.14);
    }
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

    .dashboard-shell{align-items:flex-start}
    .trend-hero-card{
      background:
        radial-gradient(circle at top right, rgba(236,72,153,.16), transparent 28%),
        radial-gradient(circle at left center, rgba(124,58,237,.12), transparent 36%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(250,245,255,.92));
    }
    .trend-toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end}
    .trend-switch{
      display:inline-flex;
      align-items:center;
      padding:4px;
      border-radius:999px;
      background:rgba(99,102,241,.08);
      border:1px solid rgba(99,102,241,.12);
    }
    .trend-switch-btn{
      border:none;
      background:transparent;
      color:#6b7280;
      font-size:.85rem;
      font-weight:700;
      padding:.45rem .95rem;
      border-radius:999px;
      transition:var(--transition);
    }
    .trend-switch-btn.active{
      background:linear-gradient(135deg,#7c3aed,#ec4899);
      color:#fff;
      box-shadow:0 10px 20px rgba(124,58,237,.22);
    }
    .trend-chart .ct-grid{stroke:rgba(124,58,237,.08)}
    .analytics-card{
      background:
        radial-gradient(circle at top right, rgba(34,197,94,.12), transparent 26%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(240,253,244,.92));
    }
    .analytics-card .ct-grid{stroke:rgba(16,185,129,.08)}
    .analytics-chip-group{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .analytics-value-pill{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-height:34px;
      padding:.4rem .8rem;
      border-radius:999px;
      background:rgba(255,255,255,.82);
      border:1px solid rgba(148,163,184,.16);
      color:#0f172a;
      font-weight:800;
      font-variant-numeric:tabular-nums;
      box-shadow:0 8px 18px rgba(15,23,42,.06);
    }
    .analytics-footer{
      display:grid;
      grid-template-columns:repeat(2, minmax(0, 1fr));
      gap:12px;
    }
    .analytics-footer-pill{
      padding:12px 14px;
      border-radius:16px;
      background:rgba(255,255,255,.75);
      border:1px solid rgba(16,185,129,.12);
    }
    .analytics-footer-pill span{
      display:block;
      color:#6b7280;
      font-size:.76rem;
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:.04em;
      margin-bottom:3px;
    }
    .analytics-footer-pill strong{
      color:#065f46;
      font-size:.95rem;
    }
    .analytics-pie-card{
      position:relative;
    }
    .analytics-pie-card-primary{
      background:
        radial-gradient(circle at top right, rgba(124,58,237,.14), transparent 28%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(250,245,255,.9));
    }
    .analytics-pie-card-success{
      background:
        radial-gradient(circle at top right, rgba(16,185,129,.14), transparent 28%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(239,253,245,.9));
    }
    .analytics-pie-card-warning{
      background:
        radial-gradient(circle at top right, rgba(245,158,11,.14), transparent 28%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,251,235,.92));
    }
    .analytics-pie-card::before{
      content:"";
      position:absolute;
      inset:0;
      pointer-events:none;
      opacity:.9;
    }
    .analytics-pie-card-primary::before{
      background:radial-gradient(circle at top right, rgba(124,58,237,.12), transparent 30%);
    }
    .analytics-pie-card-success::before{
      background:radial-gradient(circle at top right, rgba(16,185,129,.12), transparent 30%);
    }
    .analytics-pie-card-warning::before{
      background:radial-gradient(circle at top right, rgba(245,158,11,.14), transparent 30%);
    }
    .analytics-pie-card .chart-header,
    .analytics-pie-card .p-3{
      position:relative;
      z-index:1;
    }
    .analytics-pie-stage{
      position:relative;
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:258px;
      margin-bottom:18px;
      padding:18px;
      border-radius:26px;
      overflow:hidden;
      isolation:isolate;
      border:1px solid rgba(255,255,255,.55);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.85);
    }
    .analytics-pie-stage::before,
    .analytics-pie-stage::after{
      content:"";
      position:absolute;
      border-radius:50%;
      pointer-events:none;
    }
    .analytics-pie-stage::before{
      inset:26px;
      border:1px dashed rgba(255,255,255,.34);
      opacity:.85;
    }
    .analytics-pie-stage::after{
      inset:48px;
      border:1px solid rgba(255,255,255,.2);
      opacity:.9;
    }
    .analytics-pie-stage-primary{
      background:
        radial-gradient(circle at 18% 18%, rgba(255,255,255,.92), transparent 24%),
        radial-gradient(circle at 82% 18%, rgba(196,181,253,.48), transparent 28%),
        linear-gradient(135deg, rgba(99,102,241,.18), rgba(236,72,153,.12));
    }
    .analytics-pie-stage-success{
      background:
        radial-gradient(circle at 18% 18%, rgba(255,255,255,.92), transparent 24%),
        radial-gradient(circle at 82% 18%, rgba(110,231,183,.42), transparent 28%),
        linear-gradient(135deg, rgba(16,185,129,.16), rgba(6,182,212,.12));
    }
    .analytics-pie-stage-warning{
      background:
        radial-gradient(circle at 18% 18%, rgba(255,255,255,.92), transparent 24%),
        radial-gradient(circle at 82% 18%, rgba(253,230,138,.56), transparent 30%),
        linear-gradient(135deg, rgba(245,158,11,.18), rgba(244,114,182,.1));
    }
    .analytics-pie-stage-glow{
      position:absolute;
      left:16%;
      right:16%;
      bottom:16%;
      height:38%;
      border-radius:999px;
      filter:blur(44px);
      opacity:.7;
      z-index:0;
      pointer-events:none;
    }
    .analytics-pie-stage-primary .analytics-pie-stage-glow{
      background:linear-gradient(90deg, rgba(124,58,237,.28), rgba(236,72,153,.18));
    }
    .analytics-pie-stage-success .analytics-pie-stage-glow{
      background:linear-gradient(90deg, rgba(16,185,129,.24), rgba(6,182,212,.16));
    }
    .analytics-pie-stage-warning .analytics-pie-stage-glow{
      background:linear-gradient(90deg, rgba(245,158,11,.28), rgba(244,114,182,.14));
    }
    .analytics-pie-chart{
      position:relative;
      z-index:2;
      width:100%;
      height:240px !important;
    }
    .analytics-pie-chart .ct-slice-donut{
      filter:drop-shadow(0 10px 18px rgba(71,85,105,.12));
      transition:stroke-width .22s ease, opacity .22s ease;
    }
    .analytics-pie-card:hover .analytics-pie-chart .ct-slice-donut{
      opacity:.96;
    }
    .trend-insight-bar{
      display:grid;
      grid-template-columns:repeat(3, minmax(0, 1fr));
      gap:12px;
    }
    .trend-insight-pill{
      display:flex;
      flex-direction:column;
      gap:2px;
      padding:14px 16px;
      border-radius:18px;
      background:rgba(255,255,255,.72);
      border:1px solid rgba(124,58,237,.08);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.6);
    }
    .trend-insight-pill span{font-size:.76rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em}
    .trend-insight-pill strong{font-size:1rem;color:#111827}

    .quick-sidebar-stack{display:grid;gap:16px}
    .quick-send-header{background:linear-gradient(180deg,rgba(255,255,255,.9), rgba(246,240,255,.86))}
    .quick-send-card.is-warning{box-shadow:0 18px 40px rgba(239,68,68,.12)}
    .quick-balance-card{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      padding:16px 18px;
      border-radius:20px;
      background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(236,72,153,.08));
      border:1px solid rgba(124,58,237,.14);
      margin-bottom:16px;
    }
    .quick-balance-card.is-warning{
      background:linear-gradient(135deg,rgba(248,113,113,.12),rgba(251,191,36,.12));
      border-color:rgba(239,68,68,.16);
    }
    .quick-balance-label{font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6b7280}
    .quick-balance-value{font-size:1.6rem;font-weight:800;color:#111827;line-height:1.1}
    .quick-balance-meta{display:flex;flex-direction:column;align-items:flex-end;gap:4px;text-align:right;font-size:.82rem;color:#6b7280}
    .quick-balance-meta strong{font-size:1rem;color:#4c1d95}
    .quick-steps{
      display:flex;
      align-items:center;
      flex-wrap:wrap;
      gap:8px;
      margin-bottom:18px;
      padding-bottom:14px;
      border-bottom:1px solid rgba(148,163,184,.18);
    }
    .quick-step{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:.45rem .8rem;
      border-radius:999px;
      background:rgba(99,102,241,.06);
      color:#6b7280;
      font-size:.8rem;
      font-weight:700;
    }
    .quick-step small{
      width:22px;
      height:22px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:50%;
      background:#fff;
      color:#7c3aed;
      font-size:.7rem;
      font-weight:800;
      margin:0;
    }
    .quick-step.is-active{
      background:linear-gradient(135deg,#7c3aed,#ec4899);
      color:#fff;
      box-shadow:0 12px 24px rgba(124,58,237,.2);
    }
    .quick-step.is-active small{color:#7c3aed}
    .quick-label{font-weight:700;color:#111827;margin-bottom:.5rem}
    .quick-input{
      min-height:52px;
      border-radius:16px;
      border:1px solid rgba(148,163,184,.22);
      background:rgba(255,255,255,.88);
      box-shadow:none;
    }
    .quick-input:focus{
      border-color:rgba(124,58,237,.35);
      box-shadow:0 0 0 .2rem rgba(124,58,237,.12);
    }
    .quick-summary{
      margin:18px 0 14px;
      padding:8px 0;
      border-top:1px solid rgba(148,163,184,.18);
      border-bottom:1px solid rgba(148,163,184,.18);
    }
    .quick-summary-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:10px 0;
      color:#475569;
      font-weight:600;
    }
    .quick-summary-row strong{font-size:1rem;color:#111827}
    .quick-summary-row.is-emphasis strong{font-size:1.15rem;color:#4c1d95}
    .quick-warning{
      display:flex;
      align-items:center;
      padding:12px 14px;
      margin-bottom:14px;
      border-radius:16px;
      background:rgba(248,113,113,.12);
      color:#b91c1c;
      font-size:.9rem;
      font-weight:600;
    }
    .quick-send-btn{
      min-height:54px;
      border:none;
      border-radius:16px;
      font-weight:800;
      letter-spacing:.01em;
      background:linear-gradient(135deg,#7c3aed,#5b5ce6);
      box-shadow:0 16px 30px rgba(124,58,237,.24);
      color:#fff;
      transition:var(--transition);
    }
    .quick-send-btn:hover:not(.disabled):not(:disabled){
      color:#fff;
      transform:translateY(-1px);
      box-shadow:0 20px 34px rgba(124,58,237,.28);
    }
    .quick-send-btn.disabled,
    .quick-send-btn:disabled{
      opacity:.65;
      box-shadow:none;
      cursor:not-allowed;
    }
    .quick-helper{
      margin-top:12px;
      color:#6b7280;
      font-size:.82rem;
      line-height:1.55;
    }
    .exchange-card .chart-header{background:linear-gradient(180deg,rgba(255,255,255,.88), rgba(241,248,255,.82))}
    .exchange-rate-list{
      display:flex;
      flex-direction:column;
      gap:10px;
      max-height:440px;
      overflow-y:auto;
      padding-right:4px;
    }
    .exchange-rate-head,
    .exchange-rate-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
    }
    .exchange-rate-head{
      position:sticky;
      top:0;
      z-index:1;
      padding:0 2px 8px;
      font-size:.76rem;
      font-weight:800;
      color:#6b7280;
      text-transform:uppercase;
      letter-spacing:.04em;
      background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(255,255,255,.9));
    }
    .exchange-rate-row{
      padding:12px 14px;
      border-radius:16px;
      background:rgba(255,255,255,.78);
      border:1px solid rgba(148,163,184,.12);
      box-shadow:0 8px 20px rgba(15,23,42,.05);
    }
    .exchange-rate-main{
      display:flex;
      flex-direction:column;
      gap:2px;
      min-width:0;
    }
    .exchange-rate-main strong{font-size:.95rem;color:#111827}
    .exchange-rate-main span{
      color:#64748b;
      font-size:.8rem;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .exchange-rate-values{
      display:flex;
      align-items:center;
      gap:6px;
      font-weight:800;
      color:#334155;
    }
    .exchange-rate-values small{margin:0;color:#94a3b8;font-size:.75rem}

    @media (min-width: 1200px){
      .dashboard-aside .quick-sidebar-stack{position:sticky;top:12px}
    }
    @media (max-width: 1199.98px){
      .trend-insight-bar{grid-template-columns:repeat(2, minmax(0, 1fr))}
    }
    @media (max-width: 767.98px){
      .trend-insight-bar{grid-template-columns:1fr}
      .analytics-footer{grid-template-columns:1fr}
      .quick-balance-card{flex-direction:column;align-items:flex-start}
      .quick-balance-meta{align-items:flex-start;text-align:left}
      .trend-toolbar{justify-content:flex-start}
      .analytics-pie-stage{
        min-height:232px;
        padding:14px;
      }
      .analytics-pie-chart{
        height:212px !important;
      }
      .pie-center{
        width:118px;
        height:118px;
        padding:14px;
      }
      .pie-center-value{
        font-size:1.15rem;
      }
      .legend-item-card{
        padding:11px 12px;
      }
      .legend-right{
        font-size:.88rem;
      }
    }

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
