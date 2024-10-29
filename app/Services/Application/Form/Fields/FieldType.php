<?php

namespace App\Services\Application\Form\Fields;

use App\Helpers\ReverseEnum;

enum FieldType: string
{
    use ReverseEnum;

    case CHECKBOX = 'checkbox';
    case DATE = 'date';
    case DECIMAL = 'decimal';
    case DROPDOWN = 'dropdown';
    case FILE = 'file';
    case GROUP = 'group';
    case MOBILE = 'mobile';
    case NUMBER = 'number';
    case RADIO = 'radio';
    case STRING = 'string';

    /**
     * @throws FieldTypeException
     */
    public function getFieldByArray(array $array): Field
    {
        if ($this == self::STRING) {
            return StringField::fromArray($array);
        } elseif ($this == self::MOBILE) {
            return MobileField::fromArray($array);
        } elseif ($this == self::NUMBER) {
            return NumberField::fromArray($array);
        } elseif ($this == self::DATE) {
            return DateField::fromArray($array);
        } elseif ($this->isMcqField()) {
            return RadioField::fromArray($array);
        } elseif ($this->isCheckboxField()) {
            return CheckBoxField::fromArray($array);
        } elseif ($this === self::FILE) {
            return FileField::fromArray($array);
        } elseif ($this === self::GROUP) {
            return GroupField::fromArray($array);
        }

        throw new FieldTypeException();
    }

    public function isMcqField(): bool
    {
        return $this === self::RADIO || $this === self::DROPDOWN;
    }

    public function isCheckboxField(): bool
    {
        return $this === self::CHECKBOX;
    }

    public function getClassName(): string
    {
        if ($this->isMcqField()) {
            return RadioField::class;
        }

        if ($this->isCheckboxField()) {
            return CheckBoxField::class;
        }

        return 'App\\Services\\Application\\Form\\Fields\\'.ucfirst($this->value).'Field';
    }
}
