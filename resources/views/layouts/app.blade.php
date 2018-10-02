<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('layouts.header')
</head>
<body>
    <div id="app">
        @include('layouts.navigation')

        @if(Session::has('flash_notification'))
            <div class="container">
                <div class="row mt-4">
                    <div class="col-sm-12 col-md-8 offset-md-2">
                        @include('flash::message')
                    </div>
                </div>
            </div>
        @endif

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('script')
    <script>
        $(document).ready(function(){
            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        })
    </script>
</body>
</html>