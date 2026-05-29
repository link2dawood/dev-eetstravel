{{-- Legacy alias kept in case anything still resolves auth.login directly.
     LoginController actually returns auth.simple-login. Both share the
     same chrome via auth/layout.blade.php. --}}
@include('auth.simple-login')
