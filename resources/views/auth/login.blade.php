@extends('auth.login-layout')

@section('content')

    <div class="row">
        <div class="col-md-12 col-xs-12"><img src="{{url('resources/assets/images/logo.png')}}" border="0" alt=""/></div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12 loginshape" id="loginpart">
            <div class="col-md-12 col-xs-12 toppart text-center">
                Login To Your Account
            </div>
            <div class="col-md-12 col-xs-12 text-center">

                @if(Session::has('message'))
                    <div class="alert alert-{{Session::get('alert')}} progress-bar-striped">
                        {{ Session::get('message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger form-errors">
                        <ul>
                            {{ implode('', $errors->all(':message')) }}
                        </ul>
                    </div>
                @endif


                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                    {!! csrf_field() !!}


                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <input id="login-username" type="email" class="form-control" name="email" value="@if(isset($_COOKIE['email'])) {{$_COOKIE['email']}} @endif" placeholder="username@company">
                </div>

                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input id="login-password" type="password" class="form-control" name="password" placeholder="password" value="@if(isset($_COOKIE['pass'])) {{$_COOKIE['pass']}} @endif">
                </div>


                <div class="input-group">
                    <div class="checkbox">
                        <label>
                            <input id="login-remember" type="checkbox" name="remember" value="1"> <span class="checklable">Remember me</span>
                        </label>
                    </div>
                </div>


                <div class="form-group">

                    <div class="col-sm-7 text-left"><br>
                        <a href="javascript:;" id="forgeturpass" class="checklable">Forget Password?</a>


                    </div>
                    <!-- Button -->
                    <div class="col-sm-5 controls text-right">
                        <button class="btn btn-primary buttonAllSite" type="submit">Login</button>


                    </div>
                </div>

              </form>


            </div>
        </div><!-- ./login Part -->

        <div class="col-md-12 col-xs-12 loginshape" id="forgetpart">
            <div class="col-md-12 col-xs-12 toppart text-center">
                Forget Your Password

            </div>
            <div class="col-md-12 col-xs-12 text-center">

<form>
                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                        <input id="login-username" type="text" class="form-control" name="identifier" value="" placeholder="username or email">
                    </div>


                    <div style="margin-top:70px" class="form-group">

                        <!-- Button -->
                        <div class="col-sm-12 controls text-right">
                            <button class="btn btn-primary buttonAllSite2" type="submit">Resend Password</button>


                        </div>
                    </div>


                </form>


            </div>
        </div>

        <div class="col-md-12 col-xs-12 text-left loginfooterpart"><img src="{{url('resources/assets/images/loginbutton.png')}}" border="0" alt=""/></div>
    </div>

@endsection
