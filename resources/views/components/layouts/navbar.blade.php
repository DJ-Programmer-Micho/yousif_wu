{{-- resources/views/components/layouts/navbar.blade.php --}}
@php
    $user = auth()->user();
    $isAdmin = (int) ($user->role ?? 0) === 1;
    $receiverBlocked = \App\Services\ReceiverGate::isBlockedFor($user) && !$isAdmin;

    $dashboardActive       = request()->routeIs('dashboard');
    $senderActive          = request()->routeIs('sender');
    $receiverActive        = request()->routeIs('reciever');
    $countriesOpen         = request()->routeIs('country-limit', 'general-country-limit', 'country-tax', 'general-country-tax', 'country-rules', 'country-info');
    $bankStatementActive   = request()->routeIs('bank.statement');
    $transfersOpen         = request()->routeIs(
        'sender.executed.transfer',
        'sender.pending.transfer',
        'sender.rejected.transfer',
        'receiver.executed.transfer',
        'receiver.pending.transfer',
        'receiver.rejected.transfer'
    );
    $senderTransfersOpen   = request()->routeIs('sender.executed.transfer', 'sender.pending.transfer', 'sender.rejected.transfer');
    $receiverTransfersOpen = request()->routeIs('receiver.executed.transfer', 'receiver.pending.transfer', 'receiver.rejected.transfer');
    $mtcnActive            = request()->routeIs('mtcn');
    $balanceOpen           = request()->routeIs('balance.sender', 'balance.reciever');
    $announcementActive    = request()->routeIs('announcement');
    $registerActive        = request()->routeIs('register');

    $itemState        = fn (bool $active): string => $active ? 'sidebar-item selected' : 'sidebar-item';
    $linkState        = fn (bool $active): string => 'sidebar-link' . ($active ? ' active' : '');
    $submenuState     = fn (bool $active): string => 'collapse first-level base-level-line' . ($active ? ' show' : '');
    $secondLevelState = fn (bool $active): string => 'collapse second-level base-level-line' . ($active ? ' show' : '');
@endphp

