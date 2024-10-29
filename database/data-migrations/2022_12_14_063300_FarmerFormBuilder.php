<?php

namespace Database\DataMigrations;

use App\Helpers\NumberRange;
use App\Models\Field;
use App\Models\Page;
use App\Models\Section;
use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\Fields\CheckBoxField;
use App\Services\Application\Form\Fields\FieldType;
use App\Services\Application\Form\Fields\McqOption;
use App\Services\Application\Form\Fields\RadioField;
use App\Services\Application\Form\ValidationRules\DateValidationRule;
use App\Services\Application\Form\ValidationRules\Rule;
use App\Services\Application\Form\ValidationRules\StringValidationRule;
use App\Services\Application\Form\ValidationRules\NumberValidationRule;
use App\Services\Application\Step\StepType;
use App\Services\wegro\BankLoanRepeater;
use App\Services\wegro\CoSocietyLoanRepeater;
use App\Services\wegro\ExperienceRepeater;
use App\Services\wegro\LocalLoanRepeater;
use App\Services\wegro\MfiLoanRepeater;
use App\Services\wegro\OtherLoanRepeater;
use App\Services\wegro\SpouseRepeater;
use Illuminate\Support\Str;
use Polygontech\DataMigration\Contracts\DataMigrationInterface;

class FarmerFormBuilder implements DataMigrationInterface
{
    /**
     * Run the migration.
     *
     * @return null | string
     */
    public function handle()
    {
        $order = 0;
        $this->storeOtpSection(++$order);
        // $this->storeFarmerTypeSection(++$order);
        $this->storeNidSection(++$order);
        $this->storeGuarantorSection(++$order);
        $this->storeGuarantorOtpSection(++$order);
        // $this->storeNomineeSection(++$order);
        $this->storeAssessmentInfoSection(++$order);
        $this->storeProjectLoanDetails(++$order);

        // $this->storeFinancialInformationSection(++$order);
        // $this->storeLifestyleInformationSection(++$order);
        // $this->storeProfessionalInformationSection(++$order);
        // $this->storeProductivityInformationSection(++$order);
        // $this->storeProductionToolsInformationSection(++$order);
        // $this->storeStackHolderInformationSection(++$order);
        // $this->storeDisasterManagementSection(++$order);
        // $this->storePreviousExperienceSection(++$order);

        $this->storePreviewSection(++$order);
        //$this->storeSignatureSection(++$order);
        //$this->storeSuccessSection(++$order);
    }

    private function storeOtpSection($order)
    {
        Section::create([
            'label' => 'OTP',
            'order' => $order,
            'slug' => Str::slug('otp'),
            'type' => StepType::OTP,
        ]);
    }

