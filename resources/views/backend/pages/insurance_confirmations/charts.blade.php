@extends('backend.layouts.master')

@section('title')
Insurance Chart Reports - WCP
@endsection

@section('styles')

<style>
    .chart--container {
        min-height: 530px;
        width: 100%;
        height: 100%;
    }

    .zc-ref {
        display: none;
    }
</style>
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left"><a class="btn btn-outline-dark" type="button"
                        href="{{ route(name: 'admin.insurance-confirmations.view') }}">{{ __('Back') }}</a></h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route(name: 'admin.insurance-confirmations.view') }}">{{ __('List Insurance
                            Confirmation') }}</a></li>
                    <li><span>Insurance Chart Reports</span></li>
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
                    {{-- <h4 class="header-title">Insurance Chart Reports</h4> --}}

                    <div class="col-lg-12 mt-1">
                        <div class="card">
                            <div class="card-body">
                                <div id="highChart" class="chart--container"></div>

                                {{-- <div id="myChart" class="chart--container">
                                    <a class="zc-ref" href="https://www.zingchart.com/">Powered by ZingChart</a>
                                </div> --}}

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- start chart js -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<!-- start zingchart js -->
{{-- <script defer nonce="undefined" src="https://cdn.zingchart.com/zingchart.min.js"></script> --}}
{{-- <script>
    zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
    ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
</script> --}}

<script src="{{ asset('assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>
<script src="{{ asset('assets/js/line-chart.js') }}"></script>
<script src="{{ asset('assets/js/bar-chart.js') }}"></script>
<script src="{{ asset('assets/js/pie-chart.js') }}"></script>
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>


<!-- Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ensure the areas and pendingCounts are defined here
        let areas = @json($pendingCountsByArea->keys());
        let pendingCounts = @json($pendingCountsByArea->values());

        Highcharts.chart('highChart', {
            chart: { type: 'column' },
            title: { text: 'Pending Insurance Acceptance by Area' },
            xAxis: { categories: areas },
            yAxis: { title: { text: 'Pending Y-Axis' } },
            series: [{
                name: 'Pending Confirmations',
                data: pendingCounts.map(count => parseInt(count))
            }]
        });
    });
</script>
@endsection