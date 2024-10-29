<?php

namespace App\Services\Application\Form\ValidationRules;

use App\Helpers\NumberRange;
use App\Services\Application\Form\ValidationRuleException;

class StringValidationRule extends Rule
{
    public function __construct(
        public bool $isAlphaSupported = true,
        public readonly ?string $isAlphaSupportedMessage = 'কোন অক্ষর ব্যবহার করা যাবে না',
        public readonly bool $isNumberSupported = true,
        public readonly ?string $isNumberSupportedMessage = 'কোন নাম্বার ব্যবহার করা যাবে না',
        public readonly bool $isSpecialCharacterSupported = true,
        public readonly ?string $isSpecialCharacterSupportedMessage = 'কোন বিশেষ চিহ্ন ব্যবহার করা যাবে না ',
        public readonly bool $containOnlyBanglaCharacters = false,
        public readonly ?string $containOnlyBanglaCharactersMessage = 'ক্ষেত্রটি শুধুমাত্র বাংলা অক্ষর সমর্থন করে',
        public readonly bool $containOnlyEnglishCharacters = false,
        public readonly ?string $containOnlyEnglishCharactersMessage = 'ক্ষেত্রটি শুধুমাত্র ইংরেজি অক্ষর সমর্থন করে',
        public readonly ?NumberRange $length = null,
        public ?string $lengthMessage = null,
        bool $isRequired = true,
        ?string $isRequiredMessage = 'তথ্য দেওয়া অত্যাবশ্যক',
        public ?string $slug = null
    ) {
        parent::__construct(isRequired: $isRequired, isRequiredMessage: $isRequiredMessage);

        $this->slug = $slug;
        $this->lengthMessage = $this->generateLengthMessage();
    }

    private function generateLengthMessage(): string
    {
        switch ($this->slug) {
            case 'nid_number':
                return 'NID নাম্বার ১০/১৩/১৭ এর ব্যাতিক্রম হতে পারবেনা';
            case 'guarantor_nid_number':
                return 'নমিনীর NID নাম্বার ১০/১৩/১৭ এর ব্যাতিক্রম হতে পারবেনা';
            case 'count_goats':
            case 'count_cows':
            case 'times_loan_taken':
                return 'একক/দশক নম্বর এর ব্যাতিক্রম হতে পারবেনা';
            case 'cultivated_own_land':
            case 'cultivated_rented_land':
                return 'পাঁচ সংখ্যার বেশি হতে পারবে না';
            case 'registered_post_code':
                return 'পোস্ট কোড চার সংখ্যার হতে হবে';
            case 'mfi_amount_loan':
            case 'current_loan_amount':
                return 'সংখ্যাটি হাজারের উপরে গ্রহণযোগ্য';
            case 'amount_loan_taken':
                return 'সংখ্যাটি অযুতের উপরে গ্রহণযোগ্য';
            case 'requested_loan_amount':
                return 'সংখ্যাটি অযুতের উপরে গ্রহণযোগ্য';
            case 'requested_loan_amount_fieldOfficer':
                return 'সংখ্যাটি অযুতের উপরে গ্রহণযোগ্য';
            default:
                return 'তথ্য সঠিক নয়';
        }
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data = [
            'is_alpha_supported' => ['value' => $this->isAlphaSupported, 'message' => $this->isAlphaSupportedMessage],
            'is_number_supported' => ['value' => $this->isNumberSupported, 'message' => $this->isNumberSupportedMessage],
            'is_special_character_supported' => ['value' => $this->isSpecialCharacterSupported, 'message' => $this->isSpecialCharacterSupportedMessage],
            'contain_only_bangla_characters' => ['value' => $this->containOnlyBanglaCharacters, 'message' => $this->containOnlyBanglaCharactersMessage],
            'contain_only_english_characters' => ['value' => $this->containOnlyEnglishCharacters, 'message' => $this->containOnlyEnglishCharactersMessage],
            'slug' => $this->slug,
            ...$data,
        ];
        if ($this->length != null) {
            $data['length'] = ['value' => $this->length->toArray(), 'message' => $this->lengthMessage];
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        $length = isset($array['length']) ? NumberRange::fromArray($array['length']['value']) : null;

        return new static(
            isAlphaSupported: $array['is_alpha_supported']['value'] ?? true,
            isAlphaSupportedMessage: $array['is_alpha_supported']['message'] ?? null,
            isNumberSupported: $array['is_number_supported']['value'] ?? true,
            isNumberSupportedMessage: $array['is_number_supported']['message'] ?? null,
            isSpecialCharacterSupported: $array['is_special_character_supported']['value'] ?? true,
            isSpecialCharacterSupportedMessage: $array['is_special_character_supported']['message'] ?? null,
            containOnlyBanglaCharacters: $array['contain_only_bangla_characters']['value'] ?? false,
            containOnlyBanglaCharactersMessage: $array['contain_only_bangla_characters']['message'] ?? null,
            containOnlyEnglishCharacters: $array['contain_only_english_characters']['value'] ?? false,
            containOnlyEnglishCharactersMessage: $array['contain_only_english_characters']['message'] ?? null,
            length: $length,
            lengthMessage: $array['length']['message'] ?? null,
            isRequired: $array['is_required']['value'] ?? true,
            isRequiredMessage: $array['is_required']['message'] ?? null,
            slug: $array['slug'] ?? null
        );
    }

    /**
     * @throws ValidationRuleException
     */
    public function validate(?string $data, string $label): void
    {
        $this->validateCommonRules($data, $label);
        if ($this->containOnlyEnglishCharacters) {
            $this->checkContainsOnlyEnglishCharacters($data, $label);
        }
        if ($this->length != null) {
            $this->checkLength($data);
        }
        if (! $this->isAlphaSupported) {
            $this->checkShouldNotContainAlpha($data, $label);
        }
        if (! $this->isNumberSupported) {
            $this->checkShouldNotContainNumber($data, $label);
        }
        if (! $this->isSpecialCharacterSupported) {
            $this->checkShouldNotContainSpecialCharacter($data, $label);
        }
        if ($this->containOnlyBanglaCharacters) {
            $this->checkShouldOnlyContainBanglaCharacter($data, $label);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    private function checkLength($data)
    {
        if ($this->length->isNotInRange(strlen($data))) {
            throw new ValidationRuleException($this->lengthMessage);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    private function checkShouldNotContainAlpha($data, $label): void
    {
        if (ctype_alpha($data)) {
            throw new ValidationRuleException($label .'-এ '.$this->isAlphaSupportedMessage);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    private function checkShouldNotContainNumber($data, $label): void
    {
        if (preg_match('~[0-9]+~', $data)) {
            throw new ValidationRuleException($label .'-এ '.$this->isNumberSupportedMessage);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    private function checkShouldNotContainSpecialCharacter($data, $label): void
    {
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+-]/', $data)) {
            throw new ValidationRuleException($label .'-এ '.$this->isSpecialCharacterSupportedMessage);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    public function checkShouldOnlyContainBanglaCharacter($data, $label): void
    {
        if (! preg_match('/^[\x{0980}-\x{09E6}\s]*$/u', $data)) {
            throw new ValidationRuleException($label .'-এর '.$this->containOnlyBanglaCharactersMessage);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    private function checkContainsOnlyEnglishCharacters($data, $label): void
    {
        if (! preg_match('/^[a-z\s!#$%&()-_{}\'"|`~]+$/', $data)) {
            throw new ValidationRuleException($label .'-এর '.$this->containOnlyEnglishCharactersMessage);
        }
    }
}
