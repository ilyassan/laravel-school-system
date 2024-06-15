@extends('layouts.master')
@section('title', 'Reset Password')

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, Welcome Back!</h2>
						</div>
					</div>
				</div>
				<!-- /breadcrumb -->
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.reset-password') }}" class="form-horizontal" method="POST">
                        @csrf
                        @method('PATCH')
                    
                        <!-- Form fields -->
                        <div class="mb-4 main-content-label">Reset Password</div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Current Password</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="password" id="password" name="password" class="form-control" required>
                                    @error('password')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">New Password</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                                    @error('new_password')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Confirm New Password</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                                    @error('new_password_confirmation')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-left pl-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Reset Password</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
@if (Session::has('message'))
    @section('js')
        <script type="text/javascript">
            swal({
                title: 'Message',
                text: '{{Session::get("message")}}',
                icon: 'success',
                buttons: {
                    confirm: {
                        text: 'OK',
                        className: 'btn btn-primary'
                    }
                }
            })
        </script>
    @endsection
@endif