@extends('layouts.master')
@section('title', 'Dashboard')

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, {{auth()->user()->getFullName()}}</h2>
						  <p class="mg-b-0">Student Dashboard</p>
						</div>
					</div>
					<div class="main-dashboard-header-right">
						<div>
							<label class="tx-13">Ratings</label>
							<div class="main-star">
								@for ($i = 1; $i <= 5; $i++)
										@if ($ratings->avg >= $i)
											<i class="typcn typcn-star-full-outline active"></i> <!-- Full active star -->
										@elseif($ratings->avg - $i >= -0.5)
											<i class="typcn typcn-star-half-outline active"></i> <!-- Half active star -->
										@else
											<i class="typcn typcn-star-outline"></i> <!-- Inactive star -->
										@endif
								@endfor
								<span>({{$ratings->count}})</span>
							</div>
						</div>
					</div>
				</div>
				<!-- /breadcrumb -->
@endsection
@section('content')

				<!-- row opened -->
				<div class="row row-sm row-deck">
                    <div class="col-lg-12 col-xl-6">
						<div class="card overflow-hidden">
							<div class="card-body">
								<div class="main-content-label mg-b-5">
									Results Performance
								</div>
								<p class="mg-b-20">Your performance compared to your class avg based on exams grades.</p>
								<div class="chartjs-wrapper-demo">
									<canvas id="gradesChart"></canvas>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-xl-6">
						<div class="card">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mg-b-0">LATEST ENTERED GRADES</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
								<p class="tx-12 tx-gray-500 mb-2">Your latest grades entered by your teachers.<a href="{{ route('grades.index') }}">Learn more</a></p>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table mg-b-0 text-md-nowrap">
										<thead>
											<tr>
												<th>Date</th>
												<th>Teacher</th>
												<th>Subject</th>
												<th>Grade</th>
											</tr>
										</thead>
										<tbody>
                                            @foreach ($latestStudentGrades as $grade)
                                                <tr onclick="window.location='{{ route('grades.show', $grade->id) }}'" style="cursor: pointer">
                                                    <td>{{$grade->getCreatedAtFormated()}}</td>
                                                    <td>{{$grade->teacher->getFullName()}}</td>
                                                    <td>{{$grade->teacher->subject->name}}</td>
                                                    <td>{{$grade->grade}}/20</td>
                                                </tr>                                          
                                            @endforeach
                                        </tbody>                                        
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm row-deck">
					<div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">Top {{ $topClassStudents->count() }} Students</h6>
                            <span class="d-block mg-b-10 text-muted tx-12">
                                The top {{ $topClassStudents->count() }} students in your class based on grades.
                            </span>
							<div class="list-group h-100 justify-content-around">
								@foreach ($topClassStudents as $student)					
								<a href="{{ route('profile.show', $student->getKey() ) }}" class="text-reset text-decoration-none">
									<div class="list-group-item border-top-0 {{$loop->last ? 'border-bottom-0 mb-0' : ''}}">
										<i class="mdi mdi-account"></i>
										<p class="text-capitalize">{{$student->getKey() === auth()->user()->getKey() ? 'You': $student->getFullName()}}</p><span>{{$loop->index + 1}}</span>
									</div>
								</a>
								@endforeach	
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-8">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">Assigned Homeworks</h4>
								<i class="mdi mdi-dots-horizontal text-gray"></i>
							</div>
							<span class="tx-12 tx-muted mb-3 ">Your latest assigned homework from the teaches.</span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-10p">Subject</th>
											<th class="wd-lg-20p">From</th>
											<th class="wd-lg-20p">To</th>
											<th class="wd-lg-50p">Title</th>
										</tr>
									</thead>
									<tbody>
                                        @foreach ($classHomeworks as $homework)
											<tr>
												<td>{{$homework->subject->name}}</td>
												<td>{{$homework->created_at->format('m/d/Y')}}</td>
												<td>{{$homework->end_date->format('m/d/Y')}}</td>
												<td>{{$homework->title}}</td>
											</tr> 
										@endforeach                                   
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->
			</div>
		</div>
		<!-- Container closed -->
@endsection
@section('js')
{{-- Charts --}}
<script>
    $(function() {
        'use strict';

        // Student Grades Chart
		var avgStudentGradesEachMonth = {!! json_encode($avgStudentGradesEachMonth) !!};
		var avgClassGradesEachMonth = {!! json_encode($avgClassGradesEachMonth) !!};
		
        var ctx8 = document.getElementById('gradesChart');
        new Chart(ctx8, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    data: Object.values(avgStudentGradesEachMonth),
                    borderColor: '#007bff',
                    borderWidth: 1,
                    fill: false,
                    label: 'You',
                }, {
                    data: Object.values(avgClassGradesEachMonth),
                    borderColor: '#f7557a',
                    borderWidth: 1,
                    fill: false,
                    label: 'Your Class Avg',
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    labels: {
                        fontSize: 10,
                        fontColor: "rgb(171, 167, 167,0.9)",
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            fontSize: 10,
                            max: 20,
                            fontColor: "rgb(171, 167, 167,0.9)",
                        },
                        gridLines: {
                            display: true,
                            color: 'rgba(171, 167, 167,0.2)',
                            drawBorder: false
                        },
                    }],
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontSize: 11,
                            fontColor: "rgb(171, 167, 167,0.9)",
                        },
                        gridLines: {
                            display: true,
                            color: 'rgba(171, 167, 167,0.2)',
                            drawBorder: false
                        },
                    }]
                }
            }
        });
    });
</script>
<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>

@endsection