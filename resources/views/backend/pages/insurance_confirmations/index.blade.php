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

<!-- CSS of Bulk ACCEPT -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<style>
    .bulk-actions {
        margin-bottom: 15px;
    }

    .bulk-actions button {
        padding: 8px 15px;
        font-weight: 500;
    }

    table.dataTable tbody td {
        vertical-align: middle;
    }

    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .custom-select {
        min-width: 100px;
    }
</style>
<!-- End Css of Bulk ACCEPT -->
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

        <div class="col-lg-12">
            <div class="row">

                <div class="col-md-4 my-3 mb-lg-0">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">Total <i class="ti-info"></i> </div>
                                <h2>{{ $total_insurance }}</h2>
                                <canvas id="seolinechart3" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 my-3 mb-lg-0">
                    <div class="card">
                        <div class="seo-fact sbg2">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">Accepted <i class="ti-check-box"></i> </div>
                                <h2>{{ $accepted_count }}</h2>
                                <canvas id="seolinechart3" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 my-3 mb-lg-0">
                    <div class="card">
                        <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">Rejected <i class="ti-na"></i> </div>
                                <h2>{{ $rejected_count }}</h2>
                                <canvas id="seolinechart3" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-12 mt-4">
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">{{ __('Admins') }}</h4>
                    <p class="float-right mb-2 mx-2">
                        @if ($usr && $usr->can('insurance-confirmations.import-view'))
                        <a class="btn btn-primary text-white"
                            href="{{ route('admin.insurance-confirmations.import-view') }}">
                            {{ __('Import XLSX') }}
                        </a>
                        @endif
                    </p>

                    <div class="mb-2 text-right">
                        <div class="btn-group bulk-actions">
                            <button type="button" class="btn btn-success" onclick="bulkAction('yes')">
                                <i class="fa fa-check"></i> Bulk Accept
                            </button>
                            <button type="button" class="btn btn-danger ml-2" onclick="bulkAction('no')">
                                <i class="fa fa-times"></i> Bulk Reject
                            </button>
                        </div>
                    </div>

                    <h4 class="header-title">Insurance Confirmation List</h4>

                    <div class="table-responsive">
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>{{ __('FID') }}</th>
                                    <th>{{ __('Farmer Name') }}</th>
                                    <th>{{ __('NID') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Thana') }}</th>
                                    <th>{{ __('Area') }}</th>
                                    <th>{{ __('Region') }}</th>
                                    <th>{{ __('Project Name') }}</th>
                                    <th>{{ __('FO ID') }}</th>
                                    <th>{{ __('FO Name') }}</th>
                                    <th>{{ __('Area Manager') }}</th>
                                    <th>{{ __('Approved Amount') }}</th>
                                    <th>{{ __('Acceptance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insuranceConfirmations as $confirmation)
                                <tr>
                                    <td><input type="checkbox" name="confirmations[]" value="{{ $confirmation->id }}">
                                    </td>
                                    <td>{{ $confirmation->fid }}</td>
                                    <td>{{ $confirmation->farmer_name }}</td>
                                    <td>{{ $confirmation->nid }}</td>
                                    <td>{{ $confirmation->phone }}</td>
                                    <td>{{ $confirmation->thana }}</td>
                                    <td>{{ $confirmation->area }}</td>
                                    <td>{{ $confirmation->region }}</td>
                                    <td>{{ $confirmation->project_name }}</td>
                                    <td>{{ $confirmation->fo_id }}</td>
                                    <td>{{ $confirmation->fo_name }}</td>
                                    <td>{{ $confirmation->area_manager }}</td>
                                    <td>{{ $confirmation->approved_amount }}</td>
                                    <td>
                                        <form
                                            action="{{ route('admin.insurance-confirmations.updateAcceptance', $confirmation->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="acceptance" onchange="this.form.submit()"
                                                class="custom-select">
                                                <option value="" {{ is_null($confirmation->acceptance) ? 'selected' : ''
                                                    }}>
                                                    Select</option>
                                                <option value="yes" {{ $confirmation->acceptance === 'yes' ? 'selected'
                                                    : '' }}>
                                                    Yes</option>
                                                <option value="no" {{ $confirmation->acceptance === 'no' ? 'selected' :
                                                    '' }}>
                                                    No</option>
                                            </select>
                                        </form>
                                    </td>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function() {
            if ($('#dataTable').length) {
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
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });
            } else {
                console.log("DataTable element not found.");
            }

            // Handle select all checkbox
            $('#select-all').change(function() {
                $('input[name="confirmations[]"]').prop('checked', $(this).prop('checked'));
            });

            // Update select-all when individual checkboxes change
            $('input[name="confirmations[]"]').change(function() {
                if ($('input[name="confirmations[]"]:checked').length === $('input[name="confirmations[]"]')
                    .length) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });
        });

        function bulkAction(action) {
            var selectedIds = [];
            $('input[name="confirmations[]"]:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Records Selected',
                    text: 'Please select at least one record to perform bulk action.'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${action === 'yes' ? 'accept' : 'reject'} the selected records?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'yes' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: action === 'yes' ? 'Yes, Accept!' : 'Yes, Reject!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.insurance-confirmations.bulk-update') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds,
                            action: action
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred while processing your request.'
                            });
                        }
                    });
                }
            });
        }

        function bulkAction(action) {
            var selectedIds = [];
            $('input[name="confirmations[]"]:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Records Selected',
                    text: 'Please select at least one record to perform bulk action.'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${action === 'yes' ? 'accept' : 'reject'} the selected records?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'yes' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: action === 'yes' ? 'Yes, Accept!' : 'Yes, Reject!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.insurance-confirmations.bulk-update') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds,
                            action: action
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred while processing your request.'
                            });
                        }
                    });
                }
            });
        }
</script>
@endsection