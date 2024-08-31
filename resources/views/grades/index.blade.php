@extends('layouts.master')

@section('title', 'Grades Table')

@section('css')
    <!-- Internal Data table css -->
    @vite('resources/js/app.js')
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
                <p class="tx-12 tx-gray-500 mb-2">
                    @if (auth()->user()->isAdmin())
                        All The Grades Entered To The System By Teachers.
                    @endif
                    @if (auth()->user()->isTeacher())
                        All The Grades You Entered The System.
                    @endif
                    @if (auth()->user()->isStudent())
                        All Your Grades Entered By Your Teacher.
                    @endif
                </p>
            </div>
            <div class="card-body">
                <form id="filterForm" class="d-flex align-items-center mb-4" style="gap: 30px">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center" style="gap: 5px">
                                <div class="dataTables_length col-sm-3 px-0" id="example1_length">
                                    <select name="per-page" class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value={{null}}>Per Page</option>
                                        <option value="10" {{request()->get('per-page') == 10 || ! request()->get('per-page') ? 'selected' : ''}}>10</option>
                                        <option value="50" {{request()->get('per-page') == 50 ? 'selected' : ''}}>50</option>
                                        <option value="100" {{request()->get('per-page') == 100 ? 'selected' : ''}}>100</option>
                                    </select>
                                </div>
        
                                <div class="dataTables_length col-sm-3 px-0" id="example1_length">
                                    <select name="subject" class="custom-select custom-select-sm form-control form-control-sm pr-1">
                                        <option value={{null}}>Select A Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{$subject->id}}" {{request()->get('subject') == $subject->id ? 'selected' : ''}}>{{$subject->name}}</option>
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
                        <div class="d-flex flex-1 justify-content-between">
                            <div class="d-flex flex-column mr-4" style="gap: 10px">
                                <button type="submit" formaction="{{ route('grades.index') }}" class="btn btn-primary py-1">Filter</button>
                                <a href="{{route('grades.index')}}" class="btn btn-primary py-1">Reset</a>
                            </div>
                            <div class="d-flex flex-column" style="gap: 10px">
                                <button type="button" onclick="handleExport()" class="btn btn-success py-1">Export to EXCEL</button>
                            </div>
                        </div>

                </form>
                <div class="table-responsive my-3">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="wd-5p border-bottom-0">#</th>
                                @php
                                    $headers = [
                                        'Teacher' => ['field' => 'teacher', 'width' => '20'],
                                        'Subject' => ['field' => 'subject', 'width' => '15'],
                                        'Student' => ['field' => 'student', 'width' => '20'],
                                        'Class' => ['field' => 'class', 'width' => '15'],
                                        'Grade' => ['field' => 'grade', 'width' => '20'],
                                        'Entered Date' => ['field' => 'entered_date', 'width' => '15'],
                                    ];
                                @endphp
                                @foreach ($headers as $key => $value)
                                    <th class="wd-{{$value['width']}}p border-bottom-0 sortable align-middle" style="cursor: pointer" onclick="sort(event)" data-value="{{$value['field']}}">
                                        {{$key}}
                                        @if (request()->get('order-by') == $value['field'])
                                            <span class="mdi mdi-chevron-{{ request()->get('sort') == 'asc' ? 'up' : 'down' }}"></span>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>                      
                        </thead>
                        <tbody>
                            @if ($grades->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center">No Grades To Show</td>
                                </tr>
                            @endif
                            @if (auth()->user()->isAdmin())
                                @foreach ($grades as $grade)
                                    <tr onclick="window.location='{{ route('grades.show', $grade->id) }}'" style="cursor: pointer">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $grade->teacher->getFullName() }}</td>
                                        <td>{{ $grade->teacher->subject->name }}</td>
                                        <td>{{ $grade->student->getFullName() }}</td>
                                        <td>{{ $grade->student->class->name }}</td>
                                        <td>{{ $grade->grade }}</td>
                                        <td>{{ $grade->getCreatedAtFormated() }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if (auth()->user()->isTeacher())
                                @foreach ($grades as $grade)
                                    <tr onclick="window.location='{{ route('grades.show', $grade->id) }}'" style="cursor: pointer">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ auth()->user()->getFullName() }}</td>
                                        <td>{{ auth()->user()->subject->name }}</td>
                                        <td>{{ $grade->student->getFullName() }}</td>
                                        <td>{{ $grade->student->class->name }}</td>
                                        <td>{{ $grade->grade }}</td>
                                        <td>{{ $grade->getCreatedAtFormated() }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if (auth()->user()->isStudent())
                                @foreach ($grades as $grade)
                                    <tr onclick="window.location='{{ route('grades.show', $grade->id) }}'" style="cursor: pointer">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $grade->teacher->getFullName() }}</td>
                                        <td>{{ $grade->teacher->subject->name }}</td>
                                        <td>{{ auth()->user()->getFullName() }}</td>
                                        <td>{{ auth()->user()->class->name }}</td>
                                        <td>{{ $grade->grade }}</td>
                                        <td>{{ $grade->getCreatedAtFormated() }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                {{$grades->withQueryString()->links()}}
            </div>
        </div>
    </div>
</div>
<form id="downloadForm" action='{{ route('grades.download') }}' method="POST" style="display: none;">
    @method('POST')
    @csrf
    <input type="hidden" name="export_id" id="exportIdInput">
    <input type="hidden" name="file_name" id="fileName">
    <button type="submit">Download</button>
</form>
@endsection

@section('js')
    <script src="{{URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>
@endsection

<script type="text/javascript">
    let exportId = null
    
    async function handleExport() {
        exportId = `${Date.now()}`; // Generate and store a unique export ID

        try {
            const form = document.getElementById('filterForm');
            const filtersData = new FormData(form);
            const filters = {};
            filtersData.forEach((value, key) => {
                filters[key] = value;
            });

            const params = new URLSearchParams(window.location.search);
            const sorting = {
                'order-by': params.get('order-by'),
                'sort': params.get('sort')
            };

            // Add exportId to filters
            filters.export_id = exportId;

            // Make the initial request to start the export
            await axios.post('{{ route('export.grades') }}', {
                ...filters,
                ...sorting,
            });

            // Show progress Swal after initial request completes
            const progressSwal = Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                showCancelButton: true,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.getCancelButton().addEventListener('click', () => cancleExport());
                },
            });

            // Listen for WebSocket
            if(window.Echo){
                window.Echo.channel(`export.${exportId}`)
                    .listen('.ExportCompleted', (event) => {
                        if (event.exportId === exportId) {
                            progressSwal.close();
                            Swal.fire({
                                title: 'File is Ready',
                                text: 'Your export file is ready. Click the button below to download it.',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: 'Download',
                                customClass: {
                                confirmButton: 'btn btn-success' // Apply the custom class
                                },
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Submit the hidden form to download the file
                                    document.getElementById('exportIdInput').value = event.exportId;
                                    document.getElementById('fileName').value = event.fileName;
                                    document.getElementById('downloadForm').submit();
                                }
                            });
                        }
                    });
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Something went wrong!', 'error');
        }
    }

    async function cancleExport(){
        try {
            await axios.post('{{ route('export.cancel') }}', { export_id: exportId });
            Swal.fire('Cancelled', 'The export process has been cancelled.', 'info');
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Failed to cancel the export.', 'error');
        }
    }

    function sort(e) {
        const field = e.target.getAttribute('data-value');

        const params = new URLSearchParams(window.location.search);

        // Get current sort field and order from query parameters
        const currentSortField = params.get('order-by');
        const currentSortOrder = params.get('sort');

        // Determine the new sort order
        const sortOrder = (currentSortField === field && currentSortOrder === 'asc') ? 'desc' : 'asc';

        // Construct the new URL with updated sorting parameters
        params.set('order-by', field);
        params.set('sort', sortOrder);

        const href = "{{ route('grades.index') }}" + '?' + params.toString();

        // Redirect to the new URL
        window.location = href;
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