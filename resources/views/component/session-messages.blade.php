
{{-- if session has flashMessages --}}
@if(session()->has('flashMessages'))
    {{-- if session has success  --}}
    @if(count(session('flashMessages')['success']) > 0)
        @foreach(session('flashMessages')['success'] as $message)
            <script>
                $.toast({
                    heading: 'Success',
                    text: "{!!$message!!}",
                    icon: 'success',
                    loader: true,        // Change it to false to disable loader
                    hideAfter : 15000,
                    position: 'top-right',
                });
                </script>
        @endforeach
    @endif
    {{--end if session has success  --}}
    {{-- if session has error  --}}
    @if(count(session('flashMessages')['error']) > 0)
        @foreach(session('flashMessages')['error'] as $message)
            <script>
                $.toast({
                    heading: 'Error',
                    text: "{!!$message!!}",
                    icon: 'error',
//                    loaderBg: '#9EC600',  // To change the background,
                    hideAfter : 15000,
                    position: 'top-right',
                });
            </script>
        @endforeach
    @endif
    {{--end if session has error  --}}
    {{-- if session has warning  --}}
    @if(count(session('flashMessages')['warning']) > 0)
        @foreach(session('flashMessages')['warning'] as $message)
            <script>
                $.toast({
                    heading: 'Information',
                    text: "{!!$message!!}",
                    icon: 'info',
                    loader: true,        // Change it to false to disable loader
                    loaderBg: '#9EC600',  // To change the background,
                    hideAfter : 15000,
                    position: 'top-right',
                });
            </script>
        @endforeach
    @endif
    {{--end if session has warning  --}}
    {{-- if session has info  --}}
    @if(count(session('flashMessages')['info']) > 0)
        @foreach(session('flashMessages')['info'] as $message)
            <script>
                $.toast({
                    heading: 'Information',
                    text: "{!!$message!!}",
                    icon: 'info',
                    loader: true,        // Change it to false to disable loader
                    loaderBg: '#9EC600',  // To change the background,
                    hideAfter : 15000,
                    position: 'top-right',
                });
            </script>
        @endforeach
    @endif
    {{--end if session has info  --}}
@endif
{{-- end if session has flashMessages --}}
