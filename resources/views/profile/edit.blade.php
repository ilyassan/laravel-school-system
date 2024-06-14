@extends('layouts.master')
@section('title', 'Profile')

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, Welcome Back!</h2>
						  <p class="mg-b-0">Profile</p>
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
                    <form action="{{ route('profile.update') }}" class="form-horizontal" method="POST">
                        @csrf
                        @method('PATCH')
                    
                        <!-- Form fields -->
                        <div class="mb-4 main-content-label">Contact Info</div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Email</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="email" name="email" class="form-control" required value="{{ old('email', auth()->user()->email) }}">
                                    @error('email')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Phone</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="phone" name="phone" class="form-control" required value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-left pl-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update Profile</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function(){
            var $emailInput = $('#email');

            if ($emailInput.length) {
                $emailInput.focus();
                var length = $emailInput.val().length;
                $emailInput[0].setSelectionRange(length, length);
            }
        });
    </script>
@endsection