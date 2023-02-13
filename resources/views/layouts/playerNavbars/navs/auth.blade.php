<nav class="navbar navbar-horizontal navbar-expand-lg navbar-dark bg-default" style="height: 3rem;">
    <div class="container">
        <a class="navbar-brand" href="{{ route('player') }}">
            <img src="{{config('settings.img.logo')}}" class="img-fluid"> {{env('APP_NAME')}}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-default"
                aria-controls="navbar-default" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar-default">
            <div class="navbar-collapse-header">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('player') }}">
                            {{ config('app.name') }}
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                                data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false"
                                aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <ul class="navbar-nav ml-lg-auto">
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active text-white" href="{{route('player')}}"> <i
                            class="fa fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active text-white" href="#" data-toggle="modal"
                       data-target="#modal-notification"> <i class="fa fa-key"></i> Rules</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active text-white" href="{{ route('player-withdraw') }}"><i
                            class="fa fa-credit-card"></i> Withdraw</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ni ni-settings-gear-65"></i> Settings
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('betting-history') }}">Betting History</a>
                        <a class="dropdown-item" href="{{ route('player-withdraw') }}">Withdraw</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">Change Password</a>
                        <a class="dropdown-item" href="{{ route('profile.settings') }}">Account Details</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </li>
            </ul>

        </div>
    </div>
    <div class="modal fade" id="modal-notification" tabindex="-1" role="dialog"
         aria-labelledby="modal-notification" aria-hidden="true">
        <div class="modal-dialog modal-default modal-dialog-centered modal-" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="py-3 text-center">
                        <i class="ni ni-bell-55 ni-3x"></i>
                        <h4 class="heading mt-4">RULES AND GUIDELINES</h4>
                        <ol class="text-left">
                            <li>Naiwan naka open bet habang naglalaban = CANCEL</li>
                            <li>Closed betting na at nawalan bigla ng video = CONTINUE</li>
                            <li>Hindi parehas fight number sa video at betting button = CANCEL</li>
                            <li>120 and below payout = CANCEL</li>
                            <li>Bawal farmer = BAN ACCOUNT</li>
                            <li>Early closing para maiwasan ang dayaan</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-white ml-auto"
                            data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
