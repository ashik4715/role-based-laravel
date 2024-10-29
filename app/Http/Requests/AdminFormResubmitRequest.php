<?php

namespace App\Http\Requests;

use App\Services\Application\Form\Fields\FieldType;
use App\Services\Application\Form\Fields\McqOption;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use Illuminate\Foundation\Http\FormRequest;

class AdminFormResubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * @return ResubmissionRequestItem[]
     */
    public function getResubmissionRequestItems(): array
    {
        $resubmissionRequestItems = [];
        foreach ($this->items as $item) {
            $resubmissionRequestItems[] = new ResubmissionRequestItem(
                sectionSlug: $item['section_slug'],
                resubmissionNote: $item['resubmission_note'],
                fieldSlug: $item['field_slug'] ?? null,
                pageSlug: $item['page_slug'] ?? null
            );
        }

        return $resubmissionRequestItems;
    }
}
