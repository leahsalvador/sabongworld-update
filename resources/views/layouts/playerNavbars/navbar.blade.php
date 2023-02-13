@auth()
    @include('layouts.playerNavbars.navs.auth')
@endauth
    
@guest()
    @include('layouts.playerNavbars.navs.guest')
@endguest