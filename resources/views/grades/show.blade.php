@extends('layouts.master')
@section('title', 'Show Grade')

@section('page-header')
    <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div class="my-auto">
                <div class="d-flex">
                    <h4 class="content-title mb-0 my-auto">Grades</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Show Grade</span>
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
                    <div class="form-horizontal">
                        <div class="mb-4 main-content-label">Teacher</div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">First Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->teacher->getFirstName() }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Last Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->teacher->getLastName() }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Subject</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->teacher->subject->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 main-content-label">Student</div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">First Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->student->getFirstName() }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Last Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->student->getLastName() }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Class</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->student->class->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 main-content-label">Grade Info</div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Grade</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->grade }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Created At</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->getCreatedAtDetailsFormated() }}">
                                </div>
                            </div>
                        </div>
                        @if ($grade->updated_at != $grade->created_at && isset($grade->updated_at))
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3 d-flex align-items-center">
                                        <label class="form-label m-0">Updated At</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" style="pointer-events: none" class="form-control bg-light" value="{{ $grade->getUpdatedAtDetailsFormated() }}">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->isTeacher())
                            <div class="card-footer text-left pl-0 d-flex align-items-start">
                                <a href="{{route('grades.edit', $grade->id)}}" class="btn btn-primary waves-effect waves-light mr-5">Edit Grade</a>
                                <form id="deleteGradeForm" action="{{route('grades.destroy', $grade->id)}}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button onclick="handleSubmit(event)" type="button" class="btn btn-danger waves-effect waves-light">Delete Grade</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
	function handleSubmit(e){
        
        Swal.fire({
            title: 'Warning',
            text: 'Are you sure you want to delete this grade ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
			customClass: {
				confirmButton: 'btn btn-danger btn-lg',
				icon: 'text-danger border-danger',
			},
        })
        .then((result) => {
			if (result.isConfirmed) {
                const form = document.querySelector('#deleteGradeForm');
				form.submit();
			}
		});
	}
</script>

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

@if (Session::has('warning'))
    @section('tag-js')
        Swal.fire({
            title: 'Warning',
            text: '{{ Session::get("warning") }}',
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    @endsection
    {{ Session::forget("warning") }}
@endif

@if (Session::has('error'))
    @section('tag-js')
        Swal.fire({
            title: 'Message',
            text: '{{ Session::get("error") }}',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    @endsection
    {{ Session::forget("error") }}
@endif