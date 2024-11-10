@extends('backend.layouts.master')

@section('title')
List Insurance Confirmation - WCP
@endsection

@php
    $usr = Auth::guard('admin')->user();
@endphp

@section('styles')
<!-- Start datatable css -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">

<!-- DataTables Buttons CSS for Export -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Dashboard</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route(name: 'admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>Insurance Confirmation</span></li>
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
    <div class="col-lg-12 mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <h4 class="header-title float-left">{{ __('Admins') }}</h4>
                <p class="float-right mb-2">
                    @if ($usr->can('insurance-confirmations.import-view'))
                    <a class="btn btn-primary text-white" href="{{ route('admin.insurance-confirmations.import-view') }}">
                        {{ __('Import XLSX') }}
                    </a>
                    @endif
                </p>
                <h4 class="header-title">Insurance Confirmation List</h4>
                
                <div class="table-responsive">
                    <table id="dataTable" class="text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th>{{ __('NID')}}</th>
                                <th>{{ __('Acceptance')}}</th>
                                <th>{{ __('Project Name')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($insuranceConfirmations as $confirmation)
                                <tr>
                                    <td>{{ $confirmation->nid }}</td>
                                    <td>{{ $confirmation->acceptance }}</td>
                                    <td>{{ $confirmation->project_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<!-- Start datatable js -->
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

<!-- DataTables Buttons JS for Export -->
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>


<script>
$(document).ready(function () {
    if ($('#dataTable').length) {
        console.log("DataTable found, initializing...");
        $('#dataTable').DataTable({
            responsive: false,
            // dom: 'Bfrtip',
            dom: '<"top"lB>rt<"bottom"p><"clear">',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ]
        });
    } else {
        console.log("DataTable element not found.");
    }
});

</script>
@endsection