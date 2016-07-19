    {{ HTML::style('assets/back/css/bootstrap.min.css') }}
    {{ HTML::style('assets/css/font-awesome.min.css') }}
    {{ HTML::style('assets/back/css/custom.css') }}
    {{ HTML::style('assets/back/css/custom.css') }}
    {{ HTML::script('assets/js/jquery.min.js') }}

    @if(Session::has('direction') && Session::get('direction')=='rtl')
        {{ HTML::style('assets/back/css/rtl.css') }}
    @else
        {{ HTML::style('assets/back/css/ltr.css') }}
    @endif
    
    <div class="row">
        @yield('head')
    </div> 
    <div>
		@yield('main')
    </div>
    
    <!-- script -->
    {{ HTML::script('assets/js/bootstrap.min.js') }}
    {{ HTML::script('assets/back/js/script.js') }}
    {{ HTML::script('assets/back/js/respond.min.js') }}