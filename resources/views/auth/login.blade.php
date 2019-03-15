@extends('la.layouts.auth')

@section('htmlheader_title')
    ログイン
@endsection

@section('content')
<body class="hold-transition login-page">
    <div class="login-box">
        <div style="text-align: center; margin-bottom:50px;">
            <img width="80%" src="{{ asset('la-assets/img/logo.png') }}" alt="Rose Group" />
        </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>おっと!</strong> 入力に問題がありました.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="login-box-body">
    <p class="login-box-msg">サインインしてください。</p>
    <form action="{{ url('/login') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Eメール" name="email"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="パスワード" name="password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" name="remember"> Eメール・パスワードを保存する
                    </label>
                </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">サインイン</button>
            </div><!-- /.col -->
        </div>
    </form>

    @include('auth.partials.social_login')

    <!-- <a href="{{ url('/password/reset') }}">I forgot my password</a><br><br> -->
    <!-- <a href="{{ url('register') }}">Don't Have Account? Register</a><br> -->
    <!--<a href="{{ url('/register') }}" class="text-center">Register a new membership</a>-->

</div><!-- /.login-box-body -->

</div><!-- /.login-box -->

    @include('la.layouts.partials.scripts_auth')

    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

@endsection
