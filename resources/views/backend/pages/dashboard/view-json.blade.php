@extends('backend.layouts.master')

@section('title')
JSON Page - Ogro
@endsection

@section('styles')
<style>
    .tab-btn {
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        padding: 10px 20px;
        margin-right: 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .tab-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .status-badge {
        background: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
    }

    .action-btn {
        padding: 6px 20px;
        border-radius: 4px;
        margin-left: 10px;
    }

    .farmer-image {
        max-width: 100%;
        height: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .detail-row {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .comment-icon {
        color: #6c757d;
        cursor: pointer;
        float: right;
    }
</style>
<style>
    pre {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 10px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
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
        <div class="col-lg-12 mt-5 grid-margin stretch-card">
            @php
            $value = reset($data);
            @endphp
            {{-- @foreach($data as $key => $value) --}}
            @if($value)
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Application ID: #{{ $single_application['id'] ?? 'N/A' }}
                        <span class="status-badge">{{ $single_application->status ?? 'Pending' }}</span>
                    </h5>
                    <p class="mb-1">Farmer: {{ $value['farmer_info']['values']['nid_name_english']['value'] ?? 'N/A' }}
                    </p>
                    <p class="mb-1">Address: {{
                        implode(', ', [
                        $value['farmer_info']['values']['permanent_address']['children']['registered_thana']['value'] ??
                        'N/A',
                        $value['farmer_info']['values']['permanent_address']['children']['registered_village']['value']
                        ?? 'N/A',
                        $value['farmer_info']['values']['permanent_address']['children']['registered_district']['value']
                        ?? 'N/A',
                        $value['farmer_info']['values']['permanent_address']['children']['registered_division']['value']
                        ?? 'N/A',
                        $value['farmer_info']['values']['permanent_address']['children']['registered_post_code']['value']
                        ?? 'N/A',
                        $value['farmer_info']['values']['permanent_address']['children']['registered_post_office']['value']
                        ?? 'N/A',
                        ]) }}
                    </p>
                    <p>Agent: {{ Auth::guard('admin')->user()->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-right">
                    <div>
                        <button class="btn btn-warning action-btn">Re-Submit</button>
                        <button class="btn btn-success action-btn">Approve</button>
                        <button class="btn btn-danger action-btn">Reject</button>
                    </div>
                </div>
            </div>

            <!-- Accordion -->
            <div id="accordion">
                <!-- Accordion 1: Personal Information (NID type) -->
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                                aria-expanded="true" aria-controls="collapseOne">
                                <b>1. ব্যক্তিগত তথ্য</b>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Attachment</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            @php
                                            $url = 'https://wegro-agent-production.s3.ap-southeast-1.amazonaws.com/';
                                            $front_image = $value['nid_images']['value']['front_image'] ?? null;
                                            $back_image = $value['nid_images']['value']['back_image'] ?? null;
                                            $selfie_images = $value['selfie_images']['value']['user_image'] ?? null;

                                            $frontImageUrl = $front_image ? $url . $front_image :
                                            '/api/placeholder/400/300';
                                            $backImageUrl = $back_image ? $url . $back_image :
                                            '/api/placeholder/400/300';
                                            $selfieImageUrl = $selfie_images ? $url . $selfie_images :
                                            '/api/placeholder/400/300';
                                            @endphp
                                            <img src="{{ $frontImageUrl }}" alt="NID Front" class="farmer-image">
                                        </div>
                                        <div class="col-6">
                                            <img src="{{ $backImageUrl }}" alt="NID Front" class="farmer-image">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Farmer Image</h6>
                                    <img src="{{ $selfieImageUrl }}" alt="NID Front" class="farmer-image">
                                </div>
                            </div>
                            @foreach(['nid_name_english', 'farmer_gender', 'nid_number', 'nid_dob',
                            'nid_father_name_english',
                            'nid_mother_name_english', 'marital_status'] as $field)
                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span>
                                        {{
                                        $value['farmer_info']['values'][$field]['value'] ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <!-- Accordion 2: Permanent Address (NID type) -->
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo"
                                aria-expanded="false" aria-controls="collapseTwo">
                                <b>2. স্থায়ী ঠিকানা (NID অনুযায়ী)</b>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row">
                                <b>স্থায়ী ঠিকানা (NID অনুযায়ী)</b>
                            </div>
                            <!-- Iterate over the address fields -->
                            @foreach(['registered_thana', 'registered_district', 'registered_division',
                            'registered_village'] as $field)
                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $value['farmer_info']['values']['permanent_address']['children'][$field]['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Accordion 3: Guarantor Pages -->
                <div class="card">
                    <div class="card-header" id="headingThree">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree"
                                aria-expanded="false" aria-controls="collapseThree">
                                <b>3. গ্যারান্টরের তথ্য</b>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row">
                            </div>
                            <!-- Loop over the guarantor pages -->
                            @foreach($data['guarantor']['pages'] as $page)
                            @php
                            // dd($page);
                            @endphp
                            <div class="col-md-12">
                                <h6>Guarantor Page: {{ $page['label'] ?? 'N/A' }}
                                </h6>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_name_english']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_name_english']['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_nid_number']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_nid_number']['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_dob']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_dob']['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_father_name']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_father_name']['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_mother_name']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_mother_name']['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_relationship']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_relationship']['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['is_nominee_guarantor_different']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['is_nominee_guarantor_different']['value'][0]
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>
                            @php
                            $url = 'https://wegro-agent-production.s3.ap-southeast-1.amazonaws.com/';
                            $nominee_front_image = $page['values']['guarantor_trade_licence_upload']['value'] ?? null;
                            $nominee_back_image = $page['values']['police_clearance']['value'] ?? null;

                            $frontImageUrl = $front_image ? $url . $nominee_front_image :
                            '/api/placeholder/400/300';
                            $backImageUrl = $back_image ? $url . $nominee_back_image :
                            '/api/placeholder/400/300';
                            @endphp
                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['guarantor_trade_licence_upload']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <img src="{{ $frontImageUrl ?? 'N/A' }}" alt="nominee NID Front"
                                        class="farmer-image">
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>

                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values']['police_clearance']['label']
                                        ?? 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <img src="{{ $backImageUrl ?? 'N/A' }}" alt="nominee NID Front"
                                        class="farmer-image">
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <!-- Accordion 4: Farmer_type কৃষকের ধরণ -->
                <div class="card">
                    <div class="card-header" id="headingFour">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour"
                                aria-expanded="false" aria-controls="collapseFour">
                                <b>4. কৃষকের ধরণ</b>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row">
                                <b>কৃষকের ধরণ</b>
                            </div>
                            <!-- Iterate over the address fields -->
                            @foreach($data['assessment_info']['pages'] as $page)
                            @php
                            // dd($page);
                            @endphp
                            @foreach([
                            'assessment_info',
                            'farmer_has_bank',
                            'farmer_loan_wegro',
                            'present_loan_farmer',
                            'present_loan_mfi',
                            'has_cows',
                            'has_goats',
                            'has_goats',
                            'count_cows',
                            'count_goats',
                            'type_farming_jomi',
                            'cultivated_own_land',
                            'cultivated_rented_land',
                            'mfi_amount_loan',
                            'amount_loan_taken',
                            'current_loan_amount',
                            ] as $field)
                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values'][$field]['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Accordion 5: Farmer_type প্রোজেক্ট এর ধরণ -->
                <div class="card">
                    <div class="card-header" id="headingFive">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive"
                                aria-expanded="false" aria-controls="collapseFive">
                                <b>5. প্রোজেক্ট এর ধরণ</b>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row">
                                <b>প্রোজেক্ট এর ধরণ</b>
                            </div>
                            <!-- Iterate over the address fields -->
                            @foreach($data['project_loan_details']['pages'] as $page)
                            @php
                            // dd($page);
                            @endphp
                            @foreach([
                            'crop_selection_single',
                            'crop_time',
                            'requested_loan_amount',
                            'requested_loan_amount_fieldOfficer',
                            ] as $field)
                            <div class="row detail-row">
                                <div class="col-md-4">
                                    <span>{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span>{{
                                        $page['values'][$field]['value']
                                        ?? 'N/A'
                                        }}</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-outline-dark">
                                        <i class="ti-comment-alt"> Comment</i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            @endif
            {{-- @endforeach --}}
        </div>
    </div>
</div>
@endsection