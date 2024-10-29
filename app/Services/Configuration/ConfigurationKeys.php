<?php

namespace App\Services\Configuration;

enum ConfigurationKeys: string
{
    case RESUBMIT_LIMIT = 'resubmit_limit';
    case RANDOM_QC_PASS_PERCENT = 'random_qc_pass_percent';
    case RANDOM_QC_FAIL_PERCENT = 'random_qc_fail_percent';
    case MACHINE_QC_THRESHOLD = 'machine_qc_threshold';
    case APPLICATION_MAX_OPERATION_TIME = 'application_max_operation_time';
    case DRAFT_FORM_LIFETIME = 'draft_form_lifetime';
    case TERMS_AND_CONDITIONS = 'terms_and_conditions';
    case SINGLE_NID_APPLICATION_LIMIT = 'single_nid_application_limit';
}
