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
                    <form class="form-horizontal">
                        <div class="mb-4 main-content-label">Name</div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">First Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->first_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Last Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->last_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 main-content-label">Contact Info</div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Email</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->email}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Phone</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->phone}}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 main-content-label">School Info</div>
                        @if (auth()->user()->isStudent())
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Role</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="Student">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Class</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->class->name}}">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->isTeacher())
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Role</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="Teacher">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Subject</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->subject->name}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Classes ({{auth()->user()->classes->count()}})</label>
                                    </div>
                                    <div class="col-md-9">
                                        @php
                                            $classes = "";
                                            foreach (auth()->user()->classes as $class) {
                                                $classes .= "$class->name, ";
                                            }
                                            $classes = rtrim($classes, ', ');
                                        @endphp
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $classes }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Salary</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{auth()->user()->salary}} DH">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->isAdmin())
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Role</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="Admin">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="card-footer text-left pl-0">
                            <a href="{{route('profile.edit')}}" class="btn btn-primary waves-effect waves-light">Edit Profile</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@if (Session::has('success'))
    @section('tag-js')
        Swal.fire({
            title: 'Message',
            text: '{{ Session::get("success") }}',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    @endsection
    {{ Session::forget("success") }}
@endif