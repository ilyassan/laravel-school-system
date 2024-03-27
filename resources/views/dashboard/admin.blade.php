@extends('layouts.master')
@section('title', 'Dashboard')

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
						  <p class="mg-b-0">School Overview</p>
						</div>
					</div>
					<div class="main-dashboard-header-right">
						<div>
							<label class="tx-13">Users Ratings</label>
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
				<!-- row -->
				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-warning-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL CHARGES</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{$charges->total}} DH</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last month</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$charges->variation_rate > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$charges->variation_rate}}%</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12,9,5,6,4</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-success-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">AVG STUDENT GRADE</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{$avgStudentGrade->total}}/20</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last month</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$avgStudentGrade->variation_rate > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$avgStudentGrade->variation_rate}}%</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10,9,5,6,6</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL STUDENTS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{number_format($students->total)}}</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$students->variation > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$students->variation > 0 ? '+': '-'}}{{$students->variation}}</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7,9,5,8,10</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL TEACHERS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{number_format($teachers->total)}}</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-{{$teachers->variation > 0 ? 'up': 'down'}} text-white"></i>
											<span class="text-white op-7">{{$teachers->variation > 0 ? '+': '-'}}{{$teachers->variation}}</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12,9,5,6,10</span>
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
									<h4 class="card-title mb-0">Students Absence</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
								<p class="tx-12 text-muted mb-0">Students absence last week.</p>
							</div>
							<div class="card-body">
								<div class="ht-200 ht-lg-250">
									<canvas id="absenceChart"></canvas>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-xl-6">
						<div class="card card-dashboard-map-one justify-content-md-between" style="min-height: 300px">
							<div>
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-1">Students Gender</h4>
								</div>
								<p class="tx-12 text-muted mb-0">Students gender distribution overview.</p>
							</div>
							<div class="ht-200 ht-sm-300" id="flotPie1"></div>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm row-deck">
					<div class="col-md-12 col-xl-4">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title mb-1">Teachers Reports</h3>
								<span class="tx-12 tx-muted mb-3 ">Latest reports sent by the teachers.</span>
							</div>
							<div class="card-body pt-1">
								<div>
									<div class="chips">
										@foreach ($latestTeacherReports as $report)
											<div class="team">
												<a href="#" class="chip">
													<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/4.jpg')}}"></span>
													{{$report->user_name}}
												</a>
												<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
												<p class="{{$loop->last ? 'mb-0' : 'mb-2'}}">{{$report->shortDescription}} ...</p>
											</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-xl-8">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">Latest Charges</h4>
								<i class="mdi mdi-dots-horizontal text-gray"></i>
							</div>
							<span class="tx-12 tx-muted mb-3 ">Latest charges paying by the school.</span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-25p">Date</th>
											<th class="wd-lg-25p tx-right">Title</th>
											<th class="wd-lg-25p tx-right">Quantity</th>
											<th class="wd-lg-25p tx-right">Total Price</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($latestCharges as $charge)
											<tr>
												<td>{{$charge->created_at->format('d M Y')}}</td>
												<td class="tx-right tx-medium tx-inverse">{{$charge->title}}</td>
												<td class="tx-right tx-medium tx-inverse">{{$charge->quantity}}</td>
												<td class="tx-right tx-medium tx-danger">{{$charge->price * $charge->quantity}} DH</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm row-deck">
					<div class="col-md-12 col-lg-8 col-xl-8">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title mb-1">Students Reports</h3>
								<span class="tx-12 tx-muted mb-3 ">Latest reports sent by the students.</span>
							</div>
							<div class="card-body pt-1">
								<div>
									<div class="chips">
										@foreach ($latestStudentsReports as $report)
											<div class="team">
												<a href="#" class="chip">
													<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/4.jpg')}}"></span>
													{{$report->user_name}}
												</a>
												<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
												<p class="{{$loop->last ? 'mb-0' : 'mb-2'}}">{{$report->shortDescription}} ...</p>
											</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">Top 5 Classes</h6><span class="d-block mg-b-10 text-muted tx-12">The top 5 classes based on grades average of the last 3 month.</span>
							<div class="list-group h-100 justify-content-around">
								@foreach ($topClasses as $class)									
									<div class="list-group-item border-top-0 {{$loop->last ? 'border-bottom-0 mb-0' : ''}}">
										<i class="mdi mdi-account-multiple"></i>
										<p>CLASS-{{chr(65 + $loop->index)}}</p><span>{{$class->name}}</span>
									</div>
								@endforeach
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
		var lastWeekAbsences = {!! json_encode($lastWeekAbsences) !!};
		var {1:mon, 2:tue, 3:wed, 4:thur, 5:fri, 6:sat} = lastWeekAbsences;

		var ctx1 = document.getElementById('absenceChart').getContext('2d');
		new Chart(ctx1, {
			type: 'bar',
			data: {
				labels: ['Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat'],
				datasets: [{
					label: 'Hours',
					data: [mon, tue, wed, thur, fri, sat],
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

		// Student Gender Chart

		var piedata = [{
			label: 'Boys',
			data: [
				[1, {!! json_encode($students->boys) !!}]
			],
			color: '#36A2EB'
		}, {
			label: 'Girls',
			data: [
				[1, {!! json_encode($students->girls) !!}]
			],
			color: '#FF6384'
		}];
		$.plot('#flotPie1', piedata, {
			series: {
				pie: {
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 2 / 3,
						formatter: labelFormatter,
						threshold: 0.1
					}
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			}
		});
		function labelFormatter(label, series) {
			return '<div style="font-size:8pt; text-align:center; padding:2px; color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
		}
	});
</script>
<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!--Internal  Flot js-->
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
@endsection