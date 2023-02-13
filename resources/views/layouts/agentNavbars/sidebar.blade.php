<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-default" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main"
                aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0"
           href="{{ auth()->user()->user_level == 'admin' ? route('admin-dashboard') : route('dashboard')}}">
            {{-- <img src="//logo.clearbit.com/clearbit.com?size=50" class="navbar-brand-img" alt="..."> --}}
            <h1 class="text-white" style="">
                {{-- config('app.name')--}}
                {{env('APP_NAME')}}
            </h1>
            {{-- <img src="{{ asset('argon') }}/img/brand/blue.png" class="navbar-brand-img" alt="..."> --}}
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">

                            {{-- <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1-800x800.jpg"> --}}
                            <img alt="Image placeholder"
                                 src="https://www.clipartmax.com/png/full/123-1237090_these-are-some-cats-avatar-i-drew-during-my-free-time-animated.png">

                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>{{ __('My profile') }}</span>
                    </a>
                    <a href="{{ route('profile.settings') }}" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">

                        <a href="{{  auth()->user()->user_level == 'admin' ? route('admin-dashboard') : route('dashboard') }}">
                            <img
                                src="https://www.clipartmax.com/png/full/123-1237090_these-are-some-cats-avatar-i-drew-during-my-free-time-animated.png">

                            {{-- <img src="{{ asset('argon') }}/img/brand/blue.png"> --}}
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                                data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false"
                                aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended"
                           placeholder="{{ __('Search') }}" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Navigation -->

            <!-- Divider -->
            <hr class="my-3">
            <!-- Heading -->
            <h6 class="navbar-heading text-muted">{{ __('General') }}</h6>
            <!-- Navigation -->
            <ul class="navbar-nav mb-md-3 ">
                <li class="nav-item">
                    <a class="nav-link text-white"
                       href="{{ auth()->user()->user_level == 'admin' ? route('admin-dashboard'): route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                @if(auth()->user()->user_level == 'admin')
                    <li class="nav-item">
                        <a class="nav-link text-white"
                           href="{{route('admin.player.betting')}}">
                            <i class="fa fa-cog"></i> {{ __('Live Betting') }}
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-white"
                       href="{{ auth()->user()->user_level == 'admin' ? route('admin_Logs'): route('Logs') }}">
                        <i class="fa fa-cog"></i> {{ __('Betting Logs') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white"
                       href="{{ auth()->user()->user_level == 'admin' ? route('admin-comission-logs'): route('comission-logs') }}">
                        <i class="fas fa-chart-line"></i> {{ __('Commission Logs') }}
                    </a>
                </li>
            </ul>
            <!-- Divider -->
            <hr class="my-3">
            <!-- Heading -->
            <h6 class="navbar-heading text-muted">{{ __('Loading Station') }}</h6>
            <!-- Navigation -->
            <ul class="navbar-nav mb-md-3">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#loadin-station" data-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="loadin-station">
                        <i class="fas fa-wallet"></i>
                        <span class="nav-link-text">{{ __('Wallet') }}</span>
                    </a>

                    <div class="collapse" id="loadin-station">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-white"
                                   href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-wallet'): route('agent-wallet') }}">
                                    {{ __('Wallet Station') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white"
                                   href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-wallet-logs'): route('agent-wallet-logs') }}">
                                    {{ __('Wallet Logs') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#loading-comission" data-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="loading-comission">
                        <i class="fas fa-money-bill"></i>
                        <span class="nav-link-text">{{ __('Commission') }}</span>
                    </a>

                    <div class="collapse" id="loading-comission">
                        <ul class="nav nav-sm flex-column">

                            @if (auth()->user()->user_level != 'silver-agent')
                                <li class="nav-item">
                                    <a class="nav-link text-white"
                                       href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-comission'): route('agent-comission') }}">
                                        {{ __('Commission Station') }}
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link text-white"
                                   href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-comission-logs'): route('agent-comission-logs') }}">

                                    {{ __('Commission Logs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white"
                                   href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-comission-archive'): route('agent-comission-archive') }}">

                                    {{ __('Commission Archive') }}
                                </a>
                            </li>

                            @if (in_array(auth()->user()->user_level,['admin','master-agent','sub-agent','gold-agent']))
                                <li class="nav-item">
                                    <a class="nav-link text-white"
                                       href="{{ auth()->user()->user_level == 'admin' ? route('admin-edit-comission'): route('agent-comission-edit') }}">
                                        {{ __('Commission Editting') }}
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>
            </ul>
            <!-- Divider -->
            <hr class="my-3">
            <!-- Heading -->
            <h6 class="navbar-heading text-muted">{{ __('Users') }}</h6>
            <!-- Navigation -->

            @if (auth()->user()->user_level != 'admin')
                <ul class="navbar-nav mb-md-3">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('active-players') }}">
                            <i class="fa fa-users"></i> {{ __('Active Players') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('user-approval') }}">
                            <span><i class="fa fa-user-plus"></i>{{ __('For Approval') }}</span><span
                                style="background-color: red"
                                class="badge text-white ml-3">{{count(auth()->user()->user_approval)}}</span>
                        </a>
                    </li>
                    @if (in_array(auth()->user()->user_level,['master-agent','sub-agent', 'gold-agent']))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('agent-list') }}">
                                <i class="fa fa-project-diagram"></i> {{ __('Agents List') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('transaction.log') }}">
                                <i class="fa fa-users"></i> Transactions Log
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('deactivated-players') }}">
                            <i class="fa fa-user-times"></i> {{ __('Deactivated') }}
                        </a>
                    </li>
                </ul>
            @else
                <ul class="navbar-nav mb-md-3">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin-active-players') }}">
                            <i class="fa fa-users"></i> {{ __('Active Players') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin-user-approval') }}">
                            <i class="fa fa-user-plus"></i> {{ __('For Approval') }}<span style="background-color: red"
                                                                                          class="badge text-white ml-3">{{count(auth()->user()->user_approval)}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('sub-operator-list') }}">
                            <i class="fa fa-project-diagram"></i>{{ __('Operator List') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('master-agent-list') }}">
                            <i class="fa fa-project-diagram"></i>{{ __('Sub Operator List') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('gold-agent-list') }}">
                            <i class="fa fa-project-diagram"></i>{{ __('Master Agent List') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('silver-agent-list') }}">
                            <i class="fa fa-project-diagram"></i>{{ __('Gold Agent') }}
                        </a>
                    </li>
                    {{--<li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('bronze-agent-list') }}">
                            <i class="fa fa-project-diagram"></i>{{ __('Bronze Agents List') }}
                        </a>
                    </li>--}}
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin-deactivated-players') }}">
                            <i class="fa fa-user-times"></i>{{ __('Deactivated') }}
                        </a>
                    </li>
                </ul>
                <h6 class="navbar-heading text-muted">{{ __('Admin Settings') }} </h6>
                <ul class="navbar-nav mb-md-3">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('round-viewer') }}">
                            <i class="fa fa-coins"></i>{{ __('Game Rounds Viewer') }}
                        </a>
                    </li>
                    {{--<li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('site-settings') }}">
                            <i class="fa fa-cog"></i>{{ __('Site Settings') }}
                        </a>
                    </li>--}}
                    @if(auth()->user()->user_level == 'super-admin')
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('site-settings') }}">
                                <i class="fa fa-cog"></i>{{ __('Add Video') }}
                            </a>
                        </li>
                    @endif
                </ul>

            @endif
            @if (auth()->user()->user_level != 'admin')
                <!-- Divider -->
                <hr class="my-3">
                <!-- Heading -->
                <h6 class="navbar-heading text-muted">{{ __('Player Deposit/ Withdraw') }}</h6>
                <!-- Navigation -->
                <ul class="navbar-nav mb-md-3">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('withdraw-request') }}">
                            <i class="fa fa-funnel-dollar"></i>{{ __('Withdrawals') }} <span
                                style="background-color: red"
                                class="badge text-white ml-3">{{count(auth()->user()->transactions->where('type','wallet')->where('transaction_type','withdraw')->where('transaction_status','pending'))}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('withdraw-request-history') }}">
                            <i class="fa fa-receipt"></i>{{ __('Request History') }}
                        </a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</nav>
