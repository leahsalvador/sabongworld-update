<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-default" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main"
                aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0"
           href="{{ auth()->user()->user_level == 'admin' ? route('admin-dashboard') : route('dashboard') }}">
            {{-- <img src="//logo.clearbit.com/clearbit.com?size=50" class="navbar-brand-img" alt="..."> --}}
            <h1 class="text-white">{{env('APP_NAME')}}</h1>
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

                        <a href="{{ route('superadmin') }}">
                            <img
                                src="https://www.clipartmax.com/png/full/123-1237090_these-are-some-cats-avatar-i-drew-during-my-free-time-animated.png">
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
            <!-- Divider -->
            <hr class="my-3">
            <!-- Heading -->
            <h6 class="navbar-heading text-muted">General</h6>
            <!-- Navigation -->
            <ul class="navbar-nav mb-md-3 ">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{route('sadmin.archive.game')}}">
                        <i class="fa fa-history"></i> {{ __('Games Archive') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{route('sadmin.archive.betting')}}">
                        <i class="fa fa-history"></i> {{ __('Betting Archive') }}
                    </a>
                </li>
<!--                <li class="nav-item">
                    <a class="nav-link text-white" href="{{route('sadmin.archive.winner')}}">
                        <i class="fa fa-history"></i> {{ __('Winner Archive') }}
                    </a>
                </li>-->
            </ul>

            <!-- Divider -->
            <hr class="my-3">
            <!-- Heading -->
            <h6 class="navbar-heading text-muted">Users</h6>
            <!-- Navigation -->
            <ul class="navbar-nav mb-md-3">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin-active-players') }}">
                        <i class="fa fa-users"></i> Active Players
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('sadmin.agent.operator') }}">
                        <i class="fa fa-project-diagram"></i>{{ __('Operators') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('sadmin.agent.sub.operator') }}">
                        <i class="fa fa-project-diagram"></i>{{ __('Sub Operators') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('sadmin.agent.master') }}">
                        <i class="fa fa-project-diagram"></i>{{ __('Master Agents') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('sadmin.agent.gold') }}">
                        <i class="fa fa-project-diagram"></i>{{ __('Gold Agents') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin-deactivated-players') }}">
                        <i class="fa fa-user-times"></i> Deactivated
                    </a>
                </li>
            </ul>
            <h6 class="navbar-heading text-muted">Admin Settings</h6>
            <ul class="navbar-nav mb-md-3">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin-round-viewer') }}">
                        <i class="fa fa-coins"></i> Game Rounds Viewer
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin-site-settings') }}">
                        <i class="fa fa-cog"></i>
                        {{ __('Add Video') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
