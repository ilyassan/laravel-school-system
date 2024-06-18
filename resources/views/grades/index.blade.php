@extends('layouts.master')

@section('title', 'Grades Table')

@section('css')
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">Grades</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Grades Table</span>
        </div>
    </div>
</div>

<!-- /breadcrumb -->
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">GRADES TABLE</h4>
                    <i class="mdi mdi-dots-horizontal text-gray"></i>
                </div>
                <p class="tx-12 tx-gray-500 mb-2">All The Grades Entered To The System By Teachers.</p>
            </div>
            <div class="card-body">
                <form class="d-flex align-items-center" style="gap: 30px">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center" style="gap: 5px">
                                <div class="dataTables_length col-sm-3 px-0" id="example1_length">
                                    <select name="per-page" class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value={{null}}>Per Page</option>
                                        <option value="10" {{request()->get('per-page') == 10 ? 'selected' : ''}}>10</option>
                                        <option value="50" {{request()->get('per-page') == 50 ? 'selected' : ''}}>50</option>
                                        <option value="100" {{request()->get('per-page') == 100 ? 'selected' : ''}}>100</option>
                                    </select>
                                </div>
        
                                <div class="dataTables_length col-sm-3 px-0" id="example1_length">
                                    <select name="subject" class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value={{null}}>Select A Subject</option>
                                        @foreach($subjects as $suject)
                                            <option value="{{$suject->id}}" {{request()->get('subject') == $suject->id ? 'selected' : ''}}>{{$suject->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div id="example1_filter" class="dataTables_filter col-sm-6 px-0">
                                    <input type="search" name="keyword" value="{{request()->get('keyword')}}" class="form-control form-control-sm" placeholder="Class, Student's name, Teacher's name">
                                </div>
                            </div>
    
                            <div class="d-flex align-items-center mt-2" style="gap: 10px">
                                <div class="input-group col-sm-6 px-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                        </div>
                                    </div><input name="from-date" class="form-control fc-datepicker" placeholder="From" type="text" value="{{ request()->get('from-date') }}" autocomplete="off">
                                </div>
                                <div class="input-group col-sm-6 px-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                        </div>
                                    </div><input name="to-date" class="form-control fc-datepicker" placeholder="To" type="text" value="{{ request()->get('to-date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary py-1">Filter</button>
                        </div>
                </form>
                <div class="table-responsive my-3">
                    <table class="table text-md-nowrap" id="example1">
                        <thead>
                            <tr>
                                <th class="wd-5p border-bottom-0">#</th>
                                <th class="wd-20p border-bottom-0">Teacher</th>
                                <th class="wd-20p border-bottom-0">Subject</th>
                                <th class="wd-20p border-bottom-0">Student</th>
                                <th class="wd-5p border-bottom-0">Class</th>
                                <th class="wd-20p border-bottom-0">Entered date</th>
                                <th class="wd-10p border-bottom-0">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grades as $grade)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $grade->teacher->fullname }}</td>
                                <td>{{ $grade->teacher->subject->name }}</td>
                                <td>{{ $grade->student->fullname }}</td>
                                <td>{{ $grade->student->class->name }}</td>
                                <td>{{ $grade->created_at->format('m/d/Y') }}</td>
                                <td>{{ $grade->grade }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{$grades->withQueryString()->links()}}
            </div>
        </div>
    </div>
</div>
@endsection
 


@if ($invalidFilter)
    @section('tag-js')
        swal({
        title: 'Invalid Filter',
        text: '{{$invalidFilter[0]}}',
        icon: 'warning',
        button: 'Ok',
        })
    @endsection
@endif

@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>

    <script src="{{URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>
@endsection
