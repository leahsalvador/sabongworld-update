@auth()
    @include('layouts.agentNavbars.navs.auth')
@endauth
    
@guest()
    @include('layouts.agentNavbars.navs.guest')
@endguest