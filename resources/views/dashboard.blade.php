@extends('layouts.master')
@section('title', 'Dashboard')

@section('css')
<!--  Owl-carousel css-->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
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
								<i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star"></i> <span>(14,873)</span>
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
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL CHARGES</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">$4,820.50</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last month</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-down text-white"></i>
											<span class="text-white op-7"> -2.09%</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-success-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">AVG STUDENTS NOTES</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">20/17.45</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last month</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7"> 9%</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL STUDENTS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">7645</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-down text-white"></i>
											<span class="text-white op-7"> +495</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL TEACHERS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">246</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last year</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7"> +35</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-md-12 col-lg-12 col-xl-6">
						<div class="card" style="height: 390px">
							<div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-0">Students Absence</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
								<p class="tx-12 text-muted mb-0">Students Absence Last Week.</p>
							</div>
							<div class="card-body">
								<div class="ht-200 ht-lg-250">
									<canvas id="absenceChart"></canvas>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-xl-6">
						<div class="card card-dashboard-map-one justify-content-md-between" style="height: 390px">
							<div>
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-1">Students Gender</h4>
								</div>
								<p class="tx-12 text-muted mb-0">Students Gender Distribution Overview.</p>
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
								<h3 class="card-title mb-1">Teachers Report</h3>
								<span class="tx-12 tx-muted mb-3 ">Lastest reports sending by the teachers.</span>
							</div>
							<div class="card-body pt-1">
								<div>
									<div class="chips">
										<div class="team">
											<a href="#" class="chip">
												<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/2.jpg')}}"></span> Victoria
											</a>
											<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
											<p>On the other hand, we denounce with right indignation and dislike imagesralized</p>
										</div>
										<div class="team">
											<a href="#" class="chip">
												<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/3.jpg')}}"></span> Nathan
											</a>
											<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
											<p>On the other hand, we denounce with right indignation and dislike imagesralized</p>
										</div>
										<div class="team">
											<a href="#" class="chip">
												<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/4.jpg')}}"></span> Alice
											</a>
											<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
											<p class="mb-0">On the other hand, we denounce with right indignation and dislike imagesralized</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-xl-8">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">Lastest Charges</h4>
								<i class="mdi mdi-dots-horizontal text-gray"></i>
							</div>
							<span class="tx-12 tx-muted mb-3 ">Lastest charges paying by the school.</span>
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
										<tr>
											<td>19 Nov 2023</td>
											<td class="tx-right tx-medium tx-inverse">Computers</td>
											<td class="tx-right tx-medium tx-inverse">10</td>
											<td class="tx-right tx-medium tx-danger">9500$</td>
										</tr>
										<tr>
											<td>18 Nov 2023</td>
											<td class="tx-right tx-medium tx-inverse">Independence Day Event</td>
											<td class="tx-right tx-medium tx-inverse">1</td>
											<td class="tx-right tx-medium tx-danger">2000$</td>
										</tr>
										<tr>
											<td>17 Nov 2023</td>
											<td class="tx-right tx-medium tx-inverse">Tables</td>
											<td class="tx-right tx-medium tx-inverse">20</td>
											<td class="tx-right tx-medium tx-danger">1500$</td>
										</tr>
										<tr>
											<td>16 Nov 2023</td>
											<td class="tx-right tx-medium tx-inverse">Eectricity bill</td>
											<td class="tx-right tx-medium tx-inverse">1</td>
											<td class="tx-right tx-medium tx-danger">8000$</td>
										</tr>
										<tr>
											<td>15 Nov 2023</td>
											<td class="tx-right tx-medium tx-inverse">Water Bill</td>
											<td class="tx-right tx-medium tx-inverse">1</td>
											<td class="tx-right tx-medium tx-danger">3500%</td>
										</tr>
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
								<h3 class="card-title mb-1">Students Report</h3>
								<span class="tx-12 tx-muted mb-3 ">Lastest reports sending by the students.</span>
							</div>
							<div class="card-body pt-1">
								<div>
									<div class="chips">
										<div class="team">
											<a href="#" class="chip">
												<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/2.jpg')}}"></span> Victoria
											</a>
											<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
											<p>On the other hand, we denounce with right indignation and dislike imagesralized</p>
										</div>
										<div class="team">
											<a href="#" class="chip">
												<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/3.jpg')}}"></span> Nathan
											</a>
											<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
											<p>On the other hand, we denounce with right indignation and dislike imagesralized</p>
										</div>
										<div class="team">
											<a href="#" class="chip">
												<span class="avatar cover-image" data-image-src="{{URL::asset('assets/img/faces/4.jpg')}}"></span> Alice
											</a>
											<i class="fas fa-envelope text-primary" aria-hidden="true"></i>
											<p class="mb-0">On the other hand, we denounce with right indignation and dislike imagesralized</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">Top 5 Classes</h6><span class="d-block mg-b-10 text-muted tx-12">The top 5 classes based by notes.</span>
							<div class="list-group h-100 justify-content-around">
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account-multiple"></i>
									<p>CLASS-A</p><span>BAC-2</span>
								</div>
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account-multiple"></i>
									<p>CLASS-B</p><span>BAC-3</span>
								</div>
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account-multiple"></i>
									<p>CLASS-C</p><span>BAC-1</span>
								</div>
								<div class="list-group-item border-top-0">
									<i class="mdi mdi-account-multiple"></i>
									<p>CLASS-D</p><span>BAC-5</span>
								</div>
								<div class="list-group-item border-top-0 border-bottom-0 mb-0">
										<i class="mdi mdi-account-multiple"></i>
									<p>CLASS-E</p><span>BAC-4</span>
								</div>
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

		var ctx1 = document.getElementById('absenceChart').getContext('2d');
		new Chart(ctx1, {
			type: 'bar',
			data: {
				labels: ['Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat'],
				datasets: [{
					label: 'Hours',
					data: [12, 39, 20, 10, 25, 18],
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
				[1, 69]
			],
			color: '#36A2EB'
		}, {
			label: 'Girls',
			data: [
				[1, 59]
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