<aside class="left-sidebar app-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="in">

                {{-- Dashboard --}}
                <li class="{{ $itemState($dashboardActive) }}">
                    <a class="{{ $linkState($dashboardActive) }}"
                       href="{{ route('dashboard') }}"
                       aria-expanded="{{ $dashboardActive ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-home feather-icon">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span class="hide-menu">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Applications') }}</span></li>

                {{-- Sender --}}
                <li class="{{ $itemState($senderActive) }}">
                    <a class="{{ $linkState($senderActive) }}"
                       href="{{ route('sender') }}"
                       aria-expanded="{{ $senderActive ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-tag feather-icon">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7" y2="7"></line>
                        </svg>
                        <span class="hide-menu">{{ __('Sender') }}</span>
                    </a>
                </li>

                {{-- Receiver --}}
                <li class="{{ $itemState($receiverActive && !$receiverBlocked) }}">
                    <a class="{{ $linkState($receiverActive && !$receiverBlocked) }} {{ $receiverBlocked ? 'disabled text-danger app-link-disabled' : '' }}"
                       href="{{ $receiverBlocked ? 'javascript:void(0)' : route('reciever') }}"
                       aria-disabled="{{ $receiverBlocked ? 'true' : 'false' }}"
                       tabindex="{{ $receiverBlocked ? '-1' : '0' }}"
                       title="{{ $receiverBlocked ? __('Receivers are disabled for your account') : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none"
                             stroke="{{ $receiverBlocked ? '#ff6b6b' : 'currentColor' }}"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-message-square feather-icon">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span class="hide-menu">{{ __('Receiver Form') }}</span>
                    </a>
                </li>

                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Utilities') }}</span></li>

                {{-- Countries --}}
                <li class="{{ $itemState($countriesOpen) }}">
                    <a class="{{ $linkState($countriesOpen) }} has-arrow"
                       href="javascript:void(0)"
                       aria-expanded="{{ $countriesOpen ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-globe feather-icon">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <span class="hide-menu">{{ __('Countries') }}</span>
                    </a>
                    <ul aria-expanded="{{ $countriesOpen ? 'true' : 'false' }}" class="{{ $submenuState($countriesOpen) }}">
                        @if($isAdmin)
                            <li class="{{ $itemState(request()->routeIs('country-limit')) }}">
                                <a href="{{ route('country-limit') }}" class="{{ $linkState(request()->routeIs('country-limit')) }}">
                                    <i data-feather="sliders" class="feather-icon" style="width:16px;height:16px;"></i>
                                    <span class="hide-menu">{{ __('Country Limit') }}</span>
                                </a>
                            </li>
                            <li class="{{ $itemState(request()->routeIs('general-country-limit')) }}">
                                <a href="{{ route('general-country-limit') }}" class="{{ $linkState(request()->routeIs('general-country-limit')) }}">
                                    <i data-feather="sliders" class="feather-icon" style="width:16px;height:16px;"></i>
                                    <span class="hide-menu">{{ __('General Country Limit') }}</span>
                                </a>
                            </li>
                            <li class="{{ $itemState(request()->routeIs('country-tax')) }}">
                                <a href="{{ route('country-tax') }}" class="{{ $linkState(request()->routeIs('country-tax')) }}">
                                    <i data-feather="percent" class="feather-icon" style="width:16px;height:16px;"></i>
                                    <span class="hide-menu">{{ __('Country Tax') }}</span>
                                </a>
                            </li>
                            <li class="{{ $itemState(request()->routeIs('general-country-tax')) }}">
                                <a href="{{ route('general-country-tax') }}" class="{{ $linkState(request()->routeIs('general-country-tax')) }}">
                                    <i data-feather="percent" class="feather-icon" style="width:16px;height:16px;"></i>
                                    <span class="hide-menu">{{ __('General Country Tax') }}</span>
                                </a>
                            </li>
                            <li class="{{ $itemState(request()->routeIs('country-rules')) }}">
                                <a href="{{ route('country-rules') }}" class="{{ $linkState(request()->routeIs('country-rules')) }}">
                                    <i data-feather="shield" class="feather-icon" style="width:16px;height:16px;"></i>
                                    <span class="hide-menu">{{ __('Country Bans') }}</span>
                                </a>
                            </li>
                        @endif
                        <li class="{{ $itemState(request()->routeIs('country-info')) }}">
                            <a href="{{ route('country-info') }}" class="{{ $linkState(request()->routeIs('country-info')) }}">
                                <i data-feather="info" class="feather-icon" style="width:16px;height:16px;"></i>
                                <span class="hide-menu">{{ __('Country Info') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Bank Statement --}}
                <li class="{{ $itemState($bankStatementActive) }}">
                    <a class="{{ $linkState($bankStatementActive) }}"
                       href="{{ route('bank.statement') }}"
                       aria-expanded="{{ $bankStatementActive ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-credit-card feather-icon">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        <span class="hide-menu">{{ __('Bank Statement') }}</span>
                    </a>
                </li>

                {{-- Transfers --}}
                <li class="{{ $itemState($transfersOpen) }}">
                    <a class="{{ $linkState($transfersOpen) }} has-arrow"
                       href="javascript:void(0)"
                       aria-expanded="{{ $transfersOpen ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-repeat feather-icon">
                            <polyline points="17 1 21 5 17 9"></polyline>
                            <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                            <polyline points="7 23 3 19 7 15"></polyline>
                            <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                        </svg>
                        <span class="hide-menu">{{ __('Transfers') }}</span>
                    </a>
                    <ul aria-expanded="{{ $transfersOpen ? 'true' : 'false' }}" class="{{ $submenuState($transfersOpen) }}">

                        {{-- Sender transfers --}}
                        <li class="{{ $itemState($senderTransfersOpen) }}">
                            <a class="has-arrow {{ $linkState($senderTransfersOpen) }}"
                               href="javascript:void(0)"
                               aria-expanded="{{ $senderTransfersOpen ? 'true' : 'false' }}">
                                <i data-feather="arrow-up-right" class="feather-icon" style="width:16px;height:16px;"></i>
                                <span class="hide-menu">{{ __('Senders') }}</span>
                            </a>
                            <ul aria-expanded="{{ $senderTransfersOpen ? 'true' : 'false' }}" class="{{ $secondLevelState($senderTransfersOpen) }}">
                                <li class="{{ $itemState(request()->routeIs('sender.executed.transfer')) }}">
                                    <a href="{{ route('sender.executed.transfer') }}" class="{{ $linkState(request()->routeIs('sender.executed.transfer')) }}">
                                        <i data-feather="check-circle" class="feather-icon" style="width:14px;height:14px;"></i>
                                        <span class="hide-menu">{{ __('Executed') }}</span>
                                    </a>
                                </li>
                                <li class="{{ $itemState(request()->routeIs('sender.pending.transfer')) }}">
                                    <a href="{{ route('sender.pending.transfer') }}" class="{{ $linkState(request()->routeIs('sender.pending.transfer')) }}">
                                        <i data-feather="clock" class="feather-icon" style="width:14px;height:14px;"></i>
                                        <span class="hide-menu">{{ __('Pending') }}</span>
                                    </a>
                                </li>
                                <li class="{{ $itemState(request()->routeIs('sender.rejected.transfer')) }}">
                                    <a href="{{ route('sender.rejected.transfer') }}" class="{{ $linkState(request()->routeIs('sender.rejected.transfer')) }}">
                                        <i data-feather="x-circle" class="feather-icon" style="width:14px;height:14px;"></i>
                                        <span class="hide-menu">{{ __('Rejected') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Receiver transfers --}}
                        <li class="{{ $itemState($receiverTransfersOpen) }}">
                            <a class="has-arrow {{ $linkState($receiverTransfersOpen) }}"
                               href="javascript:void(0)"
                               aria-expanded="{{ $receiverTransfersOpen ? 'true' : 'false' }}">
                                <i data-feather="arrow-down-left" class="feather-icon" style="width:16px;height:16px;"></i>
                                <span class="hide-menu">{{ __('Receivers') }}</span>
                            </a>
                            <ul aria-expanded="{{ $receiverTransfersOpen ? 'true' : 'false' }}" class="{{ $secondLevelState($receiverTransfersOpen) }}">
                                <li class="{{ $itemState(request()->routeIs('receiver.executed.transfer')) }}">
                                    <a href="{{ route('receiver.executed.transfer') }}" class="{{ $linkState(request()->routeIs('receiver.executed.transfer')) }}">
                                        <i data-feather="check-circle" class="feather-icon" style="width:14px;height:14px;"></i>
                                        <span class="hide-menu">{{ __('Executed') }}</span>
                                    </a>
                                </li>
                                <li class="{{ $itemState(request()->routeIs('receiver.pending.transfer')) }}">
                                    <a href="{{ route('receiver.pending.transfer') }}" class="{{ $linkState(request()->routeIs('receiver.pending.transfer')) }}">
                                        <i data-feather="clock" class="feather-icon" style="width:14px;height:14px;"></i>
                                        <span class="hide-menu">{{ __('Pending') }}</span>
                                    </a>
                                </li>
                                <li class="{{ $itemState(request()->routeIs('receiver.rejected.transfer')) }}">
                                    <a href="{{ route('receiver.rejected.transfer') }}" class="{{ $linkState(request()->routeIs('receiver.rejected.transfer')) }}">
                                        <i data-feather="x-circle" class="feather-icon" style="width:14px;height:14px;"></i>
                                        <span class="hide-menu">{{ __('Rejected') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                {{-- MTCN --}}
                <li class="{{ $itemState($mtcnActive) }}">
                    <a class="{{ $linkState($mtcnActive) }}"
                       href="{{ route('mtcn') }}"
                       aria-expanded="{{ $mtcnActive ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-search feather-icon">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <span class="hide-menu">{{ __('MTCN checker') }}</span>
                    </a>
                </li>

                {{-- Balance (admin only) --}}
                @if($isAdmin)
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu">{{ __('Balance') }}</span></li>

                    <li class="{{ $itemState($balanceOpen) }}">
                        <a class="{{ $linkState($balanceOpen) }} has-arrow"
                           href="javascript:void(0)"
                           aria-expanded="{{ $balanceOpen ? 'true' : 'false' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-dollar-sign feather-icon">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <span class="hide-menu">{{ __('Registers') }}</span>
                        </a>
                        <ul aria-expanded="{{ $balanceOpen ? 'true' : 'false' }}" class="{{ $submenuState($balanceOpen) }}">
                            <li class="{{ $itemState(request()->routeIs('balance.sender')) }}">
                                <a href="{{ route('balance.sender') }}" class="{{ $linkState(request()->routeIs('balance.sender')) }}">
                                    <span class="hide-menu">{{ __('Sender') }}</span>
                                </a>
                            </li>
                            <li class="{{ $itemState(request()->routeIs('balance.reciever')) }}">
                                <a href="{{ route('balance.reciever') }}" class="{{ $linkState(request()->routeIs('balance.reciever')) }}">
                                    <span class="hide-menu">{{ __('Receivers') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Announcement (admin only) --}}
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Announcement') }}</span></li>

                @if($isAdmin)
                    <li class="{{ $itemState($announcementActive) }}">
                        <a class="{{ $linkState($announcementActive) }}"
                           href="{{ route('announcement') }}"
                           aria-expanded="{{ $announcementActive ? 'true' : 'false' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-bell feather-icon">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span class="hide-menu">{{ __('Announcement') }}</span>
                        </a>
                    </li>
                @endif

                {{-- Authentication (admin only) --}}
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Authentication') }}</span></li>

                @if($isAdmin)
                    <li class="{{ $itemState($registerActive) }}">
                        <a class="{{ $linkState($registerActive) }}"
                           href="{{ route('register') }}"
                           aria-expanded="{{ $registerActive ? 'true' : 'false' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-users feather-icon">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span class="hide-menu">{{ __('Register Managements') }}</span>
                        </a>
                    </li>
                @endif

                {{-- Logout --}}
                <li class="list-divider"></li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('auth.logout') }}" aria-expanded="false"
                       style="color: rgba(255,120,120,.85) !important; border-color: rgba(200,60,60,.18); border-bottom-color: rgba(180,40,40,.28);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-log-out feather-icon">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span class="hide-menu">{{ __('Logout') }}</span>
                    </a>
                </li>

            </ul>
        </nav>

        {{-- Promo card --}}
        <div class="sidebar-promo-card">
            <div class="sidebar-promo-label">{{ __('Iraq Remit') }}</div>
            <div class="sidebar-promo-title">{{ __('Fast transfer workspace') }}</div>
            <div class="sidebar-promo-text">{{ __('New Design, New Look and Better Control v3.1') }}</div>
        </div>
    </div>
</aside>