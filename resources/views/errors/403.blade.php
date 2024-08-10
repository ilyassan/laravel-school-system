@extends('layouts.master2')
@section('title', 'Forbidden')

@section('css')
<!--- Internal Fontawesome css-->
<link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
<!---Ionicons css-->
<link href="{{URL::asset('assets/plugins/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
<!---Internal Typicons css-->
<link href="{{URL::asset('assets/plugins/typicons.font/typicons.css')}}" rel="stylesheet">
<!---Internal Feather css-->
<link href="{{URL::asset('assets/plugins/feather/feather.css')}}" rel="stylesheet">
<!---Internal Falg-icons css-->
<link href="{{URL::asset('assets/plugins/flag-icon-css/css/flag-icon.min.css')}}" rel="stylesheet">
@endsection
@section('content')
		<!-- Main-error-wrapper -->
		<div class="main-error-wrapper  page page-h ">
			<img src="{{URL::asset('assets/img/media/403.png')}}" class="error-page" alt="error">
			@if (isset($exception) && $exception->getMessage())
                <h2>{{$exception->getMessage()}}.</h2>
            @else
                <h2>You dont't have the permission to access this page.</h2>
            @endif
			<a class="btn btn-outline-danger" href="{{ route('dashboard') }}">Back To Dashboard</a>
		</div>
		<!-- /Main-error-wrapper -->
@endsection
@section('js')
@endsection