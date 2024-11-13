@extends('backend.layouts.master')

@section('title')
Dashboard | WCP
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Dashboard</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="index.html">Home</a></li>
                    <li><span>Dashboard</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-6 mt-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <a href="{{ route('admin.roles.index') }}">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fa fa-users"></i> Roles </div>
                                    <h2 class="ml-5">{{ $total_roles }}</h2>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg2">
                            <a href="{{ route('admin.admins.index') }}">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fa fa-user"></i> Admins </div>
                                    <h2 class="ml-5">{{ $total_admins }}</h2>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3 mb-lg-0">
                    <div class="card">
                        <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fa fa-lock"></i>Permissions </div>
                                <h2>{{ $total_permissions }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- section applications tables -->
                <div class="col-lg-12 grid-margin stretch-card mt-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Showing Applications: <b>{{ count($applications) }}</b></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>IDs</th>
                                            <th>Agent ID</th>
                                            <th>Phone</th>
                                            <th>Data</th>
                                            <th>status</th>
                                            <th>address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <thead>
                                        <tr>
                                            <th>Serial</th>
                                            <th>Unique Code</th>
                                            <th>Project Name</th>
                                            <th>Investor Name</th>
                                            <th>Order ID</th>
                                            <th>Bank</th>
                                            <th>Invested Amount</th>
                                            <th>Payment Mode</th>
                                            <th>Our Bank</th>
                                            <th>JSON</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($applications as $row)
                                        <tr>
                                            <td>{{ $row['id'] }}</td>
                                            <td>{{ $row['agent_id'] }}</td>
                                            <td>{{ $row['phone'] }}</td>
                                            <td>
                                                <a href="{{ route('admin.view.json', $row['id']) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button" class="btn
                                        @if ($row['status'] == App\Services\Application\Status::INITIATED)
                                            btn-info
                                        @elseif ($row['status'] == App\Services\Application\Status::DRAFTED)
                                            btn-warning
                                        @elseif ($row['status'] == App\Services\Application\Status::SUBMITTED)
                                            btn-success
                                        @elseif ($row['status'] == App\Services\Application\Status::APPROVED)
                                            btn-outline-dark
                                        @endif
                                        ">
                                                    {{ $row['status'] }}
                                                </button>
                                            </td>
                                            <td>{{ $row['address'] }}</td>
                                            <td>
                                                <form action="{{ route('admin.applications.destroy', $row['id']) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this application?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="9">{!! $applications->links() !!}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- endsection applications tables -->

            </div>
        </div>
    </div>
</div>
@endsection