    private function storeFarmerTypeSection($order)
    {
        $section = Section::create([
            'label' => 'চাষের ধরন',
            'order' => $order,
            'slug' => 'farming_type',
            'type' => StepType::SURVEY
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'চাষের ধরন',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
            'slug' => 'farming_type',
        ]);
        $optionOne = new McqOption(label: 'শস্য উৎপাদন', value: 'crop');
        $optionTwo = new McqOption(label: 'গবাদিপশু পালন', value: 'cattle');
        $radio_field = (new CheckBoxField(id: 1, label: '', slug: '', order: 1))->addOption($optionOne)->addOption($optionTwo);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'চাষের ধরন',
                'type' => FieldType::CHECKBOX,
                'order' => 1,
                'slug' => 'farming_type',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson()
            ]
        );
    }

    private function storeNidSection($order)
    {
        $section = Section::create([
            'label' => 'NID',
            'order' => $order,
            'slug' => Str::slug('NID'),
            'type' => 'nid',
        ]);
        Page::create([
            'section_id' => $section->id,
            'label' => 'Nid Images',
            'type' => StepType::NID,
            'order' => 1,
            'slug' => 'nid_images',
        ]);
        Page::create([
            'section_id' => $section->id,
            'label' => 'ছবি',
            'type' => StepType::INFORMATION,
            'order' => 2,
            'slug' => 'selfie_instructions',
            'description' => '<div style="color: #464646;padding:8px"> <div style="text-align: center;"><img src="/Ellipse_181.jpg" width="220px" height="220px" alt="farmer" /></div> <h4 style="font-size: 1rem;">উপরের ছবির মত করে কৃষকের একটি ছবি তুলুন</h4> <p style="font-size: 0.875rem;line-height:0.5">লক্ষ্য রাখুন যাতে-</p> <p style="font-size: 0.875rem;line-height:1.8;text-align:left"> ১। কৃষকের পুরো মুখমণ্ডল স্পষ্ট দেখা যায়।<br /> ২। ছবি তুলার পূর্বে মাস্ক, চশমা, টুপি পড়ে থাকলে তা খুলে নিন।<br /> ৩। ছবি তোলার সময় পর্যাপ্ত আলোর দিকে মুখ করে ছবি তুলুন।<br /> ৪। কৃষকের ছবি স্পষ্ট হতে হবে, ছবি অস্পষ্ট দেখা গেলে তা বাতিল বলে গণ্য হবে। </p> <br /><p style="font-size: 2rem;line-height:0.5; color:red;"> <b>অথবা </b> </p><br /><p style="font-size: 1rem;line-height:0.5;"> <b>ছবি থেকে ছবি তুলে দেয়া যাবে । </b> </p></div>',
        ]);

        Page::create([
            'section_id' => $section->id,
            'label' => 'ছবি',
            'type' => StepType::SELFIE,
            'order' => 3,
            'slug' => 'selfie_images',
        ]);

        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'ব্যক্তিগত তথ্য',
            'type' => StepType::FORM_SECTION,
            'order' => 4,
            'slug' => 'farmer_info',
        ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নাম (বাংলা)',
        //         'type' => FieldType::STRING,
        //         'order' => 1,
        //         'slug' => 'nid_name_bangla',
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyBanglaCharacters: true))->toJson()
        //     ]
        // );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের নাম (ইংরেজি)',
                'type' => FieldType::STRING,
                'order' => 5,
                'slug' => 'nid_name_english',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
            ->addOption(new McqOption(label: 'পুরুষ', value: 'Male'))
            ->addOption(new McqOption(label: 'মহিলা', value: 'Female'))
            ->addOption(new McqOption(label: 'অন্যান্য', value: 'Others'));
        Field::create([
            'page_id' => $page->id,
            'slug' => 'farmer_gender',
            'label' => 'কৃষকের জেন্ডার',
            'type' => FieldType::DROPDOWN,
            'order' => 7,
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
        ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'পিতার নাম (বাংলা)',
        //         'type' => FieldType::STRING,
        //         'order' => 3,
        //         'slug' => 'nid_father_name_bangla',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isAlphaSupported: false, containOnlyBanglaCharacters: true))->toJson()
        //     ]
        // );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'NID নাম্বার',
                'type' => FieldType::STRING,
                'order' => 8,
                'slug' => 'nid_number',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false,length: new NumberRange(lengths: [10, 13, 17]),slug: 'nid_number'))->toJson()
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের জন্ম তারিখ',
                'type' => FieldType::DATE,
                'order' => 11,
                'slug' => 'nid_dob',
                'rules' => (new DateValidationRule())->setBefore('-18 years')->toJson()
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের পিতার নাম (ইংরেজি)',
                'type' => FieldType::STRING,
                'order' => 12,
                'slug' => 'nid_father_name_english',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'পিতার পেশা',
        //         'type' => FieldType::STRING,
        //         'order' => 5,
        //         'slug' => 'nid_father_profession',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isAlphaSupported: true))->toJson()
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'মাতার নাম (বাংলা)',
        //         'type' => FieldType::STRING,
        //         'order' => 6,
        //         'slug' => 'nid_mother_name_bangla',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isAlphaSupported: false, containOnlyBanglaCharacters: true))->toJson()
        //     ]
        // );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের মাতার নাম (ইংরেজি)',
                'type' => FieldType::STRING,
                'order' => 13,
                'slug' => 'nid_mother_name_english',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'মাতার পেশা',
        //         'type' => FieldType::STRING,
        //         'order' => 8,
        //         'slug' => 'nid_mother_profession',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isAlphaSupported: true))->toJson()
        //     ]
        // );

        
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'জন্ম স্থান',
        //         'type' => FieldType::STRING,
        //         'order' => 14,
        //         'slug' => 'nid_pob',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isAlphaSupported: true))->toJson()
        //     ]
        // );

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'ইসলাম', value: 'Islam'))
        //     ->addOption(new McqOption(label: 'হিন্দু', value: 'Hinduism'))
        //     ->addOption(new McqOption(label: 'খ্রিস্টান', value: 'Christian'))
        //     ->addOption(new McqOption(label: 'বুদ্ধ', value: 'Buddhism'))
        //     ->addOption(new McqOption(label: 'অন্যান্য', value: 'Others'));
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nid_religion',
        //     'label' => 'ধর্ম',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 41,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'ইমেল (যদি থাকে)',
        //         'type' => FieldType::STRING,
        //         'order' => 12,
        //         'slug' => 'email',
        //         'rules' => (new StringValidationRule(isRequired: false))->toJson()
        //     ]
        // );
        // $optionOne = new McqOption(label: 'বাংলাদেশী', value: 'bangladeshi');
        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 8))->addOption($optionOne);
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'জাতীয়তা',
        //         'type' => FieldType::DROPDOWN,
        //         'order' => 13,
        //         'slug' => 'nationality',
        //         'possible_values' => $radioField->getOptionsJson(),
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 14))
            ->addOption(new McqOption(label: 'বিবাহিত', value: 'Married'))
            ->addOption(new McqOption(label: 'অবিবাহিত', value: 'Single'))
            ->addOption(new McqOption(label: 'তালাকপ্রাপ্ত', value: 'Divorced'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'বৈবাহিক অবস্থা',
                'type' => FieldType::DROPDOWN,
                'order' => 14,
                'slug' => 'marital_status',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'Married', field: $field->slug, page: $page->slug);
        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'spouse',
            'label' => 'স্বামী/স্ত্রীর তথ্য',
            'type' => FieldType::GROUP,
            'order' => 15,
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'is_repeatable' => 1,
            'repeatable_text' => "আরও স্বামী/স্ত্রী আছে ",
            'repeatable_class' => SpouseRepeater::class,
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField->id,
                'label' => 'স্বামী/স্ত্রীর নাম',
                'type' => FieldType::STRING,
                'order' => 16,
                'slug' => 'name',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField->id,
        //         'label' => 'স্বামী/স্ত্রীর NID নাম্বার',
        //         'type' => FieldType::STRING,
        //         'order' => 18,
        //         'slug' => 'nid_no',
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [10, 13, 17])))->toJson()
        //     ]
        // );
        // $options = [];
        // $options[] = new McqOption(label: 'নিঃসন্তান', value: '0');
        // $options[] = new McqOption(label: '১', value: '1');
        // $options[] = new McqOption(label: '২', value: '2');
        // $options[] = new McqOption(label: '৩', value: '3');
        // $options[] = new McqOption(label: '৪', value: '4');
        // $options[] = new McqOption(label: '৫', value: '5');
        // $options[] = new McqOption(label: '৬', value: '6');
        // $options[] = new McqOption(label: '৭', value: '7');
        // $options[] = new McqOption(label: '৮', value: '8');
        // $options[] = new McqOption(label: '৯', value: '9');
        // $options[] = new McqOption(label: '১০', value: '10');

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 9))->setOptions($options);
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'সন্তান-সন্তানাদির সংখ্যা',
        //         'type' => FieldType::DROPDOWN,
        //         'order' => 19,
        //         'slug' => 'children_count',
        //         'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //         'possible_values' => $radioField->getOptionsJson(),
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );

        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'permanent_address',
            'label' => 'স্থায়ী ঠিকানা (NID অনুযায়ী)',
            'type' => FieldType::GROUP,
            'order' => 17,
        ]);

        Field::create([
            'page_id' => $page->id,
            'slug' => 'registered_division',
            'label' => 'বিভাগ (NID অনুযায়ী)',
            'type' => FieldType::DROPDOWN,
            'order' => 18,
            'cached_key' => 'divisions',
            'group_id' => $groupField->id,
            'rules' => (new Rule())->toJson(),
            'dependent_field' => 'registered_district',
        ]);

        Field::create([
            'page_id' => $page->id,
            'slug' => 'registered_district',
            'label' => 'জেলা/শহর (NID অনুযায়ী)',
            'type' => FieldType::DROPDOWN,
            'order' => 19,
            'cached_key' => 'districts',
            'group_id' => $groupField->id,
            'rules' => (new Rule())->toJson(),
            'dependent_field' => 'registered_thana',
        ]);

        Field::create([
            'page_id' => $page->id,
            'slug' => 'registered_thana',
            'label' => 'উপজেলা (NID অনুযায়ী)',
            'type' => FieldType::DROPDOWN,
            'order' => 20,
            'cached_key' => 'thanas',
            'group_id' => $groupField->id,
            'rules' => (new StringValidationRule())->toJson(),

        ]);
        Field::create([
            'page_id' => $page->id,
            'slug' => 'registered_village',
            'label' => 'গ্রাম',
            'type' => FieldType::STRING,
            'order' => 21,
            'group_id' => $groupField->id,
            'rules' => (new StringValidationRule())->toJson(),

        ]);
        Field::create([
            'page_id' => $page->id,
            'slug' => 'registered_post_office',
            'label' => 'পোস্ট অফিস',
            'type' => FieldType::STRING,
            'order' => 22,
            'group_id' => $groupField->id,
            'rules' => (new StringValidationRule())->toJson()
        ]);
        Field::create([
            'page_id' => $page->id,
            'slug' => 'registered_post_code',
            'label' => 'পোস্ট কোড',
            'type' => FieldType::STRING,
            'order' => 23,
            'group_id' => $groupField->id,
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [4])))->toJson()
        ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'registered_union',
        //     'label' => 'ইউনিয়ন (ইংরেজি)(NID অনুযায়ী)',
        //     'type' => FieldType::STRING,
        //     'order' => 22,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'registered_road',
        //     'label' => 'গ্রাম/রাস্তা (ইংরেজি)(NID অনুযায়ী)',
        //     'type' => FieldType::STRING,
        //     'order' => 26,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);

        // $option = (new McqOption(label: 'স্থায়ী ঠিকানা এবং বর্তমান ঠিকানা একই।', value: 'yes'));
        // $field = (new CheckBoxField(id: 1, label: '', slug: '', order: 20))->addOption($option);
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'is_permanent_current_address_same',
        //     'label' => 'স্থায়ী ঠিকানা এবং বর্তমান ঠিকানা একই।',
        //     'type' => FieldType::CHECKBOX,
        //     'order' => 27,
        //     'possible_values' => $field->getOptionsJson(),
        //     'rules' => (new Rule(isRequired: false))->toJson(),
        // ]);
        // $condition = new Condition(section: $section->slug, value: [], field: $field->slug, page: $page->slug);

        // $groupField2 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_address',
        //     'label' => 'বর্তমান ঠিকানা',
        //     'type' => FieldType::GROUP,
        //     'order' => 28,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_division',
        //     'label' => 'বিভাগ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 29,
        //     'cached_key' => 'divisions',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'current_district',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_district',
        //     'label' => 'জেলা/শহর',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 30,
        //     'cached_key' => 'districts',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'current_thana',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_thana',
        //     'label' => 'থানা',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 31,
        //     'cached_key' => 'thanas',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),

        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_post_office',
        //     'label' => 'পোস্ট অফিস',
        //     'type' => FieldType::STRING,
        //     'order' => 32,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_union',
        //     'label' => 'ইউনিয়ন',
        //     'type' => FieldType::STRING,
        //     'order' => 33,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'current_road',
        //     'label' => 'গ্রাম/রাস্তা',
        //     'type' => FieldType::STRING,
        //     'order' => 34,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);

        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        //     ->addOption(new McqOption(label: 'না', value: 'no'));
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'case',
        //     'label' => 'কোন মামলা জারি আছে কি?',
        //     'type' => FieldType::RADIO,
        //     'order' => 35,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visibility_dependent_field' => json_encode(['was_in_jail'])
        // ]);
        // $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        //     ->addOption(new McqOption(label: 'না', value: 'no'));

        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'was_in_jail',
        //     'label' => 'সাজাপ্রাপ্ত?',
        //     'type' => FieldType::RADIO,
        //     'order' => 36,
        //     'rules' => (new Rule(isRequired: false))->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     'visibility_dependent_field' => json_encode(['description'])
        // ]);
        // $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'description',
        //     'label' => 'বিবরণ',
        //     'type' => FieldType::STRING,
        //     'order' => 37,
        //     'rules' => (new StringValidationRule(isRequired: false))->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        //     ->addOption(new McqOption(label: 'না', value: 'no'));
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'trade_licence',
        //     'label' => 'ট্রেড লাইসেন্স আছে কি?',
        //     'type' => FieldType::RADIO,
        //     'order' => 38,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visibility_dependent_field' => json_encode(['trade_licence_group'])
        // ]);
        // $conditionTl = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        // $groupField = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'trade_licence_group',
        //     'label' => 'ট্রেড লাইসেন্স',
        //     'type' => FieldType::GROUP,
        //     'order' => 39,
        //     'visible_if' => (new ConditionWrapper(frontend: $conditionTl))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_name',
        //     'label' => 'ব্যবসার নাম',
        //     'type' => FieldType::STRING,
        //     'order' => 40,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);
        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'ছোট', value: 'small'))
        //     ->addOption(new McqOption(label: 'বড়', value: 'large'))
        //     ->addOption(new McqOption(label: 'মাঝারি', value: 'medium'));
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_type',
        //     'label' => 'ব্যবসার ধরণ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 41,
        //     'group_id' => $groupField->id,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        // ]);

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'এস এম ই', value: 'sme'))
        //     ->addOption(new McqOption(label: 'পাইকারি', value: 'retail'))
        //     ->addOption(new McqOption(label: 'কৃষি', value: 'agri'))
        //     ->addOption(new McqOption(label: 'কর্পোরেট', value: 'corporate'));
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_sector',
        //     'label' => 'ব্যবসার সেক্টর',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 42,
        //     'group_id' => $groupField->id,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'employee_count',
        //     'label' => 'কর্মীর সংখ্যা',
        //     'type' => FieldType::STRING,
        //     'order' => 43,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'farmer_business_address',
        //     'label' => 'ঠিকানা',
        //     'type' => FieldType::STRING,
        //     'order' => 44,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);


        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'নিজের', value: 'self'))
        //     ->addOption(new McqOption(label: 'অন্যের', value: 'other'));

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_ownership',
        //     'label' => 'ব্যবসার স্থান এর মালিকানা',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 41,
        //     'group_id' => $groupField->id,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_total_area',
        //     'label' => 'ব্যবসার জায়গার পরিমাণ (শতক)',
        //     'type' => FieldType::STRING,
        //     'order' => 46,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_sales',
        //     'label' => 'ব্যাবসা প্রতিষ্ঠানের বিক্রি',
        //     'type' => FieldType::STRING,
        //     'order' => 47,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_cost',
        //     'label' => 'ব্যাবসা প্রতিষ্ঠানের খরচ',
        //     'type' => FieldType::STRING,
        //     'order' => 48,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'ট্রেড লাইসেন্স',
        //         'type' => FieldType::FILE,
        //         'order' => 55,
        //         'slug' => 'trade_licence_upload',
        //         'visible_if' => (new ConditionWrapper(frontend: $conditionTl))->toJson(),
        //         'rules' => (new Rule(
        //             false
        //         ))->toJson(),
        //     ]
        // );

        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        //     ->addOption(new McqOption(label: 'না', value: 'no'));

        //     $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_loan',
        //     'label' => 'লোন আছে কি?',
        //     'type' => FieldType::RADIO,
        //     'order' => 49,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visibility_dependent_field' => json_encode(['business_loan_emi'])
        // ]);
        // $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'business_loan_emi',
        //     'label' => 'ই এম আই ',
        //     'type' => FieldType::STRING,
        //     'order' => 50,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        //     ->addOption(new McqOption(label: 'না', value: 'no'));

        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'other_liabilities',
        //     'label' => 'অন্যান্য দায়বদ্ধতা আছে কি?',
        //     'type' => FieldType::RADIO,
        //     'order' => 51,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visibility_dependent_field' => json_encode(['other_liabilities_source, other_liabilities_amount'])
        // ]);
        // $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        // $groupField = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'other_liabilities_information',
        //     'label' => 'অন্যান্য দায়বদ্ধতা',
        //     'type' => FieldType::GROUP,
        //     'order' => 52,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'other_liabilities_source',
        //     'label' => 'উৎস',
        //     'type' => FieldType::STRING,
        //     'order' => 53,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isSpecialCharacterSupported: false))->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'other_liabilities_amount',
        //     'label' => 'পরিমাণ',
        //     'type' => FieldType::STRING,
        //     'order' => 54,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);


        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'পুলিশ ক্লিয়ারেন্স',
        //         'type' => FieldType::FILE,
        //         'order' => 56,
        //         'slug' => 'police_clearance',
        //         'rules' => (new Rule(
        //             false
        //         ))->toJson(),
        //     ]
        // );
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের কার্ড/প্রত্যয়ন এর ছবি (যদি থাকে)',
                'type' => FieldType::FILE,
                'order' => 60,
                'slug' => 'character_certificate',
                'rules' => (new Rule(
                    false
                ))->toJson(),
            ]
        );
    }

    private function storeGuarantorSection($order)
    {
        $section = Section::create([
            'label' => 'নমিনী তথ্য',
            'order' => $order,
            'slug' => Str::slug('Guarantor'),
            'type' => StepType::FORM_SECTION,
        ]);
        
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'নমিনী তথ্য',
            'type' => StepType::FORM_SECTION,
            'order' => 1,
            'slug' => 'guarantor_info',
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর নাম (ইংরেজি)',
                'type' => FieldType::STRING,
                'order' => 1,
                'slug' => 'guarantor_name_english',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর ফোন নাম্বার',
                'type' => FieldType::MOBILE,
                'order' => 2,
                'slug' => 'guarantor_phone_number',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequiredMessage: 'নমিনীর ফোন নাম্বার is required'))->toJson()
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর NID নাম্বার',
                'type' => FieldType::STRING,
                'order' => 3,
                'slug' => 'guarantor_nid_number',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [10, 13, 17]), slug:'guarantor_nid_number'))->toJson()
            ]
        );
                
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'গ্যারান্টারের ঠিকানা',
        //         'type' => FieldType::STRING,
        //         'order' => 2,
        //         'slug' => 'guarantor_address',
        //         'rules' => (new StringValidationRule())->toJson(),
        //     ]
        // );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর জন্ম তারিখ',
                'type' => FieldType::DATE,
                'order' => 5,
                'slug' => 'guarantor_dob',
                'rules' => (new DateValidationRule())->setBefore('-18 years')->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনীর জন্মস্থান (ইংরেজি)(NID অনুযায়ী)',
        //         'type' => FieldType::STRING,
        //         'order' => 6,
        //         'slug' => 'guarantor_pob',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
        //     ]
        // );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর পিতার নাম (ইংরেজি)',
                'type' => FieldType::STRING,
                'order' => 7,
                'slug' => 'guarantor_father_name',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর মাতার নাম (ইংরেজি)',
                'type' => FieldType::STRING,
                'order' => 8,
                'slug' => 'guarantor_mother_name',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson()
            ]
        );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 9))
            ->addOption(new McqOption(label: 'স্বামী/স্ত্রী (Husband/Wife)', value: 'Spouse'))
            ->addOption(new McqOption(label: 'মা/বাবা (Father/Mother)', value: 'Parents'))
            ->addOption(new McqOption(label: 'আত্মীয় (Relative)', value: 'Relative'))
            ->addOption(new McqOption(label: 'প্রতিবেশী (Neighbour)', value: 'Neighbour'))
            ->addOption(new McqOption(label: 'অন্যান্য (Other)', value: 'Other'));
        Field::create([
            'page_id' => $page->id,
            'slug' => 'guarantor_relationship',
            'label' => 'নমিনীর সাথে কৃষকের সম্পর্ক',
            'type' => FieldType::DROPDOWN,
            'order' => 9,
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
        ]);

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'ইসলাম', value: 'Islam'))
        //     ->addOption(new McqOption(label: 'হিন্দু', value: 'Hinduism'))
        //     ->addOption(new McqOption(label: 'খ্রিস্টান', value: 'Christian'))
        //     ->addOption(new McqOption(label: 'বুদ্ধ', value: 'Buddhism'))
        //     ->addOption(new McqOption(label: 'অন্যান্য', value: 'Others'));
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_religion',
        //     'label' => 'ধর্ম',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 41,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        // ]);

        // $optionOne = new McqOption(label: 'বিবাহিত', value: 'married');
        // $optionTwo = new McqOption(label: 'অবিবাহিত', value: 'single');

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))->addOption($optionOne)->addOption($optionTwo);
        // $field = Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'বৈবাহিক অবস্থা',
        //         'type' => FieldType::DROPDOWN,
        //         'order' => 7,
        //         'slug' => 'guarantor_marital_status',
        //         'possible_values' => $radioField->getOptionsJson(),
        //         'rules' => (new Rule())->toJson(),
        //         'visibility_dependent_field' => json_encode(['guarantor_children_count'])
        //     ]
        // );
        // $condition = new Condition(section: $section->slug, value: 'married', field: $field->slug, page: $page->slug);
        // $groupField = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_spouse',
        //     'label' => 'স্বামী/স্ত্রীর তথ্য',
        //     'type' => FieldType::GROUP,
        //     'order' => 8,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     'is_repeatable' => 1,
        //     'repeatable_text' => "আরও স্বামী/স্ত্রী আছে ",
        //     'repeatable_class' => SpouseRepeater::class,
        // ]);
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField->id,
        //         'label' => 'স্বামী/স্ত্রীর নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 9,
        //         'slug' => 'guarantor_name',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson()
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField->id,
        //         'label' => 'স্বামী/স্ত্রীর NID নাম্বার',
        //         'type' => FieldType::STRING,
        //         'order' => 10,
        //         'slug' => 'guarantor_nid_no',
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [10, 13, 17])))->toJson()
        //     ]
        // );
        // $options = [];
        // $options[] = new McqOption(label: 'নিঃসন্তান', value: '0');
        // $options[] = new McqOption(label: '১', value: '1');
        // $options[] = new McqOption(label: '২', value: '2');
        // $options[] = new McqOption(label: '৩', value: '3');
        // $options[] = new McqOption(label: '৪', value: '4');
        // $options[] = new McqOption(label: '৫', value: '5');
        // $options[] = new McqOption(label: '৬', value: '6');
        // $options[] = new McqOption(label: '৭', value: '7');
        // $options[] = new McqOption(label: '৮', value: '8');
        // $options[] = new McqOption(label: '৯', value: '9');
        // $options[] = new McqOption(label: '১০', value: '10');

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))->setOptions($options);
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'সন্তান-সন্তানাদির সংখ্যা',
        //         'type' => FieldType::DROPDOWN,
        //         'order' => 11,
        //         'slug' => 'guarantor_children_count',
        //         'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //         'possible_values' => $radioField->getOptionsJson(),
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'ই-টিন (E-Tin)',
        //         'type' => FieldType::STRING,
        //         'order' => 12,
        //         'slug' => 'guarantor_e_tin',
        //         'rules' => (new StringValidationRule(isNumberSupported: true))->toJson()
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'গ্যারেন্টরের মাতার পেশা',
        //         'type' => FieldType::STRING,
        //         'order' => 16,
        //         'slug' => 'guarantor_mother_profession',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson()
        //     ]
        // );

        // $groupField = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_permanent_address',
        //     'label' => 'গ্যারেন্টরের স্থায়ী ঠিকানা',
        //     'type' => FieldType::GROUP,
        //     'order' => 19,
        // ]);


        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_division',
        //     'label' => 'গ্যারেন্টরের বিভাগ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 20,
        //     'cached_key' => 'divisions',
        //     'group_id' => $groupField->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'guarantor_district',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_district',
        //     'label' => 'গ্যারেন্টরের জেলা/শহর',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 21,
        //     'cached_key' => 'districts',
        //     'group_id' => $groupField->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'guarantor_thana',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_thana',
        //     'label' => 'থানা',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 22,
        //     'cached_key' => 'thanas',
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson(),

        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_post_office',
        //     'label' => 'গ্যারেন্টরের পোস্ট অফিস',
        //     'type' => FieldType::STRING,
        //     'order' => 23,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_union',
        //     'label' => 'গ্যারেন্টরের ইউনিয়ন',
        //     'type' => FieldType::STRING,
        //     'order' => 24,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_road',
        //     'label' => 'গ্যারেন্টরের গ্রাম/রাস্তা',
        //     'type' => FieldType::STRING,
        //     'order' => 25,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);

        // $option = (new McqOption(label: 'গ্যারেন্টরের স্থায়ী ঠিকানা এবং বর্তমান ঠিকানা একই।', value: 'yes'));
        // $field = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: 'is_guarantor_permanent_current_address_same', order: 25))->addOption($option);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'is_guarantor_permanent_current_address_same',
        //     'label' => 'গ্যারেন্টরের স্থায়ী ঠিকানা এবং বর্তমান ঠিকানা একই',
        //     'type' => FieldType::CHECKBOX,
        //     'order' => 26,
        //     'possible_values' => $field->getOptionsJson(),
        //     'rules' => (new Rule(isRequired: false))->toJson(),
        // ]);
        // $condition = new Condition(section: $section->slug, field: $field->slug, value: [], page: $page->slug);
        // $groupField2 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_address',
        //     'label' => 'গ্যারেন্টরের বর্তমান ঠিকানা',
        //     'type' => FieldType::GROUP,
        //     'order' => 27,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_division',
        //     'label' => 'গ্যারেন্টরের বর্তমান বিভাগ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 28,
        //     'cached_key' => 'divisions',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'guarantor_current_district',
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_district',
        //     'label' => 'গ্যারেন্টরের বর্তমান জেলা/শহর',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 29,
        //     'cached_key' => 'districts',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'guarantor_current_thana',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_thana',
        //     'label' => 'থানা',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 30,
        //     'cached_key' => 'thanas',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),

        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_post_office',
        //     'label' => 'গ্যারেন্টরের বর্তমান পোস্ট অফিস',
        //     'type' => FieldType::STRING,
        //     'order' => 31,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_union',
        //     'label' => 'গ্যারেন্টরের বর্তমান ইউনিয়ন',
        //     'type' => FieldType::STRING,
        //     'order' => 32,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_current_road',
        //     'label' => 'গ্যারেন্টরের বর্তমান গ্রাম/রাস্তা',
        //     'type' => FieldType::STRING,
        //     'order' => 33,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);
        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))
        //     ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        //     ->addOption(new McqOption(label: 'না', value: 'no'));
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_trade_licence',
        //     'label' => 'ট্রেড লাইসেন্স আছে কি?',
        //     'type' => FieldType::RADIO,
        //     'order' => 34,
        //     'rules' => (new Rule())->toJson(),
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visibility_dependent_field' => json_encode(['guarantor_trade_licence_group'])
        // ]);
        // $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        // $groupField = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_trade_licence_group',
        //     'label' => 'ট্রেড লাইসেন্স',
        //     'type' => FieldType::GROUP,
        //     'order' => 35,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'guarantor_business_address',
        //     'label' => 'ব্যবসার ঠিকানা',
        //     'type' => FieldType::STRING,
        //     'order' => 36,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'ট্রেড লাইসেন্স',
        //         'type' => FieldType::FILE,
        //         'order' => 37,
        //         'slug' => 'guarantor_trade_licence_upload',
        //         'group_id' => $groupField->id,
        //         'rules' => (new Rule(
        //             false
        //         ))->toJson(),
        //         'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     ]
        // );

        $option = (new McqOption(label: 'নমিনী এবং গ্যারান্টর একই।', value: 'yes'));
        $field = (new CheckBoxField(id: 1, label: '', slug: '', order: 26))->addOption($option);
        Field::create([
            'page_id' => $page->id,
            'slug' => 'is_nominee_guarantor_different',
            'label' => 'নমিনী এবং গ্যারান্টর আলাদা।',
            'type' => FieldType::CHECKBOX,
            'order' => 39,
            'possible_values' => $field->getOptionsJson(),
            'rules' => (new Rule(isRequired: false))->toJson(),
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর NID এর সামনের পাশের ছবি',
                'type' => FieldType::FILE,
                'order' => 57,
                'slug' => 'guarantor_trade_licence_upload',
                'rules' => (new Rule($isRequiredMessage = 'নমিনীর NID এর সামনের পাশের ছবি দেওয়া অত্যাবশ্যক'))->toJson(),
            ]
        );
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'নমিনীর NID এর পেছনের পাশের ছবি',
                'type' => FieldType::FILE,
                'order' => 58,
                'slug' => 'police_clearance',
                'rules' => (new Rule($isRequiredMessage = 'নমিনীর NID এর পেছনের পাশের ছবি দেওয়া অত্যাবশ্যক'))->toJson(),
            ]
        );
    }

    private function storeGuarantorOtpSection($order)
    {
        Section::create([
            'label' => 'গ্যারেন্টরের OTP',
            'order' => $order,
            'slug' => Str::slug('guarantor_otp'),
            'type' => StepType::OTP,
        ]);
    }

    private function storeNomineeSection($order)
    {
        $condition = new Condition(section: 'guarantor', page: 'guarantor_info', field: 'is_nominee_guarantor_different', value: []);
        $section = Section::create([
            'label' => 'নমিনি',
            'order' => $order,
            'slug' => Str::slug('Nominee'),
            'type' => StepType::FORM_SECTION,
            'visible_if' => (new ConditionWrapper(backend: $condition))->toJson()
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'নমিনির তথ্য',
            'type' => StepType::FORM_SECTION,
            'order' => 1,
            'slug' => 'farmer_nominee',
        ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির নাম (ইংরেজি)',
        //         'type' => FieldType::STRING,
        //         'order' => 1,
        //         'slug' => 'nominee_name_english',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, containOnlyEnglishCharacters: true))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 2,
        //         'slug' => 'nominee_phone_number',
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isNumberSupported: true, isRequiredMessage: "নমিনির ফোন নাম্বার is required"))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির পিতার নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 3,
        //         'slug' => 'nominee_father_name',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির মাতার নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 4,
        //         'slug' => 'nominee_mother_name',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির সাথে সম্পর্ক',
        //         'type' => FieldType::STRING,
        //         'order' => 5,
        //         'slug' => 'nominee_relationship',
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির NID নাম্বার',
        //         'type' => FieldType::STRING,
        //         'order' => 6,
        //         'slug' => 'nominee_nid_number',
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [10, 13, 17])))->toJson()
        //     ]
        // );

        // $groupField1 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_permanent_address',
        //     'label' => 'নমিনির স্থায়ী ঠিকানা',
        //     'type' => FieldType::GROUP,
        //     'order' => 7,
        // ]);


        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_division',
        //     'label' => 'নমিনির বিভাগ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 8,
        //     'cached_key' => 'divisions',
        //     'group_id' => $groupField1->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'nominee_district',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_district',
        //     'label' => 'নমিনির জেলা/শহর',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 9,
        //     'cached_key' => 'districts',
        //     'group_id' => $groupField1->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'nominee_thana',
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_thana',
        //     'label' => 'থানা',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 10,
        //     'cached_key' => 'thanas',
        //     'group_id' => $groupField1->id,
        //     'rules' => (new StringValidationRule())->toJson(),

        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_post_office',
        //     'label' => 'নমিনির পোস্ট অফিস',
        //     'type' => FieldType::STRING,
        //     'order' => 11,
        //     'group_id' => $groupField1->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_union',
        //     'label' => 'নমিনির ইউনিয়ন',
        //     'type' => FieldType::STRING,
        //     'order' => 12,
        //     'group_id' => $groupField1->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_road',
        //     'label' => 'নমিনির গ্রাম/রাস্তা',
        //     'type' => FieldType::STRING,
        //     'order' => 13,
        //     'group_id' => $groupField1->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);

        // $options = (new McqOption(label: 'নমিনি স্থায়ী ঠিকানা এবং বর্তমান ঠিকানা একই।', value: 'yes'));
        // $field = (new CheckBoxField(id: 1, label: '', slug: '', order: 22))->addOption($options);
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'is_nominee_permanent_current_address_same',
        //     'label' => 'নমিনি স্থায়ী ঠিকানা এবং বর্তমান ঠিকানা একই।',
        //     'type' => FieldType::CHECKBOX,
        //     'order' => 14,
        //     'possible_values' => $field->getOptionsJson(),
        //     'rules' => (new Rule(isRequired: false))->toJson(),
        // ]);

        // $condition = new Condition(section: $section->slug, field: $field->slug, value: [], page: $page->slug);
        // $groupField2 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_address',
        //     'label' => 'নমিনির বর্তমান ঠিকানা',
        //     'type' => FieldType::GROUP,
        //     'order' => 15,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_division',
        //     'label' => 'নমিনির বর্তমান বিভাগ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 16,
        //     'cached_key' => 'divisions',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'nominee_current_district',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_district',
        //     'label' => 'নমিনির বর্তমান জেলা/শহর',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 17,
        //     'cached_key' => 'districts',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new Rule())->toJson(),
        //     'dependent_field' => 'nominee_current_thana',
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_thana',
        //     'label' => 'থানা',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 18,
        //     'cached_key' => 'thanas',
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),

        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_post_office',
        //     'label' => 'নমিনির বর্তমান পোস্ট অফিস',
        //     'type' => FieldType::STRING,
        //     'order' => 19,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_union',
        //     'label' => 'নমিনির বর্তমান ইউনিয়ন',
        //     'type' => FieldType::STRING,
        //     'order' => 20,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'nominee_current_road',
        //     'label' => 'নমিনির বর্তমান গ্রাম/রাস্তা',
        //     'type' => FieldType::STRING,
        //     'order' => 21,
        //     'group_id' => $groupField2->id,
        //     'rules' => (new StringValidationRule())->toJson(),
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'নমিনির NID',
        //         'type' => FieldType::FILE,
        //         'order' => 22,
        //         'slug' => 'nominee_nid_upload',
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );
    }

    private function storeFinancialInformationSection($order)
    {
        $section = Section::create([
            'label' => 'অর্থনৈতিক তথ্য',
            'order' => $order,
            'slug' => 'financial_information',
            'type' => StepType::FORM_SECTION,
        ]);

        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'অর্থনৈতিক তথ্য',
            'type' => StepType::FORM_SECTION,
            'order' => 1,
            'slug' => 'financial_information',
        ]);

        $option_one = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $option_two = new McqOption(label: 'না', value: 'no');
        $radio_field = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'MFI লোন নিয়েছেন?',
                'type' => FieldType::RADIO,
                'order' => 1,
                'slug' => 'farmer_loan',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
                'visibility_dependent_field' => json_encode(["mfi_loan_payment_regularity"])
            ]);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'mfi_loan',
            'label' => 'MFI লোন',
            'type' => FieldType::GROUP,
            'order' => 2,
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'is_repeatable' => 1,
            'repeatable_text' => "আরও লোন আছে ",
            'repeatable_class' => MfiLoanRepeater::class,
        ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোন প্রদানকারী ব্যক্তি/প্রতিষ্ঠান',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 3,
                'slug' => 'mfi_loan_organization',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোনের পরিমান',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 4,
                'slug' => 'mfi_loan_amount',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'মাসিক কিস্তি',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 5,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'slug' => 'mfi_loan_monthly_emi',
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'শেষ কিস্তি প্রদানের তারিখ',
                'type' => FieldType::DATE,
                'group_id' => $groupField->id,
                'order' => 6,
                'slug' => 'mfi_loan_last_emi_date',
                'rules' => (new DateValidationRule())->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'লোন কার্ড',
        //         'type' => FieldType::FILE,
        //         'order' => 7,
        //         'group_id' => $groupField->id,
        //         'slug' => 'mfi_loan_card',
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );

        // $option_one = new McqOption(label: 'নিয়মিত', value: 'regular');
        // $option_two = new McqOption(label: 'অনিয়মিত', value: 'irregular');
        // $radio_field = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'আপনি  কি MFI লোন পরিশোধে নিয়মিত?',
        //         'type' => FieldType::RADIO,
        //         'order' => 8,
        //         'slug' => 'mfi_loan_payment_regularity',
        //         'possible_values' => $radio_field->getOptionsJson(),
        //         'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //         'rules' => (new Rule(isRequired: false))->toJson(),
        //     ]
        // );

        $option_one = new McqOption(label: 'আছে', value: 'yes');
        $option_two = new McqOption(label: 'নেই', value: 'no');
        $radio_field = (new RadioField(id: 3, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'ব্যাংক লোন আছে?',
                'type' => FieldType::RADIO,
                'order' => 9,
                'slug' => 'has_bank_loan',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);

        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'bank_loan',
            'label' => 'ব্যাংক লোন',
            'type' => FieldType::GROUP,
            'order' => 10,
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'is_repeatable' => 1,
            'repeatable_text' => "আরও লোন আছে ",
            'repeatable_class' => BankLoanRepeater::class,
        ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোন প্রদানকারী ব্যাংক',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 11,
                'slug' => 'loan_bank_name',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোনের পরিমান',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 12,
                'slug' => 'bank_loan_amount',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'মাসিক কিস্তি',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 13,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'slug' => 'bank_loan_monthly_emi',
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'শেষ কিস্তি প্রদানের তারিখ',
                'type' => FieldType::DATE,
                'group_id' => $groupField->id,
                'order' => 14,
                'slug' => 'bank_loan_last_emi_date',
                'rules' => (new DateValidationRule())->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'লোন কার্ড',
        //         'type' => FieldType::FILE,
        //         'order' => 15,
        //         'group_id' => $groupField->id,
        //         'slug' => 'bank_loan_card',
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );

        $option_one = new McqOption(label: 'আছে', value: 'yes');
        $option_two = new McqOption(label: 'নেই', value: 'no');
        $radio_field = (new RadioField(id: 4, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'সমবায় সমিতি এর লোন আছে?',
                'type' => FieldType::RADIO,
                'order' => 16,
                'slug' => 'has_co_society_loan',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);

        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'co_society_loan',
            'label' => 'কো-অপারেটিভ সোসাইটি লোন',
            'type' => FieldType::GROUP,
            'order' => 17,
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'is_repeatable' => 1,
            'repeatable_text' => "আরও লোন আছে ",
            'repeatable_class' => CoSocietyLoanRepeater::class,
        ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোন প্রদানকারী সোসাইটি',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 18,
                'slug' => 'loan_cosociety_name',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোনের পরিমান',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 19,
                'slug' => 'cosociety_loan_amount',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'মাসিক কিস্তি',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 20,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'slug' => 'cosociety_loan_monthly_emi',
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'শেষ কিস্তি প্রদানের তারিখ',
                'type' => FieldType::DATE,
                'group_id' => $groupField->id,
                'order' => 21,
                'slug' => 'cosociety_loan_last_emi_date',
                'rules' => (new DateValidationRule())->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'লোন কার্ড',
        //         'type' => FieldType::FILE,
        //         'order' => 22,
        //         'group_id' => $groupField->id,
        //         'slug' => 'cosociety_loan_card',
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );


        $option_one = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $option_two = new McqOption(label: 'না', value: 'no');
        $radio_field = (new RadioField(id: 5, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'স্থানীয় কারো কাছ থেকে লোন নিয়েছেন?',
                'type' => FieldType::RADIO,
                'order' => 23,
                'slug' => 'has_local_loan',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);

        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'local_loan',
            'label' => 'স্থানীয় লোন',
            'type' => FieldType::GROUP,
            'order' => 24,
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'is_repeatable' => 1,
            'repeatable_text' => "আরও লোন আছে ",
            'repeatable_class' => LocalLoanRepeater::class,
        ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'স্থানীয় লোন প্রদানকারী',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 25,
                'slug' => 'local_loan_person_name',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোনের পরিমান',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 26,
                'slug' => 'local_loan_amount',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'মাসিক কিস্তি',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 27,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'slug' => 'local_loan_monthly_emi',
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'শেষ কিস্তি প্রদানের তারিখ',
                'type' => FieldType::DATE,
                'group_id' => $groupField->id,
                'order' => 28,
                'slug' => 'local_loan_last_emi_date',
                'rules' => (new DateValidationRule())->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'লোন কার্ড',
        //         'type' => FieldType::FILE,
        //         'order' => 29,
        //         'group_id' => $groupField->id,
        //         'slug' => 'local_loan_card',
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );


        $option_one = new McqOption(label: 'আছে', value: 'yes');
        $option_two = new McqOption(label: 'নেই', value: 'no');
        $radio_field = (new RadioField(id: 7, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'যৌথ পরিবারের অন্য কারো কি লোন আছে?',
                'type' => FieldType::RADIO,
                'order' => 30,
                'slug' => 'has_other_family_member_loan',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
                'visibility_dependent_field' => json_encode(["in_debt_count"])
            ]
        );

        $option_one = new McqOption(label: '১ জন', value: '1');
        $option_two = new McqOption(label: '২ জন', value: '2');
        $option_three = new McqOption(label: '৩ জন', value: '3');
        $option_four = new McqOption(label: '৪ জন', value: '4');
        $radio_field = (new RadioField(id: 8, type: FieldType::DROPDOWN, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two)->addOption($option_three)->addOption($option_four);

        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'ঋণগ্রস্থ সদস্য সংখ্যা',
                'type' => FieldType::DROPDOWN,
                'order' => 31,
                'slug' => 'in_debt_count',
                'possible_values' => $radio_field->getOptionsJson(),
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
                'rules' => (new Rule())->toJson(),
            ]);

        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'other_family_member_loan',
            'label' => 'পরিবারের অন্য সদস্যের লোন',
            'type' => FieldType::GROUP,
            'order' => 32,
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'is_repeatable' => 1,
            'repeatable_text' => "আরও লোন আছে ",
            'repeatable_class' => OtherLoanRepeater::class,
        ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'পরিবারের অন্য সদস্য কে লোন প্রদানকারী ব্যক্তি/প্রতিষ্ঠান',
                'type' => FieldType::STRING,
                'order' => 33,
                'slug' => 'other_loan_organization',
                'group_id' => $groupField->id,
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'লোনের পরিমান',
                'type' => FieldType::STRING,
                'order' => 34,
                'slug' => 'other_loan_amount',
                'group_id' => $groupField->id,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'মাসিক কিস্তি',
                'type' => FieldType::STRING,
                'group_id' => $groupField->id,
                'order' => 35,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'slug' => 'other_loan_monthly_emi',
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'শেষ কিস্তি প্রদানের তারিখ',
                'type' => FieldType::DATE,
                'group_id' => $groupField->id,
                'order' => 36,
                'slug' => 'other_loan_last_emi_date',
                'rules' => (new DateValidationRule())->toJson()
            ]
        );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'লোন কার্ড',
        //         'type' => FieldType::FILE,
        //         'order' => 37,
        //         'group_id' => $groupField->id,
        //         'slug' => 'other_loan_card',
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );

        $option_one = new McqOption(label: 'আছে', value: 'yes');
        $option_two = new McqOption(label: 'নেই', value: 'no');
        $radio_field = (new RadioField(id: 9, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($option_one)->addOption($option_two);
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'আয়ের পরিমাণ (বাৎসরিক)?',
                'type' => FieldType::RADIO,
                'order' => 38,
                'slug' => 'has_extra_income',
                'possible_values' => $radio_field->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
                'visibility_dependent_field' => json_encode(["income_source", "income_source", "earning_amount"])
            ]
        );
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'আয়ের উৎস',
                'type' => FieldType::STRING,
                'order' => 39,
                'slug' => 'income_source',
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'আয়ের পরিমান',
                'type' => FieldType::STRING,
                'order' => 40,
                'slug' => 'earning_amount',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
            ]);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'পরিবারের মাসিক আয়ের পরিমান',
                'type' => FieldType::STRING,
                'order' => 41,
                'slug' => 'family_monthly_income',
                'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: false))->toJson(),
            ]);
    }

    private function storeLifestyleInformationSection($order)
    {
        $section = Section::create([
            'label' => 'জীবনযাপনের ধরন',
            'order' => $order,
            'slug' => 'lifestyle_information',
            'type' => StepType::FORM_SECTION,
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'জীবনযাপনের ধরন',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
            'slug' => 'lifestyle_information',
        ]);

        // $optionOne = (new McqOption(label: 'হ্যা', value: 'yes'));
        // $optionTwo = (new McqOption(label: 'না', value: 'no'));
        // $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'এই এলাকার স্থায়ী বাসিন্দা?',
        //     'type' => FieldType::RADIO,
        //     'order' => 1,
        //     'slug' => 'permanent_residence',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visibility_dependent_field' => json_encode(['years_of_residence'])
        // ]);

        // $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        // $dropdownRanges = [
        //     '1-5',
        //     '6-10',
        //     '11-15',
        //     '16-20',
        //     '21-25',
        //     '26-30',
        //     '31-35',
        //     '36-40',
        //     '41-45',
        //     '46-50'
        // ];
        // $dropdownOptions = [];
        // foreach ($dropdownRanges as $range) {
        //     list($lower, $upper) = explode('-', $range);
        //     $label = "{$lower}-{$upper}";
        //     $value = $label;
        //     $dropdownOptions[] = new McqOption(label: $label, value: $value);
        // }
        // $dropdownField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 1))->setOptions($dropdownOptions);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'কত বছর বর্তমান ঠিকানায় বসবাস করছেন?',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 2,
        //     'slug' => 'years_of_residence',
        //     'possible_values' => $dropdownField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        // ]);

        $optionOne = (new McqOption(label: 'হ্যা', value: 'yes'));
        $optionTwo = (new McqOption(label: 'না', value: 'no'));
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'বসবাসরত বাড়ির মালিকানা আপনার?',
            'type' => FieldType::RADIO,
            'order' => 3,
            'slug' => 'permanent_residence_house',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);

        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 4, type: FieldType::RADIO, label: '', slug: '', order: 1))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'জমি আছে কি?',
            'type' => FieldType::RADIO,
            'order' => 4,
            'slug' => 'land_exists',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(['land_type, land_address'])
        ]);

        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        $optionOne = new McqOption(label: 'চাষ', value: 'cultivated');
        $optionTwo = new McqOption(label: 'ভিটা', value: 'vita');
        $optionThree = new McqOption(label: 'বর্গা', value: 'fallow');
        $landTypeField = (new RadioField(id: 5, type: FieldType::DROPDOWN, label: '', slug: '', order: 2))->addOption($optionOne)->addOption($optionTwo)->addOption($optionThree);
        Field::create([
            'page_id' => $page->id,
            'label' => 'কি টাইপ?',
            'type' => FieldType::DROPDOWN,
            'order' => 5,
            'slug' => 'land_type',
            'possible_values' => $landTypeField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'ঠিকানা',
        //     'type' => FieldType::STRING,
        //     'order' => 6,
        //     'slug' => 'land_address',
        //     'rules' => (new StringValidationRule())->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 3, type: FieldType::RADIO, label: '', slug: '', order: 1))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'দোকান/ব্যবসা প্রতিষ্ঠান/কলকারখানা আছে?',
            'type' => FieldType::RADIO,
            'order' => 7,
            'slug' => 'business_establishment',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(['business_address'])
        ]);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'ঠিকানা',
            'type' => FieldType::STRING,
            'order' => 8,
            'slug' => 'business_address',
            'rules' => (new StringValidationRule())->toJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        ]);

        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 6, type: FieldType::RADIO, label: '', slug: '', order: 1))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'মোটর চালিত যানবাহন আছে?',
            'type' => FieldType::RADIO,
            'order' => 9,
            'slug' => 'vehicle_exists',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(['vehicle_cc'])
        ]);

        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        $optionOne = [
            new McqOption(label: '100', value: '100'),
            new McqOption(label: '125', value: '125'),
            new McqOption(label: '150', value: '150'),
            new McqOption(label: '200', value: '200'),
        ];
        $radioField = (new RadioField(id: 7, type: FieldType::DROPDOWN, label: '', slug: '', order: 2))->setOptions($optionOne);
        Field::create([
            'page_id' => $page->id,
            'label' => 'CC কত?',
            'type' => FieldType::DROPDOWN,
            'order' => 10,
            'slug' => 'vehicle_cc',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'বাড়িতে কি TV আছে?',
            'type' => FieldType::RADIO,
            'order' => 11,
            'slug' => 'has_tv',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            // 'visibility_dependent_field' => json_encode(['tv_brand'])
        ]);

        // $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        // $optionOne = [
        //     new McqOption(label: 'Walton', value: 'Walton'),
        //     new McqOption(label: 'Samsung', value: 'Samsung'),
        //     new McqOption(label: 'Sony', value: 'Sony'),
        //     new McqOption(label: 'Rangs', value: 'Rangs'),
        //     new McqOption(label: 'Konka', value: 'Konka'),
        //     new McqOption(label: 'Jamuna', value: 'Jamuna'),
        //     new McqOption(label: 'Others', value: 'Others'),
        // ];
        // $radioField = (new RadioField(id: 9, type: FieldType::DROPDOWN, label: '', slug: '', order: 2))->setOptions($optionOne);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'ব্র্যান্ড',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 12,
        //     'slug' => 'tv_brand',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 24))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'বাড়িতে কি Fridge আছে?',
            'type' => FieldType::RADIO,
            'order' => 13,
            'slug' => 'has_fridge',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            // 'visibility_dependent_field' => json_encode(['fridge_brand'])
        ]);

        // $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        // $optionOne = [
        //     new McqOption(label: 'Walton', value: 'Walton'),
        //     new McqOption(label: 'Rangs', value: 'Rangs'),
        //     new McqOption(label: 'Minister', value: 'Minister'),
        //     new McqOption(label: 'Kelvinator', value: 'Kelvinator'),
        //     new McqOption(label: 'Singer', value: 'Singer'),
        //     new McqOption(label: 'Vision', value: 'Vision'),
        //     new McqOption(label: 'Jamuna', value: 'Jamuna'),
        //     new McqOption(label: 'Others', value: 'Others'),
        // ];
        // $radioField = (new RadioField(id: 9, type: FieldType::DROPDOWN, label: '', slug: '', order: 2))->setOptions($optionOne);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'ব্র্যান্ড',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 14,
        //     'slug' => 'fridge_brand',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        // ]);

        // $optionOne = new McqOption(label: 'আছে', value: 'yes');
        // $optionTwo = new McqOption(label: 'নেই', value: 'no');
        // $radioField = (new RadioField(id: 3, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'বাড়িতে কি Phone আছে?',
        //     'type' => FieldType::RADIO,
        //     'order' => 15,
        //     'slug' => 'has_phone',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visibility_dependent_field' => json_encode(["phone_type"])
        // ]);

        // $optionOne = new McqOption(label: 'স্মার্ট ফোন', value: 'smart_phone');
        // $optionTwo = new McqOption(label: 'বার ফোন', value: 'bar_phone');
        // $radioField = (new RadioField(id: 4, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        // $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'ফোনটি কি ধরনের?',
        //     'type' => FieldType::RADIO,
        //     'order' => 16,
        //     'slug' => 'phone_type',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     'rules' => (new Rule(isRequired: false))->toJson(),
        // ]);

        // $optionOne = new McqOption(label: 'আছে', value: 'yes');
        // $optionTwo = new McqOption(label: 'নেই', value: 'no');
        // $radioField = (new RadioField(id: 9, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        // $field = Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'ব্যাংক অ্যাকাউন্ট আছে?',
        //     'type' => FieldType::RADIO,
        //     'order' => 17,
        //     'slug' => 'has_bank_account',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visibility_dependent_field' => json_encode(["bank_info"])
        // ]);

        // $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);

        // $groupField = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'bank_info',
        //     'label' => 'ব্যাংকের',
        //     'type' => FieldType::GROUP,
        //     'order' => 18,
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     'is_repeatable' => 0,
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'ব্যাংকের নাম ',
        //     'type' => FieldType::STRING,
        //     'order' => 19,
        //     'slug' => 'bank_name',
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'bank_district',
        //     'label' => 'জেলার নাম',
        //     'type' => FieldType::STRING,
        //     'order' => 20,
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'শাখার নাম',
        //     'type' => FieldType::STRING,
        //     'order' => 21,
        //     'slug' => 'branch_name',
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'অ্যাকাউন্টের নাম',
        //     'type' => FieldType::STRING,
        //     'order' => 22,
        //     'slug' => 'account_name',
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'অ্যাকাউন্ট নাম্বার',
        //     'type' => FieldType::STRING,
        //     'order' => 23,
        //     'slug' => 'account_number',
        //     'group_id' => $groupField->id,
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 3, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'বাড়িতে কি Computer/Laptop আছে?',
            'type' => FieldType::RADIO,
            'order' => 24,
            'slug' => 'has_pc',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            // 'visibility_dependent_field' => json_encode(["pc_type"])
        ]);

        // $options = [];
        // $options[] = new McqOption(label: 'HP', value: 'hp');
        // $options[] = new McqOption(label: 'ASUS', value: 'asus');
        // $options[] = new McqOption(label: 'Lenevo', value: 'lenevo');
        // $options[] = new McqOption(label: 'Dell', value: 'dell');
        // $options[] = new McqOption(label: 'Samsung', value: 'samsung');
        // $options[] = new McqOption(label: 'Desktop', value: 'desktop');
        // $options[] = new McqOption(label: 'Others', value: 'others');
        // $radioField = (new RadioField(id: 4, type: FieldType::DROPDOWN, label: '', slug: '', order: 22))->setOptions($options);
        // $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'Computer/Laptop কি ধরনের?',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 25,
        //     'slug' => 'pc_type',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     'rules' => (new Rule())->toJson(),
        // ]);
    }

    private function storeProfessionalInformationSection(int $order)
    {
        $condition = new Condition(section: "farming_type", value: ["crop"]);
        $section = Section::create([
            'label' => 'পেশার তথ্য',
            'order' => $order,
            'slug' => 'professional_information',
            'type' => StepType::FORM_SECTION,
            'visible_if' => (new ConditionWrapper(backend: $condition))->toJson(),
        ]);

        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'পেশার তথ্য',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
            'slug' => 'professional_information',
        ]);
        $options = [];
        $options[] = new McqOption(label: 'আমন', value: 'amon');
        $options[] = new McqOption(label: 'ইরি', value: 'iri');
        $options[] = new McqOption(label: 'গম', value: 'wheat');
        $options[] = new McqOption(label: 'ধান', value: 'paddy');
        $options[] = new McqOption(label: 'ভূট্টা', value: 'corn');
        $options[] = new McqOption(label: 'সরিষা', value: 'mustard');
        $options[] = new McqOption(label: 'আলু', value: 'pottato');
        $options[] = new McqOption(label: 'ডাল', value: 'lentis');
        $options[] = new McqOption(label: 'যব', value: 'barley');
        $options[] = new McqOption(label: 'চা', value: 'tea');
        $options[] = new McqOption(label: 'রাবার', value: 'rubber');
        $options[] = new McqOption(label: 'পাট', value: 'jute');
        $options[] = new McqOption(label: 'মশলা', value: 'spice');
        $options[] = new McqOption(label: 'সবজি', value: 'vegetable');
        $options[] = new McqOption(label: 'অন্যান্য শস্য', value: 'others');
        $checkboxField = (new CheckBoxField(id: 1, label: '', slug: '', order: 14))->setOptions($options);
        Field::create([
            'page_id' => $page->id,
            'label' => '১২ মাসে কৃষক কি কি শস্য উৎপাদন করে?',
            'type' => FieldType::CHECKBOX,
            'order' => 1,
            'slug' => 'crop_selection',
            'possible_values' => $checkboxField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visible_if' => (new ConditionWrapper(backend: $condition))->toJson(),
        ]);
        Field::create([
            'page_id' => $page->id,
            'label' => 'অন্যান্য শস্য',
            'type' => FieldType::STRING,
            'order' => 2,
            'slug' => 'others',
            'rules' => (new StringValidationRule(isNumberSupported: false, isRequired: false))->toJson(),
        ]);

        Field::create([
            'page_id' => $page->id,
            'label' => 'ফার্মার কার্ড আপলোড করুন (ঐচ্ছিক)',
            'type' => FieldType::FILE,
            'order' => 3,
            'slug' => 'farmer_card_image',
            'rules' => (new Rule())->toJson(),
        ]);
    }

    private function storeProductivityInformationSection($order)
    {
        $condition = new Condition(section: "farming_type", value: ["cattle"]);

        $section = Section::create([
            'label' => 'উৎপাদন তথ্য',
            'order' => $order,
            'slug' => 'production_information',
            'type' => StepType::FORM_SECTION,
            'visible_if' => (new ConditionWrapper(backend: $condition))->toJson()
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'উৎপাদন তথ্য',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
            'slug' => 'production_information',
        ]);

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 22))
            ->addOption(new McqOption(label: 'গাভী পালন', value: 'cow'))
            ->addOption(new McqOption(label: 'ছাগল পালন', value: 'goat'))
            ->addOption(new McqOption(label: 'মৎস্য পালন', value: 'fish'))
            ->addOption(new McqOption(label: 'মুরগি পালন', value: 'chicken'));
            
        Field::create([
            'page_id' => $page->id,
            'slug' => 'fattening_animal',
            'label' => 'গরু মোটাতাজাকরণ',
            'type' => FieldType::DROPDOWN,
            'order' => 1,
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
        ]);

        // $options = [];
        // $options[] = new McqOption(label: 'গাভী পালন', value: 'cow');
        // $options[] = new McqOption(label: 'ছাগল পালন', value: 'goat');
        // $options[] = new McqOption(label: 'মৎস্য পালন', value: 'fish');
        // $options[] = new McqOption(label: 'মুরগি পালন', value: 'chicken');

        // $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 22))->setOptions($options);
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'গরু মোটাতাজাকরণ',
        //         'type' => FieldType::DROPDOWN,
        //         'order' => 19,
        //         'slug' => 'fattening_animal',
        //         'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //         'possible_values' => $radioField->getOptionsJson(),
        //         'rules' => (new Rule())->toJson(),
        //     ]
        // );

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'নিজস্ব পশু পালনের ঘর রয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 2,
            'slug' => 'has_cattle_shed',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(["shed_capacity"])
        ]);

        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'নিজস্ব পশু পালনের ঘরের পরিমাণ (শতাংশ)',
        //     'type' => FieldType::STRING,
        //     'order' => 2,
        //     'slug' => 'shed_capacity',
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
        //     'rules' => (new StringValidationRule(isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        // ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 3, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'পশুপালনের ঘরের মেঝে কি স্বাস্থ্যসম্মত?',
            'type' => FieldType::RADIO,
            'order' => 3,
            'slug' => 'has_clean_floor',
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'rules' => (new Rule(isRequired: false))->toJson(),
            'visibility_dependent_field' => json_encode(["floor_type", "has_proper_draining_system"])
        ]);

        $optionOne = new McqOption(label: 'কাঁচা', value: 'raw');
        $optionTwo = new McqOption(label: 'পাকা ', value: 'cement');
        $radioField = (new RadioField(id: 4, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'ঘরের মেঝে কি রকম?',
            'type' => FieldType::RADIO,
            'order' => 4,
            'slug' => 'floor_type',
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'rules' => (new Rule(isRequired: false))->toJson(),
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 5, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'পশুপালনের ঘরে কি সঠিকভাবে নিষ্কাশনের ব্যবস্থা রয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 5,
            'slug' => 'has_proper_draining_system',
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'rules' => (new Rule(isRequired: false))->toJson(),
        ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'নিজস্ব পশুখাদ্য চাষের জমি আছে?',
            'type' => FieldType::RADIO,
            'order' => 6,
            'slug' => 'has_grass_land',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(["grassland_area", "has_proper_air_system"])
        ]);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'জমির পরিমাণ (শতাংশ)',
            'type' => FieldType::STRING,
            'order' => 7,
            'slug' => 'grassland_area',
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 8, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'পশুপালনের ঘরে পর্যাপ্ত আলো বাতাসের ব্যবস্থা রয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 8,
            'slug' => 'has_proper_air_system',
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
            'rules' => (new Rule(isRequired: false))->toJson(),
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 6, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);

        Field::create([
            'page_id' => $page->id,
            'label' => 'পশু খাদ্য মজুদের ব্যবস্থা রয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 9,
            'slug' => 'has_food_storage_system',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 7, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'সঠিক পশুসম্পদ ব্যবস্থাপনা আছে?',
            'type' => FieldType::RADIO,
            'order' => 10,
            'slug' => 'has_livestock_management_system',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 9, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'গবাদি পশু দেখানোর জন্য নির্দিষ্ট পশু ডাক্তার আছে কি?',
            'type' => FieldType::RADIO,
            'order' => 11,
            'slug' => 'has_enlisted_veteran',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 10, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'পর্যাপ্ত টিকা সংক্রান্ত জ্ঞান আছে?',
            'type' => FieldType::RADIO,
            'order' => 12,
            'slug' => 'has_vaccination_knowledge',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 11, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'গবাদি পশু পালনের প্রশিক্ষণ আছে? (ইংরেজি)',
            'type' => FieldType::RADIO,
            'order' => 13,
            'slug' => 'has_cattle_rearing_training',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        ]);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'প্রতিষ্ঠানের নাম (ইংরেজি)',
            'type' => FieldType::STRING,
            'order' => 14,
            'slug' => 'training_institution_name',
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        ]);

        // $options = [];
        // $options[] = new McqOption(label: '১ - ৯  সপ্তাহ', value: '1_9');
        // $options[] = new McqOption(label: '১০  - ১৮ সপ্তাহ', value: '10_18');
        // $options[] = new McqOption(label: '১৯  - ২৭ সপ্তাহ', value: '19_27');
        // $options[] = new McqOption(label: '২৮  - ৩৬ সপ্তাহ', value: '28_36');
        // $options[] = new McqOption(label: '৩৭ - ৪৫ সপ্তাহ', value: '37_45');
        // $options[] = new McqOption(label: '৪৬ - ৫২ সপ্তাহ', value: '46_52');
        // $radioField = (new RadioField(id: 11, type: FieldType::DROPDOWN, label: '', slug: '', order: 22))->setOptions($options);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'প্রশিক্ষণের মেয়াদ',
        //     'type' => FieldType::DROPDOWN,
        //     'order' => 15,
        //     'slug' => 'training_duration',
        //     'possible_values' => $radioField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        //     'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        // ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 11, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'গবাদি পশুর কি খাদ্য মজুদ আছে? (ইংরেজি)',
            'type' => FieldType::RADIO,
            'order' => 16,
            'slug' => 'is_cattle_food_stored',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(["cattle_count", "estimated_month"])
        ]);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: 'yes', page: $page->slug);
        $options = [];
        $options[] = new McqOption(label: '1', value: '1');
        $options[] = new McqOption(label: '2', value: '2');
        $options[] = new McqOption(label: '3', value: '3');
        $options[] = new McqOption(label: '4', value: '4');
        $options[] = new McqOption(label: '5', value: '5');
        $options[] = new McqOption(label: '6', value: '6');
        $options[] = new McqOption(label: '7', value: '7');
        $options[] = new McqOption(label: '8', value: '8');
        $options[] = new McqOption(label: '9', value: '9');
        $options[] = new McqOption(label: '10', value: '10');

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 9))->setOptions($options);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'পশুর সংখ্যা (ইংরেজি)',
                'type' => FieldType::DROPDOWN,
                'order' => 17,
                'slug' => 'cattle_count',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );
        $options = [];
        $options[] = new McqOption(label: '১ মাস', value: '1');
        $options[] = new McqOption(label: '২ মাস', value: '2');
        $options[] = new McqOption(label: '৩ মাস', value: '3');
        $options[] = new McqOption(label: '৪ মাস', value: '4');
        $options[] = new McqOption(label: '৫ মাস', value: '5');
        $options[] = new McqOption(label: '৬ মাস', value: '6');
        $options[] = new McqOption(label: '৭ মাস', value: '7');
        $options[] = new McqOption(label: '৮ মাস', value: '8');
        $options[] = new McqOption(label: '৯ মাস', value: '9');
        $options[] = new McqOption(label: '১০ মাস', value: '10');
        $options[] = new McqOption(label: '১১ মাস', value: '11');
        $options[] = new McqOption(label: '১২ মাস', value: '12');

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 9))->setOptions($options);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'মজুদকৃত খাদ্য কত মাসের আছে?',
                'type' => FieldType::DROPDOWN,
                'order' => 18,
                'slug' => 'estimated_month',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $options = [];
        $options[] = new McqOption(label: '১ - ৫০ টি', value: '1_50');
        $options[] = new McqOption(label: '৫১ - ১০০ টি', value: '51_100');
        $options[] = new McqOption(label: '১০১ - ১৫০ টি', value: '101_150');
        $options[] = new McqOption(label: '১৫১ - ২০০ টি', value: '151_200');

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 9))->setOptions($options);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'গত ২ বছরে কতগুলো গবাদি পশু পালন করেছেন?',
                'type' => FieldType::DROPDOWN,
                'order' => 19,
                'slug' => 'total_cattle_in_last_two_years',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );
    }

    private function storeProductionToolsInformationSection($order)
    {
        $condition = new Condition(section: "farming_type", value: ["crop"]);

        $section = Section::create([
            'label' => 'উৎপাদন সরঞ্জাম তথ্য',
            'order' => $order,
            'slug' => 'production_tools_information',
            'type' => StepType::FORM_SECTION,
            'visible_if' => (new ConditionWrapper(backend: $condition))->toJson(),
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'উৎপাদন সরঞ্জাম তথ্য',
            'slug' => 'production_tools_information',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $condition = new Condition(section: "farming_type", value: ["cattle"]);
        Field::create([
            'page_id' => $page->id,
            'label' => 'পশু ওজন করার স্কেল আছে?',
            'type' => FieldType::RADIO,
            'order' => 1,
            'slug' => 'weight_scale',
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(backend: $condition))->toJson(),
            'rules' => (new Rule())->toJson(),
        ]);
        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'ঘাস/খড় কাটার মেশিন আছে?',
            'type' => FieldType::RADIO,
            'order' => 2,
            'slug' => 'cutting_machine',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);

        $optionOne = new McqOption(label: 'আছে', value: 'yes');
        $optionTwo = new McqOption(label: 'নেই', value: 'no');
        $radioField = (new RadioField(id: 3, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'অন্যান্য কোন উৎপাদন সরঞ্জাম আছে?',
            'type' => FieldType::RADIO,
            'order' => 3,
            'slug' => 'has_other_production_tools',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(["what_other_tools"])
        ]);
        $condition = new Condition(section: $section->slug, value: "yes", field: $field->slug, page: $page->slug);
        $options = [];
        $options[] = new McqOption(label: 'কম্বাইন্ড হারভেস্টর', value: 'combaind_harvest');
        $options[] = new McqOption(label: 'ঘাস/খড় কাটার মেশিন', value: 'grass_cutter');
        $options[] = new McqOption(label: 'জমিতে ঘাস, ধান কাটার মেশিন', value: 'field_paddy_cutter');
        $options[] = new McqOption(label: 'দুধের ননি পৃথকিকরণ মেশিন', value: 'milk_cream_machine');
        $options[] = new McqOption(label: 'গরু মহিষের স্বয়ংক্রিয় গা চুলকানোর মেশিন', value: 'cow_itching_machine');
        $options[] = new McqOption(label: 'গো খাদ্য মিশ্রণ মেশিন (টি এম আর)', value: 'tmr');
        $options[] = new McqOption(label: 'সাইলেজ বেলার মেশিন', value: 'bellar_machine');
        $options[] = new McqOption(label: 'পাওয়ার ট্রিলার ', value: 'power_triller');
        $options[] = new McqOption(label: 'জমি চাষ/মালামাল পরিহন ট্র‍্যাক্টর ', value: 'tructor');
        $options[] = new McqOption(label: 'ভূট্টা থ্রেসার মেশিন ', value: 'corn_thrasher');
        $options[] = new McqOption(label: 'ফিড গ্রাইন্ডার মেশিন ', value: 'feed_grinder');
        $options[] = new McqOption(label: 'ফিড পিলেট মেশিন', value: 'feed_pillet_machine');
        $options[] = new McqOption(label: 'গরুর রাবার ম্যাট', value: 'cow_rubber_matt');
        $options[] = new McqOption(label: 'ছাগল/ভেড়ার ম্যাট', value: 'goat_matt');
        $options[] = new McqOption(label: 'ওজন মাপার স্কেল', value: 'weight_scale');
        $options[] = new McqOption(label: 'ওজন মাপার ফিতা', value: 'weight_tape');
        $options[] = new McqOption(label: 'ফিডার বোতল ও নিপল', value: 'feeder_bottle_nipple');
        $options[] = new McqOption(label: 'ফিডার বাকেট', value: 'feeder_bucket');
        $options[] = new McqOption(label: 'টিট ডিপ কাপ', value: 'tit_deep_cup');
        $options[] = new McqOption(label: 'স্প্রে মেশিন', value: 'sepro_machine');
        $options[] = new McqOption(label: 'গরুর গলার বেল্ট', value: 'cow_coller_band');
        $options[] = new McqOption(label: 'মিল্ক ক্যান', value: 'milk_can');
        $options[] = new McqOption(label: 'বহনযোগ্য গাভীর দুধ দোহন মেশিন', value: 'portable_milking_machine');
        $options[] = new McqOption(label: 'ট্রলি', value: 'trolly');
        $checkboxField = (new CheckBoxField(id: 1, label: '', slug: '', order: 14))->setOptions($options);
        Field::create([
            'page_id' => $page->id,
            'label' => 'অন্যান্য কি উৎপাদন সরঞ্জাম আছে?',
            'type' => FieldType::CHECKBOX,
            'order' => 4,
            'slug' => 'what_other_tools',
            'rules' => (new Rule())->toJson(),
            'possible_values' => $checkboxField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        ]);
    }

    private function storeStackHolderInformationSection($order)
    {
        $farmingTypeCondition = new Condition(section: "farming_type", value: ["crop"]);
        $section = Section::create([
            'label' => 'স্টেকহোল্ডার তথ্য',
            'order' => $order,
            'slug' => 'stack_holder_information',
            'type' => StepType::FORM_SECTION,
        ]);

        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'স্টেকহোল্ডার তথ্য',
            'type' => StepType::FORM_SECTION,
            'order' => 1,
            'slug' => 'stack_holder_information',
        ]);
        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => 'cattle_food',
            'label' => 'পশু খাদ্য/সার/বীজ ডিলার',
            'type' => FieldType::GROUP,
            'order' => 1,
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField->id,
                'slug' => 'cattle_food_distributor_name',
                'label' => 'নাম',
                'type' => FieldType::STRING,
                'order' => 2,
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
            ]
        );
        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField->id,
                'slug' => 'cattle_food_distributor_mobile',
                'label' => 'ফোন নাম্বার',
                'type' => FieldType::MOBILE,
                'order' => 3,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
            ]
        );
        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField->id,
                'slug' => 'cattle_food_remarks',
                'label' => 'মন্তব্য',
                'type' => FieldType::STRING,
                'order' => 4,
                'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
            ]
        );
        $groupField1 = Field::create([
            'page_id' => $page->id,
            'slug' => 'veteran',
            'label' => 'পশু চিকিৎসক/কৃষিবীদ',
            'type' => FieldType::GROUP,
            'order' => 5,
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField1->id,
                'slug' => 'veteran_name',
                'label' => 'নাম',
                'type' => FieldType::STRING,
                'order' => 6,
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
            ]
        );
        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField1->id,
                'slug' => 'veteran_mobile',
                'label' => 'ফোন নাম্বার',
                'type' => FieldType::MOBILE,
                'order' => 7,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField1->id,
                'slug' => 'veteran_remarks',
                'label' => 'মন্তব্য',
                'type' => FieldType::STRING,
                'order' => 8,
                'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
            ]
        );

        $groupField2 = Field::create([
            'page_id' => $page->id,
            'slug' => 'selling_market',
            'label' => 'পশুর বিক্রয়ের বাজার',
            'type' => FieldType::GROUP,
            'order' => 9,
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField2->id,
                'slug' => 'market_name',
                'label' => 'নাম',
                'type' => FieldType::STRING,
                'order' => 10,
                'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
            ]
        );
        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField2->id,
                'slug' => 'market_mobile',
                'label' => 'ফোন নাম্বার',
                'type' => FieldType::MOBILE,
                'order' => 11,
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'group_id' => $groupField2->id,
                'slug' => 'market_remarks',
                'label' => 'মন্তব্য',
                'type' => FieldType::STRING,
                'order' => 12,
                'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
            ]
        );

        // $groupField3 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'up_chairman',
        //     'label' => 'ইউপি চেয়ারম্যান',
        //     'type' => FieldType::GROUP,
        //     'order' => 13,
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField3->id,
        //         'slug' => 'up_chairman_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 14,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField3->id,
        //         'slug' => 'up_chairman_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 15,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField3->id,
        //         'slug' => 'up_chairman_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 16,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );

        // $groupField4 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'local_member',
        //     'label' => 'স্থানীয় মাতবর',
        //     'type' => FieldType::GROUP,
        //     'order' => 17,
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField4->id,
        //         'slug' => 'local_member_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 18,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField4->id,
        //         'slug' => 'local_member_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 19,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField4->id,
        //         'slug' => 'local_member_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 20,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );

        // $groupField5 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'partner',
        //     'label' => 'পূর্বের/বর্তমান অংশীদার',
        //     'type' => FieldType::GROUP,
        //     'order' => 21,
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField5->id,
        //         'slug' => 'partner_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 22,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField5->id,
        //         'slug' => 'partner_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 23,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField5->id,
        //         'slug' => 'partner_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 24,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );

        // $groupField6 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'neighbor_1',
        //     'label' => 'প্রতিবেশী ১',
        //     'type' => FieldType::GROUP,
        //     'order' => 25,
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField6->id,
        //         'slug' => 'neighbor_1_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 26,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField6->id,
        //         'slug' => 'neighbor_1_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 27,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField6->id,
        //         'slug' => 'neighbor_1_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 28,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );
        // $groupField7 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'neighbor_2',
        //     'label' => 'প্রতিবেশী ২',
        //     'type' => FieldType::GROUP,
        //     'order' => 29,
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField7->id,
        //         'slug' => 'neighbor_2_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 30,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField7->id,
        //         'slug' => 'neighbor_2_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 31,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField7->id,
        //         'slug' => 'neighbor_2_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 32,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );
        // $groupField8 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'neighbor_3',
        //     'label' => 'প্রতিবেশী ৩',
        //     'type' => FieldType::GROUP,
        //     'order' => 33,
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField8->id,
        //         'slug' => 'neighbor_3_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 34,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField8->id,
        //         'slug' => 'neighbor_3_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 35,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField8->id,
        //         'slug' => 'neighbor_3_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 36,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );

        // $groupField9 = Field::create([
        //     'page_id' => $page->id,
        //     'slug' => 'dealer',
        //     'label' => 'সার/বীজ ডিলার',
        //     'type' => FieldType::GROUP,
        //     'order' => 37,
        //     'visible_if' => (new ConditionWrapper(backend: $farmingTypeCondition))->toJson()
        // ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField9->id,
        //         'slug' => 'dealer_name',
        //         'label' => 'নাম',
        //         'type' => FieldType::STRING,
        //         'order' => 38,
        //         'rules' => (new StringValidationRule(isNumberSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );
        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField9->id,
        //         'slug' => 'dealer_mobile',
        //         'label' => 'ফোন নাম্বার',
        //         'type' => FieldType::MOBILE,
        //         'order' => 39,
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, isRequired: false))->toJson(),
        //     ]
        // );

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'group_id' => $groupField9->id,
        //         'slug' => 'dealer_remarks',
        //         'label' => 'মন্তব্য',
        //         'type' => FieldType::STRING,
        //         'order' => 40,
        //         'rules' => (new StringValidationRule(isNumberSupported: true, isSpecialCharacterSupported: true, isRequired: false))->toJson(),
        //     ]
        // );
    }

    private function storePreviousExperienceSection($order)
    {
        $conditionCrop = new Condition(section: "farming_type", value: ["crop"]);
        $conditionCattle = new Condition(section: "farming_type", value: ["cattle"]);

        $section = Section::create([
            'label' => 'পূর্ব অভিজ্ঞতার তথ্য',
            'order' => $order,
            'slug' => 'previous_experience',
            'type' => StepType::FORM_SECTION,
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'পূর্ব অভিজ্ঞতার তথ্য',
            'type' => StepType::FORM_SECTION,
            'order' => 1,
            'slug' => 'previous_experience',
        ]);
        $groupField = Field::create([
            'page_id' => $page->id,
            'slug' => "previous_experience",
            'label' => 'পূর্ব অভিজ্ঞতার তথ্য',
            'type' => FieldType::GROUP,
            'order' => 1,
            'is_repeatable' => 1,
            'repeatable_text' => "আরও যোগ করুন",
            'repeatable_class' => ExperienceRepeater::class,
        ]);
        Field::create([
            'page_id' => $page->id,
            'group_id' => $groupField->id,
            'label' => 'হাটের নাম (ইংরেজি)',
            'type' => FieldType::STRING,
            'order' => 2,
            'slug' => 'selling_market_name',
            'rules' => (new StringValidationRule(isSpecialCharacterSupported: false))->toJson(),
        ]);
        Field::create([
            'page_id' => $page->id,
            'group_id' => $groupField->id,
            'label' => 'পশুর সংখ্যা (টি)(ইংরেজি সংখ্যায় পূরণ করুন)',
            'type' => FieldType::STRING,
            'order' => 3,
            'slug' => 'selling_cattle_count',
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
            'visible_if' => (new ConditionWrapper(backend: $conditionCattle))->toJson(),
        ]);

        // Field::create([
        //     'page_id' => $page->id,
        //     'group_id' => $groupField->id,
        //     'label' => 'ফলনের পরিমাণ (মণ)',
        //     'type' => FieldType::STRING,
        //     'order' => 4,
        //     'slug' => 'crop_production_amount',
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        //     'visible_if' => (new ConditionWrapper(backend: $conditionCrop))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'group_id' => $groupField->id,
        //     'label' => 'ফসলের পরিমাণ (মণ)',
        //     'type' => FieldType::STRING,
        //     'order' => 5,
        //     'slug' => 'selling_crop_count',
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        //     'visible_if' => (new ConditionWrapper(backend: $conditionCrop))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'group_id' => $groupField->id,
        //     'label' => 'ক্রয়মূল্য',
        //     'type' => FieldType::STRING,
        //     'order' => 6,
        //     'slug' => 'buying_price',
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'group_id' => $groupField->id,
        //     'label' => 'বিক্রয়মূল্য',
        //     'type' => FieldType::STRING,
        //     'order' => 7,
        //     'slug' => 'selling_price',
        //     'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false))->toJson(),
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'group_id' => $groupField->id,
        //     'label' => 'ক্রয় করার সময়',
        //     'type' => FieldType::DATE,
        //     'order' => 8,
        //     'slug' => 'buying_time',
        //     'rules' => (new DateValidationRule())->toJson()
        // ]);
        // Field::create([
        //     'page_id' => $page->id,
        //     'group_id' => $groupField->id,
        //     'label' => 'বিক্রয় করার সময়',
        //     'type' => FieldType::DATE,
        //     'order' => 9,
        //     'slug' => 'selling_time',
        //     'rules' => (new DateValidationRule())->toJson()
        // ]);
    }

    private function storeDisasterManagementSection($order)
    {
        $section = Section::create([
            'label' => 'দুর্যোগ ব্যবস্থাপনা তথ্য',
            'order' => $order,
            'slug' => 'disaster_management',
            'type' => StepType::FORM_SECTION,
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'দুর্যোগ ব্যবস্থাপনা তথ্য',
            'slug' => 'disaster_management',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
        ]);

        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);

        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'বন্যা প্রবণ এলাকা?',
            'type' => FieldType::RADIO,
            'order' => 1,
            'slug' => 'is_flood_affected_area',
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
            'visibility_dependent_field' => json_encode(["faced_flood_last_3_years"])
        ]);

        $condition = new Condition(section: $section->slug, field: $field->slug, value: "yes", page: $page->slug);
        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'বিগত ৩ বছরে বন্যা হয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 2,
            'slug' => 'faced_flood_last_3_years',
            'rules' => (new Rule(isRequired: false))->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        ]);
        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'ঘূর্ণি ঝড় প্রবণ এলাকা?',
            'type' => FieldType::RADIO,
            'order' => 3,
            'slug' => 'is_cyclone_affected_area',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(["faced_cyclone_last_3_years"])
        ]);
        $condition = new Condition(section: $section->slug, field: $field->slug, value: "yes", page: $page->slug);
        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'বিগত ৩ বছরে ঘূর্ণি ঝড় হয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 4,
            'slug' => 'faced_cyclone_last_3_years',
            'rules' => (new Rule(isRequired: false))->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        ]);
        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'শুষ্ক প্রবণ এলাকা?',
            'type' => FieldType::RADIO,
            'order' => 5,
            'slug' => 'is_dry_area',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
            'visibility_dependent_field' => json_encode(["faced_dryness_last_3_years"])
        ]);

        $condition = new Condition(section: $section->slug, field: $field->slug, value: "yes", page: $page->slug);

        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 2, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);
        Field::create([
            'page_id' => $page->id,
            'label' => 'বিগত ৩ বছরে খরা/মঙ্গা হয়েছে?',
            'type' => FieldType::RADIO,
            'order' => 6,
            'slug' => 'faced_dryness_last_3_years',
            'rules' => (new Rule(isRequired: false))->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson()
        ]);
    }
    
    private function storeSuccessSection($order)
    {
        Section::create([
            'label' => 'আপনার আবেদন সফল হয়েছে',
            'order' => $order,
            'slug' => 'success',
            'type' => StepType::SUCCESS,
            'description' => 'আপনার অ্যাকাউন্ট পেমেন্ট গ্রহণের জন্যে তৈরি। ভেরিফিকেশনের পরে আপনার অ্যাকাউন্ট উত্তোলন ও অন্যান্য লেনদেনের জন্য খুলে দেয়া হবে। অনুগ্রহ করে ৩ কার্যদিবস অপেক্ষা করুন।',
        ]);
    }

    private function storeSignatureSection($order)
    {
        $section = Section::create([
            'label' => 'সাক্ষরের ছবি দিন',
            'order' => $order,
            'slug' => 'signature',
            'type' => StepType::SIGNATURE,
        ]);

        Page::create([
            'section_id' => $section->id,
            'label' => 'সাক্ষরের ছবি দিন',
            'type' => StepType::INFORMATION,
            'order' => 1,
            'slug' => 'signature_instructions',
            'description' => '<div style="color: #464646;"><div style="text-align: center;"><img src="/IMG_8293.png" alt=""></div><h4 style="font-size: 1rem;">সিগনেচারের ছবি তুলুন</h4><p style="font-size: 1.875rem;">লক্ষ্য রাখুন যাতে-</p><p style="font-size: 1.875rem;">১। সাদা কাগজের উপর সাক্ষর দিতে হবে।<br>২। কাল কালিতে সাক্ষর দিতে হবে।<br>৩। সাক্ষরের স্পষ্ট ছবি তুলতে হবে।<br></p></div>',
        ]);
        Page::create([
            'section_id' => $section->id,
            'label' => '',
            'type' => StepType::SIGNATURE,
            'order' => 2,
            'slug' => 'signature_image',
        ]);
    }

    private function storeAssessmentInfoSection($order)
    {
        $section = Section::create([
            'label' => 'কৃষকের ধরণ',
            'order' => $order,
            'slug' => 'assessment_info',
            'type' => StepType::FORM_SECTION,
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'কৃষকের ধরণ',
            'slug' => 'assessment_info',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
        ]);

        $optionOne = new McqOption(label: 'নতুন', value: 'New');
        $optionTwo = new McqOption(label: 'পুরাতন', value: 'Old');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 22))->addOption($optionOne)->addOption($optionTwo);

        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'কৃষকের ধরণ',
            'type' => FieldType::RADIO,
            'order' => 1,
            'slug' => 'assessment_info',
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
        ]);

        $optionOne = new McqOption(label: 'হ্যাঁ', value: 'yes');
        $optionTwo = new McqOption(label: 'না', value: 'no');
        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 24))->addOption($optionOne)->addOption($optionTwo);

        $field = Field::create([
            'page_id' => $page->id,
            'label' => 'কৃষকের ব্যাংক অ্যাকাউন্ট আছে কি?',
            'type' => FieldType::RADIO,
            'order' => 2,
            'slug' => 'farmer_has_bank',
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
        ]);

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))
            ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
            ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'WeGro থেকে লোন নেয়ার অভিজ্ঞতা ছিল?',
                'type' => FieldType::DROPDOWN,
                'order' => 3,
                'slug' => 'farmer_loan_wegro',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'কতবার লোন গ্রহণ করেছে?',
            'type' => FieldType::STRING,
            'order' => 4,
            'slug' => 'times_loan_taken',
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(), 
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [1, 2]), slug: 'times_loan_taken'))->toJson(),
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'শেষবার কত টাকা লোন গ্রহণ করেছে?',
                'type' => FieldType::STRING,
                'order' => 5,
                'slug' => 'amount_loan_taken',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(), 
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [1, 5, 6, 7]),slug: 'amount_loan_taken'))->toJson()
            ]
        );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))
            ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
            ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'ব্যাংক থেকে লোন নেওয়া আছে ?',
                'type' => FieldType::DROPDOWN,
                'order' => 6,
                'slug' => 'present_loan_farmer',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'সকল ব্যাংক থেকে লোনের পরিমাণ?',
                'type' => FieldType::STRING,
                'order' => 7,
                'slug' => 'current_loan_amount',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(), 
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [1, 4, 5, 6, 7]),slug: 'current_loan_amount'))->toJson()
            ]
        );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))
            ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
            ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'MFI লোন নেওয়া আছে ?',
                'type' => FieldType::DROPDOWN,
                'order' => 8,
                'slug' => 'present_loan_mfi',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'সকল MFI লোনের পরিমাণ?',
                'type' => FieldType::STRING,
                'order' => 9,
                'slug' => 'mfi_amount_loan',
                'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(), 
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [1, 4, 5, 6, 7]),slug: 'mfi_amount_loan'))->toJson()
            ]
        );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))
        ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের গরু/গাভী আছে?',
                'type' => FieldType::DROPDOWN,
                'order' => 10,
                'slug' => 'has_cows',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        $dropDownField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))
        ->addOption(new McqOption(label: '১ টি', value: '1'))
        ->addOption(new McqOption(label: '২ টি', value: '2'))
        ->addOption(new McqOption(label: '৩ টি', value: '3'))
        ->addOption(new McqOption(label: '৪ টি', value: '4'))
        ->addOption(new McqOption(label: '৫ টি', value: '5'))
        ->addOption(new McqOption(label: '৬ টি', value: '6'))
        ->addOption(new McqOption(label: '৭ টি', value: '7'))
        ->addOption(new McqOption(label: '৮ টি', value: '8'))
        ->addOption(new McqOption(label: '৯ টি', value: '9'))
        ->addOption(new McqOption(label: '১০ টি', value: '10'))
        ->addOption(new McqOption(label: '১১ টি', value: '11'))
        ->addOption(new McqOption(label: '১২ টি', value: '12'))
        ->addOption(new McqOption(label: '১৩ টি', value: '13'))
        ->addOption(new McqOption(label: '১৪ টি', value: '14'))
        ->addOption(new McqOption(label: '১৫ টি', value: '15'))
        ->addOption(new McqOption(label: '১৬ টি', value: '16'))
        ->addOption(new McqOption(label: '১৭ টি', value: '17'))
        ->addOption(new McqOption(label: '১৮ টি', value: '18'))
        ->addOption(new McqOption(label: '১৯ টি', value: '19'))
        ->addOption(new McqOption(label: '২০ টি', value: '20'))
        ->addOption(new McqOption(label: '২০ টি', value: '20'))
        ->addOption(new McqOption(label: '২১ টি', value: '21'))
        ->addOption(new McqOption(label: '২২ টি', value: '22'))
        ->addOption(new McqOption(label: '২৩ টি', value: '23'))
        ->addOption(new McqOption(label: '২৪ টি', value: '24'))
        ->addOption(new McqOption(label: '২৫ টি', value: '25'))
        ->addOption(new McqOption(label: '২৬ টি', value: '26'))
        ->addOption(new McqOption(label: '২৭ টি', value: '27'))
        ->addOption(new McqOption(label: '২৮ টি', value: '28'))
        ->addOption(new McqOption(label: '২৯ টি', value: '29'))
        ->addOption(new McqOption(label: '৩০ টি', value: '30'))
        ->addOption(new McqOption(label: '৩০ টি', value: '30'))
        ->addOption(new McqOption(label: '৩১ টি', value: '31'))
        ->addOption(new McqOption(label: '৩২ টি', value: '32'))
        ->addOption(new McqOption(label: '৩৩ টি', value: '33'))
        ->addOption(new McqOption(label: '৩৪ টি', value: '34'))
        ->addOption(new McqOption(label: '৩৫ টি', value: '35'))
        ->addOption(new McqOption(label: '৩৬ টি', value: '36'))
        ->addOption(new McqOption(label: '৩৭ টি', value: '37'))
        ->addOption(new McqOption(label: '৩৮ টি', value: '38'))
        ->addOption(new McqOption(label: '৩৯ টি', value: '39'))
        ->addOption(new McqOption(label: '৪০ টি', value: '40'))
        ->addOption(new McqOption(label: '৪১ টি', value: '41'))
        ->addOption(new McqOption(label: '৪২ টি', value: '42'))
        ->addOption(new McqOption(label: '৪৩ টি', value: '43'))
        ->addOption(new McqOption(label: '৪৪ টি', value: '44'))
        ->addOption(new McqOption(label: '৪৫ টি', value: '45'))
        ->addOption(new McqOption(label: '৪৬ টি', value: '46'))
        ->addOption(new McqOption(label: '৪৭ টি', value: '47'))
        ->addOption(new McqOption(label: '৪৮ টি', value: '48'))
        ->addOption(new McqOption(label: '৪৯ টি', value: '49'))
        ->addOption(new McqOption(label: '৫০ টি', value: '50'));
        Field::create([
            'page_id' => $page->id,
            'label' => 'বর্তমানে কৃষকের কয়টি গরু/গাভী আছে?',
            'type' => FieldType::DROPDOWN,
            'order' => 11,
            'slug' => 'count_cows',
            'possible_values' => $dropDownField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(), 
            'rules' => (new Rule())->toJson(),
        ]);

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 12))
        ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের ছাগল আছে?',
                'type' => FieldType::DROPDOWN,
                'order' => 12,
                'slug' => 'has_goats',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );

        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        $dropDownField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 24))
        ->addOption(new McqOption(label: '১ টি', value: '1'))
        ->addOption(new McqOption(label: '২ টি', value: '2'))
        ->addOption(new McqOption(label: '৩ টি', value: '3'))
        ->addOption(new McqOption(label: '৪ টি', value: '4'))
        ->addOption(new McqOption(label: '৫ টি', value: '5'))
        ->addOption(new McqOption(label: '৬ টি', value: '6'))
        ->addOption(new McqOption(label: '৭ টি', value: '7'))
        ->addOption(new McqOption(label: '৮ টি', value: '8'))
        ->addOption(new McqOption(label: '৯ টি', value: '9'))
        ->addOption(new McqOption(label: '১০ টি', value: '10'))
        ->addOption(new McqOption(label: '১১ টি', value: '11'))
        ->addOption(new McqOption(label: '১২ টি', value: '12'))
        ->addOption(new McqOption(label: '১৩ টি', value: '13'))
        ->addOption(new McqOption(label: '১৪ টি', value: '14'))
        ->addOption(new McqOption(label: '১৫ টি', value: '15'))
        ->addOption(new McqOption(label: '১৬ টি', value: '16'))
        ->addOption(new McqOption(label: '১৭ টি', value: '17'))
        ->addOption(new McqOption(label: '১৮ টি', value: '18'))
        ->addOption(new McqOption(label: '১৯ টি', value: '19'))
        ->addOption(new McqOption(label: '২০ টি', value: '20'))
        ->addOption(new McqOption(label: '২০ টি', value: '20'))
        ->addOption(new McqOption(label: '২১ টি', value: '21'))
        ->addOption(new McqOption(label: '২২ টি', value: '22'))
        ->addOption(new McqOption(label: '২৩ টি', value: '23'))
        ->addOption(new McqOption(label: '২৪ টি', value: '24'))
        ->addOption(new McqOption(label: '২৫ টি', value: '25'))
        ->addOption(new McqOption(label: '২৬ টি', value: '26'))
        ->addOption(new McqOption(label: '২৭ টি', value: '27'))
        ->addOption(new McqOption(label: '২৮ টি', value: '28'))
        ->addOption(new McqOption(label: '২৯ টি', value: '29'))
        ->addOption(new McqOption(label: '৩০ টি', value: '30'))
        ->addOption(new McqOption(label: '৩০ টি', value: '30'))
        ->addOption(new McqOption(label: '৩১ টি', value: '31'))
        ->addOption(new McqOption(label: '৩২ টি', value: '32'))
        ->addOption(new McqOption(label: '৩৩ টি', value: '33'))
        ->addOption(new McqOption(label: '৩৪ টি', value: '34'))
        ->addOption(new McqOption(label: '৩৫ টি', value: '35'))
        ->addOption(new McqOption(label: '৩৬ টি', value: '36'))
        ->addOption(new McqOption(label: '৩৭ টি', value: '37'))
        ->addOption(new McqOption(label: '৩৮ টি', value: '38'))
        ->addOption(new McqOption(label: '৩৯ টি', value: '39'))
        ->addOption(new McqOption(label: '৪০ টি', value: '40'))
        ->addOption(new McqOption(label: '৪১ টি', value: '41'))
        ->addOption(new McqOption(label: '৪২ টি', value: '42'))
        ->addOption(new McqOption(label: '৪৩ টি', value: '43'))
        ->addOption(new McqOption(label: '৪৪ টি', value: '44'))
        ->addOption(new McqOption(label: '৪৫ টি', value: '45'))
        ->addOption(new McqOption(label: '৪৬ টি', value: '46'))
        ->addOption(new McqOption(label: '৪৭ টি', value: '47'))
        ->addOption(new McqOption(label: '৪৮ টি', value: '48'))
        ->addOption(new McqOption(label: '৪৯ টি', value: '49'))
        ->addOption(new McqOption(label: '৫০ টি', value: '50'));
        Field::create([
            'page_id' => $page->id,
            'label' => 'বর্তমানে কৃষকের কয়টি ছাগল আছে?',
            'type' => FieldType::DROPDOWN,
            'order' => 13,
            'slug' => 'count_goats',
            'possible_values' => $dropDownField->getOptionsJson(),
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(), 
            'rules' => (new Rule())->toJson(),
        ]);

        // Field::create(
        //     [
        //         'page_id' => $page->id,
        //         'label' => 'বর্তমানে কৃষকের কয়টি ছাগল আছে?',
        //         'type' => FieldType::STRING,
        //         'order' => 10,
        //         'slug' => 'count_goats',
        //         'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false,length: new NumberRange(lengths: [1, 2, 3]),slug: 'count_goats'))->toJson()
        //     ]
        // );

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 12))
        ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের কৃষি জমি আছে?',
                'type' => FieldType::DROPDOWN,
                'order' => 14,
                'slug' => 'type_farming_jomi',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );
        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'নিজের কৃষিজমির পরিমান (শতক)',
            'type' => FieldType::STRING,
            'order' => 15,
            'slug' => 'cultivated_own_land',
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),  
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [1, 2, 3, 4, 5]), slug: 'cultivated_own_land'))->toJson()
        ]);
        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 12))
        ->addOption(new McqOption(label: 'হ্যাঁ', value: 'yes'))
        ->addOption(new McqOption(label: 'না', value: 'no'));
        $field = Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের বর্গা জমি আছে?',
                'type' => FieldType::DROPDOWN,
                'order' => 16,
                'slug' => 'type_borga_jomi',
                'possible_values' => $radioField->getOptionsJson(),
                'rules' => (new Rule())->toJson(),
            ]
        );
        $condition = new Condition(section: $section->slug, value: 'yes', field: $field->slug, page: $page->slug);
        Field::create([
            'page_id' => $page->id,
            'label' => 'বর্গা এবং বন্ধক কৃষি জমির পরিমান (শতক)',
            'type' => FieldType::STRING,
            'order' => 17,
            'slug' => 'cultivated_rented_land',
            'visible_if' => (new ConditionWrapper(frontend: $condition))->toJson(),  
            'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false, length: new NumberRange(lengths: [1, 2, 3, 4, 5]), slug: 'cultivated_rented_land'))->toJson()
        ]);

    }

    private function storeProjectLoanDetails($order)
    {
        $section = Section::create([
            'label' => 'প্রোজেক্ট এর ধরণ',
            'order' => $order,
            'slug' => 'project_loan_details',
            'type' => StepType::FORM_SECTION,
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'প্রোজেক্ট এর ধরণ',
            'slug' => 'project_loan_details',
            'order' => 1,
            'type' => StepType::FORM_SECTION,
        ]);

        // $options = [
        //     new McqOption(label: 'আমন ধান ', value: 'Amon Paddy'),
        //     new McqOption(label: 'সবজি চাষ ', value: 'Mixed Vegetable'),
        //     new McqOption(label: 'গরু মোটাতাজাকরণ', value: 'Cattle Fattening'),
        //     new McqOption(label: 'ছাগল ', value: 'Goat'),
        //     new McqOption(label: 'মাছ', value: 'wheat'),
        //     new McqOption(label: 'ইরি ধান', value: 'Iri Paddy'),
        //     new McqOption(label: 'ভূট্টা', value: 'Maize Harvest'),
        //     new McqOption(label: 'আম', value: 'Mango Harvest'),
        //     new McqOption(label: 'আলু', value: 'Potato'),
        //     new McqOption(label: 'পোস্ট হারভেস্ট', value: 'Post Harvesting'),
        //     new McqOption(label: 'তরমুজ', value: 'Watermelon'),
        //     new McqOption(label: 'পেঁয়াজ', value: 'Onion'),
        // ];
        // $checkboxField = (new CheckBoxField(id: 1, label: '', slug: '', order: 10))->setOptions($options);
        // Field::create([
        //     'page_id' => $page->id,
        //     'label' => 'প্রোজেক্ট এর ধরণ',
        //     'type' => FieldType::CHECKBOX,
        //     'order' => 10,
        //     'slug' => 'crop_selection',
        //     'possible_values' => $checkboxField->getOptionsJson(),
        //     'rules' => (new Rule())->toJson(),
        // ]);

        $radioField = (new RadioField(id: 1, type: FieldType::RADIO, label: '', slug: '', order: 23)) 
            ->addOption(new McqOption(label: 'আমন ধান ', value: 'Amon Paddy'))
            ->addOption(new McqOption(label: 'সবজি চাষ', value: 'Mixed Vegetable'))
            ->addOption(new McqOption(label: 'গরু মোটাতাজাকরণ', value: 'Cattle Fattening'))
            ->addOption(new McqOption(label: 'ছাগল', value: 'Goat'))
            ->addOption(new McqOption(label: 'মাছ', value: 'wheat'))
            ->addOption(new McqOption(label: 'ইরি ধান', value: 'Iri Paddy'))
            ->addOption(new McqOption(label: 'ভূট্টা', value: 'Maize Harvest'))
            ->addOption(new McqOption(label: 'আম', value: 'Mango Harvest'))
            ->addOption(new McqOption(label: 'আলু', value: 'Potato'))
            ->addOption(new McqOption(label: 'পোস্ট হারভেস্ট', value: 'Post Harvesting'))
            ->addOption(new McqOption(label: 'তরমুজ', value: 'Watermelon'))
            ->addOption(new McqOption(label: 'পেঁয়াজ', value: 'Onion'));
        Field::create([
            'page_id' => $page->id,
            'label' => 'প্রোজেক্ট এর ধরণ (একটি নির্বাচন করুন)',
            'type' => FieldType::RADIO,
            'order' => 11,
            'slug' => 'crop_selection_single',
            'possible_values' => $radioField->getOptionsJson(),
            'rules' => (new Rule())->toJson(),
        ]);

        $radioField = (new RadioField(id: 1, type: FieldType::DROPDOWN, label: '', slug: '', order: 23))
            ->addOption(new McqOption(label: '2 months', value: '2 months'))
            ->addOption(new McqOption(label: '3 months', value: '3 months'))
            ->addOption(new McqOption(label: '4 months', value: '4 months'))
            ->addOption(new McqOption(label: '5 months', value: '5 months'))
            ->addOption(new McqOption(label: '6 months', value: '6 months'))
            ->addOption(new McqOption(label: '7 months', value: '7 months'))
            ->addOption(new McqOption(label: '8 months', value: '8 months'))
            ->addOption(new McqOption(label: '9 months', value: '9 months'))
            ->addOption(new McqOption(label: '10 months', value: '10 months'))
            ->addOption(new McqOption(label: '11 months', value: '11 months'))
            ->addOption(new McqOption(label: '12 months', value: '12 months'));
        Field::create([
            'page_id' => $page->id,
            'slug' => 'crop_time',
            'label' => 'প্রোজেক্ট এর মেয়াদ', 
            'type' => FieldType::DROPDOWN,
            'order' => 12,
            'rules' => (new Rule())->toJson(),
            'possible_values' => $radioField->getOptionsJson(),
        ]);

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'কৃষকের প্রয়োজনীয় লোনের পরিমান',
                'type' => FieldType::STRING,
                'order' => 13,
                'slug' => 'requested_loan_amount',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false,length: new NumberRange(lengths: [1, 5, 6, 7]),slug: 'requested_loan_amount'))->toJson()
            ]
        );

        Field::create(
            [
                'page_id' => $page->id,
                'label' => 'প্রস্তাবিত লোনের পরিমান (FO পূরণ করবে)',
                'type' => FieldType::STRING,
                'order' => 14,
                'slug' => 'requested_loan_amount_fieldOfficer',
                'rules' => (new StringValidationRule(isAlphaSupported: false, isSpecialCharacterSupported: false,length: new NumberRange(lengths: [1, 5, 6, 7]),slug: 'requested_loan_amount_fieldOfficer'))->toJson()
            ]
        );
    }

    private function storePreviewSection($order)
    {
        $section = Section::create([
            'label' => 'সকল তথ্য',
            'order' => $order,
            'slug' => 'review',
            'type' => StepType::REVIEW,
        ]);
        $page = Page::create([
            'section_id' => $section->id,
            'label' => 'সকল তথ্য',
            'slug' => 'review',
            'order' => 1,
            'type' => StepType::REVIEW,
        ]);
    }
}
