@extends('layouts.master')
@section('title', 'Dashboard')

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
						  <p class="mg-b-0">Teacher Dashboard</p>
						</div>
					</div>
				</div>
				<!-- /breadcrumb -->
@endsection
@section('content')
				<!-- row -->
				<div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-success-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-2 tx-12 text-white">YOUR AVG STUDENTS GRADES</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{$teacherStudentsAvgGrades->total}}/20</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last month</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$teacherStudentsAvgGrades->variation_rate > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$teacherStudentsAvgGrades->variation_rate}}%</span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-2 tx-12 text-white">YOUR STUDENTS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{number_format($teacherStudents->total)}}</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$teacherStudents->variation > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$teacherStudents->variation > 0 ? '+': '-'}}{{$teacherStudents->variation}}</span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-warning-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-2 tx-12 text-white">YOUR CLASSES</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{number_format($teacherClasses->total)}}</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$teacherClasses->variation > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$teacherClasses->variation > 0 ? '+': '-'}}{{$teacherClasses->variation}}</span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-2 tx-12 text-white">SALARY</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{$salary}} DH</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7">10%</span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm row-deck">
					<div class="col-md-12 col-lg-12 col-xl-6">
						<div class="card">
							<div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-0">Your Students Classes Absence</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
								<p class="tx-12 text-muted mb-0">Your students classes absence last week.</p>
							</div>
							<div class="card-body">
								<div class="ht-200 ht-lg-250">
									<canvas id="absenceChart"></canvas>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-lg-12 col-xl-6">
                        <div class=" card card--calendar p-0 mg-b-20">
                            <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0 mb-4">
								<h4 class="card-title mb-0">Calendar</h4>
							</div>
                            <div class="pb-4 px-4 mb-0">
                                <div class="fc-datepicker main-datepicker border "></div>
                            </div>
                        </div>
                    </div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm row-deck">
					<div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">Top 5 Students</h6><span class="d-block mg-b-10 text-muted tx-12">The top 5 students in your classes based on grades.</span>
							<div class="list-group h-100 justify-content-around">
								@foreach ($topTeacherStudents as $student)					
									<div class="list-group-item border-top-0 {{$loop->last ? 'border-bottom-0 mb-0' : ''}}">
										<i class="mdi mdi-account"></i>
										<p class="text-capitalize">{{$student->name}}</p><span>{{$student->class_name}}</span>
									</div>
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
							<span class="tx-12 tx-muted mb-3 ">Your latest assigned homeworks to students.</span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-20p">From</th>
											<th class="wd-lg-20p">To</th>
											<th class="wd-lg-50p">Title</th>
											<th class="wd-lg-10p">Class</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($latestHomeworks as $homework)
											<tr>
												<td>{{$homework->created_at->format('d M Y')}}</td>
												<td>{{$homework->end_date->format('d M Y')}}</td>
												<td>{{$homework->title}}</td>
												<td>{{$homework->class_name}}</td>
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

		// Absence Chart
		var teacherClassesWithAbsences = {!! json_encode($teacherClassesWithAbsences) !!};

		var classesNames = teacherClassesWithAbsences.map(obj => obj.name);
		var classesAbsences = teacherClassesWithAbsences.map(obj => obj.absences_sum);

		var ctx1 = document.getElementById('absenceChart').getContext('2d');
		new Chart(ctx1, {
			type: 'bar',
			data: {
				labels: classesNames,
				datasets: [{
					label: 'Hours',
					data: classesAbsences,
					backgroundColor: '#285cf7'
				}]
			},
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					display: false,
					labels: {
						display: false
					}
				},
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true,
							fontSize: 10,
							max: 80,
							fontColor: "rgb(171, 167, 167,0.9)",
						},
						gridLines: {
							display: true,
							color: 'rgba(171, 167, 167,0.2)',
							drawBorder: false
						},
					}],
					xAxes: [{
						barPercentage: 0.6,
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
{{-- Calendar --}}
<script src="{{URL::asset('assets/js/app-calendar.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>

@endsection