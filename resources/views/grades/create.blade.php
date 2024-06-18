@extends('layouts.master')

@section('title', 'Create Grade')

@section('css')
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
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
                                    <input type="text" style="pointer-events: none" id="teacher" name="teacher" class="form-control bg-light" required value="{{ auth()->user()->fullname }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Subject</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" id="subject" name="subject" class="form-control bg-light" required value="{{ auth()->user()->subject->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Classes</label>
                                </div>
                                <div class="col-md-9">
                                    <select id="classesInput" class="form-control SlectBox" >
                                            <option value="">Select The Class</option>
                                            @foreach ($classes as $class)
                                                <option value={{$class->id}}>{{$class->name}}</option>
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
                                    <select id="studentSearchInput" class="form-control SlectBox">
                                    </select>
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
        // Initialize Select2 for student search input

        $('#studentSearchInput').select2({
            placeholder: 'Select The student', // Placeholder text
            ajax: {
                url: '{{route('students.search')}}', // Replace with your API endpoint
                dataType: 'json',
                method: 'POST',
                data: function (params) {
                    var query = {
                        search: params.term || '',
                        class_id: $('#classesInput').val()
                      };
                    console.log(query);

                    return query;
                },
                processResults: function (data) {
                    console.log(data)
                    return {
                        results: data.students.map(function(user) {
                            return { id: user.id, text: user.first_name + ' ' + user.last_name };
                        })
                    };
                },
                cache: false
            }
        });

        $('#classesInput').on('change', function() {
            // Trigger Select2 to fetch data based on the selected class
            $('#studentSearchInput').val(null).trigger('change');
        });
    });
@endsection