<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar ps-container ps-theme-default ps-active-y" data-sidebarbg="skin6" data-ps-id="5dd3e313-fd02-481a-6ed2-bd6bb8032bc9">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="in">
                <li class="sidebar-item selected"> 
                    <a class="sidebar-link sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home feather-icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span class="hide-menu">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Applications') }}</span></li>

                <li class="sidebar-item"> 
                    <a class="sidebar-link" href="{{  route('sender') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag feather-icon"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7" y2="7"></line></svg>
                        <span class="hide-menu">{{ __('Sender') }}</span>
                    </a>
                </li>
                @php
                $user = auth()->user();
                $isAdmin = (int)($user->role ?? 0) === 1;
                $receiverBlocked = \App\Services\ReceiverGate::isBlockedFor($user) && !$isAdmin;
                $iconStroke = $receiverBlocked ? '#ff0000' : 'currentColor';
                @endphp

                <li class="sidebar-item"> 
                    <a 
                        class="sidebar-link {{ $receiverBlocked ? 'disabled text-danger' : '' }}"
                        href="{{ $receiverBlocked ? 'javascript:void(0)' : route('reciever') }}"
                        aria-disabled="{{ $receiverBlocked ? 'true' : 'false' }}"
                        tabindex="{{ $receiverBlocked ? '-1' : '0' }}"
                        title="{{ $receiverBlocked ? __('Receivers are disabled for your account') : '' }}">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none"
                            stroke="{{ $iconStroke }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-message-square feather-icon">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span class="hide-menu">{{ __('Receiver Form') }}</span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Utilities') }}</span></li>
                <li class="sidebar-item"> 
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text feather-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span class="hide-menu">{{ __('Countries') }} </span>
                    </a>
                    <ul aria-expanded="false" class="collapse  first-level base-level-line">
                        @if (auth()->user()->role == 1)
                        <li class="sidebar-item">
                            <a href="{{  route('country-limit') }}" class="sidebar-link"><span class="hide-menu"> {{ __('Country Limit') }}</span></a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{  route('general-country-limit') }}" class="sidebar-link"><span class="hide-menu">{{ __('General Country Limit') }}</span></a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{  route('country-tax') }}" class="sidebar-link"><span class="hide-menu"> {{ __('Country Tax') }}</span></a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{  route('general-country-tax') }}" class="sidebar-link"><span class="hide-menu">{{ __('General Country Tax') }}</span></a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{  route('country-rules') }}" class="sidebar-link"><span class="hide-menu"> {{ __('Country Bans') }}</span></a>
                        </li>
                        @endif
                        <li class="sidebar-item">
                            <a href="{{  route('country-info') }}" class="sidebar-link"><span class="hide-menu"> {{ __('Country Info') }}</span></a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item"> 
                    <a class="sidebar-link" href="{{  route('bank.statement') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid feather-icon"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span class="hide-menu">{{ __('Bank Statement') }}</span>
                    </a>
                </li>
                <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                    aria-expanded="false"><i data-feather="crosshair" class="feather-icon"></i><span class="hide-menu">{{ __('Transfers') }}</span></a>
                    <ul aria-expanded="false" class="collapse first-level base-level-line">
                        <li class="sidebar-item"> <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false"><span class="hide-menu">{{ __('Senders') }}</span></a>
                            <ul aria-expanded="false" class="collapse second-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="{{ route('sender.executed.transfer') }}" class="sidebar-link">
                                        <span class="hide-menu"> {{ __('Executed') }} </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('sender.pending.transfer') }}" class="sidebar-link">
                                        <span class="hide-menu"> {{ __('Pending') }} </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('sender.rejected.transfer') }}" class="sidebar-link">
                                        <span class="hide-menu"> {{ __('Rejected') }} </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item"> <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false"><span class="hide-menu">{{ __('Receivers') }}</span></a>
                            <ul aria-expanded="false" class="collapse second-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="{{ route('receiver.executed.transfer') }}" class="sidebar-link">
                                        <span class="hide-menu"> {{ __('Executed') }} </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('receiver.pending.transfer') }}" class="sidebar-link">
                                        <span class="hide-menu"> {{ __('Pending') }} </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('receiver.rejected.transfer') }}" class="sidebar-link">
                                        <span class="hide-menu"> {{ __('Rejected') }} </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                {{-- <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid feather-icon"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg><span class="hide-menu">Transfers </span></a>
                    <ul aria-expanded="false" class="collapse  first-level base-level-line">
                        <li class="sidebar-item"><a href="{{ route('executed.transfer') }}" class="sidebar-link">
                            <span class="hide-menu"> Executed</span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{  route('pending.transfer') }}" class="sidebar-link">
                            <span class="hide-menu"> Pending</span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{ route('rejected.transfer') }}" class="sidebar-link">
                            <span class="hide-menu"> Unexecuted</span></a>
                        </li>
                    </ul>
                </li> --}}
                <li class="sidebar-item"> 
                    <a class="sidebar-link sidebar-link" href="{{ route('mtcn') }}" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sidebar feather-icon"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                        <span class="hide-menu">{{ __('MTCN checker') }}</span>
                    </a>
                </li>
                @if (auth()->user()->role == 1)
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Balance') }}</span></li>
                <li class="sidebar-item"> 
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text feather-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span class="hide-menu">{{ __('Registers') }} </span>
                    </a>
                    <ul aria-expanded="false" class="collapse  first-level base-level-line">
                        
                        <li class="sidebar-item">
                            <a href="{{  route('balance.sender') }}" class="sidebar-link"><span class="hide-menu"> {{ __('Sender') }}</span></a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{  route('balance.reciever') }}" class="sidebar-link"><span class="hide-menu">{{ __('Receivers') }}</span></a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Announcement') }}</span></li>
                @if (auth()->user()->role == 1)
                <li class="sidebar-item"> 
                    <a class="sidebar-link" href="{{  route('announcement') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart feather-icon"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>
                        <span class="hide-menu">{{ __('Announcement') }}</span>
                    </a>
                </li>
                @endif
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">{{ __('Authintication') }}</span></li>
                @if (auth()->user()->role == 1)
                <li class="sidebar-item"> 
                    <a class="sidebar-link" href="{{  route('register') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart feather-icon"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>
                        <span class="hide-menu">{{ __('Register Managments') }}</span>
                    </a>
                </li>
                @endif
                <li class="sidebar-item"> <a class="sidebar-link sidebar-link text-danger" href="{{ route('auth.logout') }}" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out feather-icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg><span class="hide-menu">{{ __('Logout') }}</span></a></li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; height: 839px; right: 3px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 664px;"></div></div></div>
    <!-- End Sidebar scroll-->
</aside>