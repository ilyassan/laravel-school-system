@extends('layouts.master')
@section('title', 'Show Grade')

@section('page-header')
    <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div class="my-auto">
                <div class="d-flex">
                    <h4 class="content-title mb-0 my-auto">Grades</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Edit Grade</span>
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
                    <form action="{{route('grades.update', $grade->id)}}" class="form-horizontal" method="POST">
                        @csrf
                        @method("PATCH")

                        <div class="mb-4 main-content-label">Grade Info</div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Grade</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="grade" name="grade" class="form-control" value="{{ $grade->grade }}" step="0.1" min="0" max="20" required>
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
                        <div class="card-footer text-left pl-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update Grade</button>
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
            var $emailInput = $('#grade');

            if ($emailInput.length) {
                $emailInput.focus();
                var length = $emailInput.val().length;
                $emailInput[0].setSelectionRange(length, length);
            }
        });
    </script>
@endsection

@section('tag-js')
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
@endsection