@extends('layouts.master')

@section('title', 'Create Grade')

@section('css')
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">Grades</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Create Grade</span>
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
                    <form action="{{ route('grades.store') }}" class="form-horizontal" method="POST">
                        @csrf
                        @method('POST')
                    
                        <!-- Form fields -->
                        <div class="mb-4 main-content-label">Grade Data</div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Teacher</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" id="teacher" class="form-control bg-light" required value="{{ auth()->user()->fullname }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Subject</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" id="subject" class="form-control bg-light" required value="{{ auth()->user()->subject->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Classes</label>
                                </div>
                                <div class="col-md-9">
                                    <select id="classesInput" name="class_id" class="form-control SlectBox" required>
                                            <option value="">Select The Class</option>
                                            @foreach ($classes as $class)
                                                <option value={{$class->id}} {{old('class_id') == $class->id ? 'selected' : ''}}>{{$class->name}}</option>
                                            @endforeach
									</select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Student</label>
                                </div>
                                <div class="col-md-9">
                                    <select id="studentSearchInput" name="student_id" class="form-control SlectBox" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Grade</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" name="grade" class="form-control" step="0.1" min="0" max="20" required>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-left pl-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Create Grade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
@endsection

@section('tag-js')
    $(document).ready(function() {
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Select2 for student search input
        $('#studentSearchInput').select2({
            placeholder: 'Select The student', // Placeholder text
            ajax: {
                url: '{{ route('students.search') }}',
                dataType: 'json',
                method: 'POST',
                data: function (params) {
                    var query = {
                        search: params.term || '',
                        class_id: $('#classesInput').val()
                    };

                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.students.map(function(user) {
                            return { id: user.id, text: user.first_name + ' ' + user.last_name };
                        })
                    };
                },
                cache: true
            }
        });
    });
    @if ($errors->any())
    Swal.fire({
        title: 'Invalid Data',
        text: '{{ $errors->first() }}', // Use $errors->first() to get the first error message
        icon: 'warning',
        confirmButtonText: 'Ok',
        customClass: {
            confirmButton: 'btn btn-primary'
        }
    });
@endif
@if (Session::has('message'))
    Swal.fire({
        title: 'Message',
        text: '{{ Session::get('message') }}',
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary'
        }
    });
    {{Session::forget('message')}}
@endif
@endsection