@once
  @push('css')
    <style>
      .country-admin-shell{
        border:none;
        border-radius:28px;
        overflow:hidden;
        background:linear-gradient(180deg, rgba(255,255,255,.97), rgba(248,250,252,.93));
        box-shadow:0 24px 55px rgba(15,23,42,.08);
        margin-bottom:1.5rem;
      }
      .country-admin-header{
        position:relative;
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:18px;
        padding:26px 28px 20px;
        background:
          radial-gradient(circle at top right, rgba(236,72,153,.16), transparent 28%),
          radial-gradient(circle at left center, rgba(124,58,237,.14), transparent 32%),
          linear-gradient(135deg, rgba(248,250,252,.99), rgba(244,236,255,.94));
        border-bottom:1px solid rgba(148,163,184,.14);
      }
      .country-admin-header::after{
        content:"";
        position:absolute;
        right:-62px;
        top:-86px;
        width:228px;
        height:228px;
        border-radius:50%;
        background:radial-gradient(circle, rgba(167,139,250,.2), transparent 64%);
        pointer-events:none;
      }
      .country-admin-header.is-success{
        background:
          radial-gradient(circle at top right, rgba(34,197,94,.14), transparent 30%),
          radial-gradient(circle at left center, rgba(6,182,212,.12), transparent 34%),
          linear-gradient(135deg, rgba(248,250,252,.99), rgba(236,253,245,.94));
      }
      .country-admin-header.is-danger{
        background:
          radial-gradient(circle at top right, rgba(248,113,113,.16), transparent 30%),
          radial-gradient(circle at left center, rgba(245,158,11,.12), transparent 34%),
          linear-gradient(135deg, rgba(248,250,252,.99), rgba(255,241,242,.95));
      }
      .country-admin-eyebrow{
        display:inline-block;
        margin-bottom:8px;
        color:#7c3aed;
        font-size:.74rem;
        font-weight:800;
        letter-spacing:.08em;
        text-transform:uppercase;
      }
      .country-admin-header.is-success .country-admin-eyebrow{ color:#0f766e; }
      .country-admin-header.is-danger .country-admin-eyebrow{ color:#b91c1c; }
      .country-admin-title{
        margin:0;
        color:#0f172a;
        font-size:1.3rem;
        font-weight:900;
      }
      .country-admin-subtitle{
        margin:8px 0 0;
        max-width:720px;
        color:#64748b;
        font-size:.92rem;
        line-height:1.6;
      }
      .country-admin-badge-card{
        position:relative;
        z-index:1;
        min-width:190px;
        padding:15px 16px;
        border-radius:22px;
        background:rgba(255,255,255,.8);
        border:1px solid rgba(148,163,184,.16);
        backdrop-filter:blur(14px);
        box-shadow:0 14px 26px rgba(99,102,241,.1);
        text-align:right;
      }
      .country-admin-badge-card span{
        display:block;
        color:#64748b;
        font-size:.72rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.05em;
      }
      .country-admin-badge-card strong{
        display:block;
        margin-top:6px;
        color:#4c1d95;
        font-size:1rem;
        font-weight:900;
      }
      .country-admin-body{
        padding:24px 28px 28px;
      }
      .country-admin-alert{
        border:none;
        border-radius:16px;
        padding:.85rem 1rem;
        box-shadow:0 12px 26px rgba(15,23,42,.05);
      }
      .country-admin-toolbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:14px;
        flex-wrap:wrap;
        margin-bottom:18px;
      }
      .country-admin-toolbar-left,
      .country-admin-toolbar-right{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
      }
      .country-admin-search,
      .country-admin-per-page,
      .country-admin-input{
        min-height:48px;
        border-radius:16px;
        border:1px solid rgba(148,163,184,.22);
        background:rgba(255,255,255,.92);
        box-shadow:none;
      }
      .country-admin-per-page{
        min-width:90px;
      }
      .country-admin-search:focus,
      .country-admin-per-page:focus,
      .country-admin-input:focus{
        border-color:rgba(124,58,237,.35);
        box-shadow:0 0 0 .2rem rgba(124,58,237,.12);
      }
      .country-admin-input[disabled],
      .country-admin-input[readonly]{
        background:linear-gradient(180deg, rgba(248,250,252,.95), rgba(241,245,249,.92));
      }
      .country-admin-label{
        display:flex;
        align-items:center;
        gap:4px;
        margin-bottom:.55rem;
        color:#111827;
        font-size:.9rem;
        font-weight:800;
      }
      .country-admin-help{
        display:block;
        margin-top:8px;
        color:#64748b;
        font-size:.78rem;
        line-height:1.5;
      }
      .country-admin-select-shell{
        padding:0;
        border:none;
        background:transparent;
      }
      .country-admin-select-shell .select2-container{
        width:100% !important;
      }
      .country-admin-select-shell .select2-selection--single{
        min-height:48px !important;
        border-radius:16px !important;
        border:1px solid rgba(148,163,184,.22) !important;
        background:rgba(255,255,255,.92) !important;
        display:flex !important;
        align-items:center !important;
        padding:0 14px !important;
        box-shadow:none !important;
      }
      .country-admin-select-shell .select2-selection__rendered{
        display:flex !important;
        align-items:center !important;
        line-height:1.2 !important;
        padding-left:0 !important;
        color:#111827 !important;
      }
      .country-admin-select-shell .select2-selection__arrow{
        height:46px !important;
        right:10px !important;
      }
      .country-admin-select-shell.is-invalid .select2-selection--single{
        border-color:#dc3545 !important;
        box-shadow:0 0 0 .2rem rgba(220,53,69,.1) !important;
      }
      .country-admin-table-wrap{
        border-radius:22px;
        overflow:hidden;
        border:1px solid rgba(148,163,184,.14);
        background:#fff;
        box-shadow:0 14px 30px rgba(15,23,42,.04);
      }
      .country-admin-table{
        margin-bottom:0;
      }
      .country-admin-table thead th{
        border-top:none;
        border-bottom:1px solid rgba(226,232,240,.9);
        background:linear-gradient(180deg, rgba(248,250,252,.98), rgba(241,245,249,.92));
        color:#475569;
        font-size:.77rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.05em;
        padding:.95rem .95rem;
      }
      .country-admin-table tbody td{
        vertical-align:middle;
        border-color:rgba(226,232,240,.72);
        padding:.95rem .95rem;
        color:#0f172a;
      }
      .country-admin-table tbody tr:hover{
        background:rgba(248,250,252,.76);
      }
      .country-admin-country{
        display:flex;
        align-items:center;
        gap:.65rem;
        min-width:0;
      }
      .country-admin-country-text{
        min-width:0;
      }
      .country-admin-country-name{
        display:block;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
        font-weight:800;
      }
      .country-admin-flag{
        width:22px;
        height:16px;
        border-radius:4px;
        object-fit:cover;
        box-shadow:0 8px 16px rgba(15,23,42,.12);
      }
      .country-admin-pill{
        display:inline-flex;
        align-items:center;
        padding:.38rem .8rem;
        border-radius:999px;
        font-size:.76rem;
        font-weight:800;
        letter-spacing:.02em;
      }
      .country-admin-pill-primary{ background:rgba(124,58,237,.1); color:#5b21b6; }
      .country-admin-pill-success{ background:rgba(16,185,129,.12); color:#065f46; }
      .country-admin-pill-danger{ background:rgba(239,68,68,.12); color:#b91c1c; }
      .country-admin-pill-warning{ background:rgba(245,158,11,.14); color:#92400e; }
      .country-admin-pill-muted{ background:rgba(148,163,184,.14); color:#475569; }
      .country-admin-action-group{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
        flex-wrap:wrap;
      }
      .country-admin-empty{
        padding:34px 20px;
        text-align:center;
        color:#64748b;
        font-size:.92rem;
      }
      .country-admin-note{
        margin-top:14px;
        padding:12px 14px;
        border-radius:16px;
        background:rgba(124,58,237,.08);
        color:#5b21b6;
        font-size:.82rem;
        font-weight:700;
        line-height:1.55;
      }
      .country-admin-note.is-muted{
        background:rgba(148,163,184,.12);
        color:#475569;
      }
      .country-admin-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        gap:18px;
      }
      .country-admin-section-card{
        border-radius:24px;
        padding:20px;
        background:linear-gradient(180deg, rgba(255,255,255,.94), rgba(248,250,252,.86));
        border:1px solid rgba(148,163,184,.14);
        box-shadow:0 16px 34px rgba(15,23,42,.04);
      }
      .country-admin-section-card.is-danger{
        background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,241,242,.92));
        border-color:rgba(248,113,113,.22);
      }
      .country-admin-section-card.is-success{
        background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(236,253,245,.92));
        border-color:rgba(16,185,129,.16);
      }
      .country-admin-section-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
        flex-wrap:wrap;
      }
      .country-admin-section-title{
        margin:0;
        color:#0f172a;
        font-size:1rem;
        font-weight:900;
      }
      .country-admin-section-subtitle{
        margin:6px 0 0;
        color:#64748b;
        font-size:.84rem;
        line-height:1.55;
      }
      .country-admin-stat-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
        gap:14px;
        margin-bottom:18px;
      }
      .country-admin-stat{
        padding:16px 18px;
        border-radius:20px;
        background:rgba(255,255,255,.82);
        border:1px solid rgba(148,163,184,.14);
        box-shadow:0 12px 26px rgba(15,23,42,.04);
      }
      .country-admin-stat-label{
        display:block;
        color:#64748b;
        font-size:.75rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.05em;
      }
      .country-admin-stat-value{
        display:block;
        margin-top:8px;
        color:#0f172a;
        font-size:1.22rem;
        font-weight:900;
      }
      .country-admin-code{
        display:block;
        padding:10px 12px;
        border-radius:14px;
        background:rgba(15,23,42,.04);
        color:#334155;
        font-size:.78rem;
        font-family:Consolas, Monaco, monospace;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
      }
      .country-admin-bracket-grid{
        display:grid;
        gap:12px;
      }
      .country-admin-bracket-card{
        padding:12px 14px;
        border-radius:18px;
        background:rgba(255,255,255,.84);
        border:1px solid rgba(148,163,184,.14);
      }
      .country-admin-bracket-card strong{
        color:#0f172a;
      }
      .country-admin-modal .modal-dialog{
        margin:1.75rem auto;
      }
      .country-admin-modal .modal-content{
        border:none;
        border-radius:26px;
        overflow:hidden;
        background:linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.95));
        box-shadow:0 28px 60px rgba(15,23,42,.18);
      }
      .country-admin-modal .modal-header{
        position:relative;
        padding:22px 24px 18px;
        border-bottom:1px solid rgba(148,163,184,.14);
        background:
          radial-gradient(circle at top right, rgba(236,72,153,.15), transparent 26%),
          linear-gradient(135deg, rgba(248,250,252,.99), rgba(244,236,255,.94));
      }
      .country-admin-modal .modal-body{
        padding:22px 24px;
      }
      .country-admin-modal .modal-footer{
        padding:18px 24px 24px;
        border-top:1px solid rgba(148,163,184,.12);
      }
      .country-admin-modal .modal-title{
        color:#0f172a;
        font-size:1rem;
        font-weight:900;
      }
      .country-admin-modal .close{
        text-shadow:none;
        opacity:.6;
      }
      .country-admin-modal .close:hover{
        opacity:1;
      }
      .country-admin-mini-table th,
      .country-admin-mini-table td{
        padding:.75rem .8rem !important;
      }
      .country-admin-static-field{
        display:flex;
        align-items:center;
        gap:.65rem;
        min-height:48px;
        padding:0 14px;
        border-radius:16px;
        border:1px solid rgba(148,163,184,.16);
        background:linear-gradient(180deg, rgba(248,250,252,.96), rgba(241,245,249,.92));
        color:#0f172a;
        font-weight:700;
      }
      .country-admin-divider{
        margin:18px 0;
        border-top:1px solid rgba(226,232,240,.78);
      }
      @media (max-width: 991.98px){
        .country-admin-grid{
          grid-template-columns:1fr;
        }
      }
      @media (max-width: 767.98px){
        .country-admin-header{
          padding:22px 20px 18px;
        }
        .country-admin-badge-card{
          min-width:0;
          width:100%;
          text-align:left;
        }
        .country-admin-body{
          padding:18px 20px 20px;
        }
        .country-admin-toolbar-left,
        .country-admin-toolbar-right{
          width:100%;
        }
        .country-admin-toolbar-right > *{
          flex:1 1 auto;
        }
        .country-admin-action-group{
          justify-content:flex-start;
        }
        .country-admin-modal .modal-body,
        .country-admin-modal .modal-header,
        .country-admin-modal .modal-footer{
          padding-left:18px;
          padding-right:18px;
        }
      }
    </style>
  @endpush
@endonce
