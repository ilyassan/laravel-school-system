@extends('layouts.master')

@section('title', 'Create Invoice')

@section('css')
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">Invoices</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Create Invoice</span>
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
                    <form action="{{ route('invoices.store') }}" class="form-horizontal" method="POST">
                        @csrf
                        @method('POST')
                    
                        <!-- Form fields -->
                        <div class="mb-4 main-content-label">Invoice Data</div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Title</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="title" class="form-control" required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Price Excl Tax</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" name="price_excl_tax" class="form-control" required min="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Tax Ratio</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" name="tax_ratio" class="form-control" required min="0" max="100">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Quantity</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" name="quantity" class="form-control" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label class="form-label m-0">Status</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="status" class="custom-select custom-select-sm form-control form-control-sm pr-1">
                                        <option value={{null}}>Select Status</option>
                                        @php
                                            $options = ['payed' => 1, 'unpayed' => -1]
                                        @endphp
                                        @foreach($options as $option => $value)
                                            <option value="{{$value}}" {{request()->get('status') == $value ? 'selected' : ''}}>{{ ucfirst($option) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-left pl-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Create Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    @if (Session::has('success'))
        Swal.fire({
            title: 'Message',
            text: '{{ Session::get('success') }}',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
        {{Session::forget('success')}}
    @endif
@endsection