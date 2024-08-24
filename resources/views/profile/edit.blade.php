@extends('layouts.master')
@section('title', 'Edit Profile')

@section('css')
    <link href="{{URL::asset('assets/css/image-input.css')}}" rel="stylesheet">
@endsection

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
                    <form action="{{ route('profile.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        @php
                            $user = auth()->user();
                        @endphp
                        <!-- Form fields -->
                        <div class="mb-4 main-content-label">Profile Image</div>
                        <div class="form-group">
                            <div class="row justify-content-center">
                                <div class="profile-pic" style="z-index: 1">
                                    <label class="-label" for="file" style="margin: 0%">
                                      <span class="glyphicon glyphicon-camera"></span>
                                      <span>Change Image</span>
                                    </label>
                                    <input id="file" type="file" name="image" onchange="loadFile(event)"/>
                                    <img src="{{ $user->getImage() }}" id="output" width="200" />
                                  </div>
                            </div>
                            @error('image')
                                <div class="text-center mt-2"><small class="text-danger mt-1">{{$message}}</small></div>
                            @enderror
                        </div>
                        <div class="mb-4 main-content-label">Contact Info</div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Email</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="email" name="email" class="form-control" required value="{{ old('email', $user->getEmail() ) }}">
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
                                    <input type="text" id="phone" name="phone" class="form-control" required value="{{ old('phone', $user->getPhone() ) }}">
                                    @error('phone')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Bio</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="bio" name="bio" class="form-control" required value="{{ old( 'bio', $user->getBio() ) }}" maxlength="150">
                                    @error('bio')
                                        <small class="text-danger mt-1">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-left pl-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update Profile</button>
                            <a href="{{route('profile.show-reset-password')}}" class="btn btn-primary waves-effect waves-light ml-5">Reset Password</a>
                            <a href="{{route('profile.reset-image')}}" class="btn btn-primary waves-effect waves-light ml-5">Reset Image</a>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(function(){
            var $emailInput = $('#email');

            if ($emailInput.length) {
                $emailInput.focus();
                var length = $emailInput.val().length;
                $emailInput[0].setSelectionRange(length, length);
            }
        });

        var loadFile = function (event) {
            var image = document.getElementById("output");
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>
@endsection