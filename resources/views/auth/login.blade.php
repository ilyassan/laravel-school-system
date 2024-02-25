@extends('layouts.master2')

@section('title', 'Login')

@section('css')
<!-- Sidemenu-respoansive-tabs css -->
<link href="{{ URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row no-gutter">
        <!-- The content half -->
        <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
            <div class="login d-flex align-items-center py-2">
                <!-- Demo content-->
                <div class="container p-0">
                    <div class="row">
                        <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                            <div class="card-sigin">
                                <div class="mb-5 d-flex">
                                </div>
                                @if (Session::has('unactive'))
                                <div class="alert alert-danger">{{ Session::get('unactive') }}</div>
                                @endif
                                <div class="card-sigin">
                                    <div class="main-signup-header">
                                        <h2>Welcome</h2>
                                        <h5 class="font-weight-semibold mb-4">Sign in to your account</h5>
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>Email / Phone</label>
                                                <input id="emailOrPhone" type="text" class="form-control @error('emailOrPhone') is-invalid @enderror" name="emailOrPhone" value="{{ old('emailOrPhone') }}" required autocomplete="email" autofocus>
                                                @error('emailOrPhone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label>Password</label>

                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" minlength="8">

                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                                <div class="form-group mt-3">
                                                    <div class="form-check p-0">
                                                        <label class="form-check-label" for="remember">
                                                            Remember me
                                                        </label>
                                                        <input class="form-check-input ml-1" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">
                                                Sign In
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End -->
            </div>
        </div><!-- End -->

        <!-- The image half -->
        <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
            <div class="row wd-100p mx-auto text-center">
                <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                    <img src="https://res.cloudinary.com/dryaaexki/image/upload/v1656586814/invoice/login_img_d02hmc.jpg" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection