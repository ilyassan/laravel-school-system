@extends('layouts.master')
@section('title', 'Profile')

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Profile</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $user->fullname }}</span>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="pl-0">
                        <div class="main-profile-overview">
                            <div class="d-flex justify-content-center">
                                <div class="main-img-user profile-user mb-5">
                                    <img alt="" src="{{ $user->image }}"><span class="fas fa-camera profile-edit text-primary"></span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mg-b-20">
                                <div>
                                    <h5 class="main-profile-name">{{ $user->fullname }}</h5>
                                    <p class="main-profile-name-text">{{ $user->getRoleName() }}</p>
                                </div>
                            </div>
                            <h6>Bio</h6>
                            <div class="main-profile-bio">
                                {{ $user->getBio() }}
                            </div>
                            <hr class="mg-y-30">
                            <label class="main-content-label tx-13 mg-b-20">Contact Info</label>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Email</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Phone</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control bg-light" value="{{ $user->phone}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <label class="main-content-label tx-13 mg-b-20">Generale Info</label>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Gender</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control bg-light" value="{{ $user->getGender() }}" readonly>
                                    </div>
                                </div>
                            </div>
                            @if ($user->isTeacher())
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-center">
                                            <label class="form-label m-0">Subject</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control bg-light" value="{{ $user->subject->name}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-center">
                                            <label class="form-label m-0">Classes ({{ $user->classes->count()}})</label>
                                        </div>
                                        <div class="col-md-9">
                                            @php
                                                $classes = "";
                                                foreach ($user->classes as $class) {
                                                    $classes .= "$class->name, ";
                                                }
                                                $classes = rtrim($classes, ', ');
                                            @endphp
                                            <input type="text" class="form-control bg-light" value="{{ $classes }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($user->isStudent())
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-center">
                                            <label class="form-label m-0">Class</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control bg-light" value="{{ $user->class->name}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($user->isStudent())
                                <hr class="mg-y-30">
                                <h6>Subjects Level</h6>

                                @php
                                    $subjectsAndAverageGrades = $user->avgGradesOfSubjects();
                                    
                                    foreach ($subjectsAndAverageGrades as $key => $avgGrade) {
                                        $subjectsAndAverageGrades[$key] = ($avgGrade / 20 * 100); // Change the range of the grade from 0->20 to 0->100
                                    }
                                    $barsColor = ['primary', 'danger', 'success', 'info', 'dark', 'warning', 'secondary', 'muted'];
                                @endphp

                                @foreach ($subjectsAndAverageGrades as $subject => $avgGrade)
                                    <div class="skill-bar {{ ! $loop->last ? 'mb-4': '' }} clearfix {{$loop->first ? 'mt-3': ''}}">
                                        <span>{{ $subject }}</span>
                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-{{ $barsColor[$loop->index] }}-gradient" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: {{$avgGrade}}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection