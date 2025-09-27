@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Notifications', 'sub_title' => $user->name,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Show', 'route' => null]]])
    {{-- modal for service table --}}
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                
                        @include('component.list_notifications_profile', ['userName' => $user->name, 'userId' => $user->id])
                        
                    
                
            </div>
        </div>
    </section>
@endsection

@section('post_scripts')
    <script src="{{asset('js/profile.js')}}"></script>
@endsection