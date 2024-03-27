@extends('layouts.master')
@section('title', 'Dashboard')

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
						  <p class="mg-b-0">Student Dashboard</p>
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
								<p class="tx-12 tx-gray-500 mb-2">Your latest grades entered by your teachers.<a href="">Learn more</a></p>
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
                                                <tr>
                                                    <td>{{$grade->created_at->format('d M Y')}}</td>
                                                    <td>{{$grade->teacher->name}}</td>
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
							<h6 class="card-title">Top 5 Students</h6>
                            <span class="d-block mg-b-10 text-muted tx-12">
                                The top 5 students in your class based on grades.
                            </span>
							<div class="list-group h-100 justify-content-around">
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account"></i>
									<p class="text-capitalize">ilyass anida</p><span>1</span>
								</div>
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account"></i>
									<p class="text-capitalize">ahmed mahmoud</p><span>2</span>
								</div>
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account"></i>
									<p class="text-capitalize">yassin yassir</p><span>3</span>
								</div>
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account"></i>
									<p class="text-capitalize">sara mansour</p><span>4</span>
								</div>
								<div class="list-group-item border-top-0 border-bottom-0 mb-0">
									<i class="mdi mdi-account"></i>
									<p class="text-capitalize">elon elian</p><span>5</span>
								</div>
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
                                        <tr>
                                            <td>Science</td>
                                            <td>28 Feb 2024</td>
                                            <td>4 Mar 2024</td>
                                            <td>Research about the humain brain</td>
                                        </tr>
                                        <tr>
                                            <td>Mathematics</td>
                                            <td>25 Feb 2024</td>
                                            <td>29 Feb 2024</td>
                                            <td>Answering the questions given in the exercise book</td>
                                        </tr>
                                        <tr>
                                            <td>Physics</td>
                                            <td>23 Feb 2024</td>
                                            <td>26 Feb 2024</td>
                                            <td>Research about photons behavior</td>
                                        </tr>                                        
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
                labels: Object.keys(avgStudentGradesEachMonth),
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