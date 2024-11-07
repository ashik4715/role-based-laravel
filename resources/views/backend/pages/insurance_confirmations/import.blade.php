@extends('backend.layouts.master')

@section('title')
Import Insurance Confirmation - WCP
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Dashboard</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="index.html">Home</a></li>
                    <li><span>Import Insurance Confirmation</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>

<div class="main-content-inner">
  <div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Import Insurance Confirmations</h4>
                <form action="{{ route('admin.insurance-confirmations.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group my-4">
                        <label for="file">Choose an Excel file:</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection
