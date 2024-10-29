<?php

namespace App\Models;

use App\Models\Field as FieldModel;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\Fields\Field as Field;
use App\Services\Application\Form\Fields\FieldTypeException;
use App\Services\Application\Form\Fields\GroupField;
use App\Services\Application\HasStepType;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\InformationStep;
use App\Services\Application\Step\NidStep;
use App\Services\Application\Step\Selfie;
use App\Services\Application\Step\Step;
use App\Services\Application\Step\StepType;
use App\Services\Application\Step\TradeLicenseStep;
use App\Services\wegro\BasicCheckerForWegro;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasStepType;

    protected $fillable = ["section_id", "label", "order", "type", "slug", "is_editable", "description", "visible_if"];
    protected $casts = [
        'type' => StepType::class,
    ];

    public function fields()
    {
        return $this->hasMany(FieldModel::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @param  Builder  $query
     * @param  int  $order
     * @return Builder
     */
    public function scopeFirstOrder(Builder $query): Builder
    {
        return $query->order(1);
    }

    /**
     * @param  Builder  $query
     * @param  int  $order
     * @return Builder
     */
    public function scopeOrder(Builder $query, int $order): Builder
    {
        return $query->where('order', $order);
    }

    /**
     * @param  Builder  $query
     * @param  int  $order
     * @return Builder
     */
    public function scopeNextOrder(Builder $query, self $page): Builder
    {
        return $query->order($page->order + 1);
    }

    /**
     * @param  Builder  $query
     * @param  string  $slug
     * @return Builder
     */
    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @return Field[]
     *
     * @throws FieldTypeException
     */
    public function getFields(?ApplicationData $applicationData = null): array
    {
        $data = [];
        $groupId = null;
        foreach ($this->fields->sortBy('order') as $field) {
            /** @var FieldModel  $field */
            if ($field->visible_if && $applicationData){
                $condition = ConditionWrapper::fromArray(json_decode($field->visible_if, true))->setApplicationData($applicationData);
                $groupId = $field->id;
                if (!$condition->isBackendVisible()) continue;
            }
            if ($groupId != null && $field->group_id == $groupId) continue;
            else $groupId = null;

            $fieldDto = $field->getField();

            if ($fieldDto->isRepeatable()) $fieldDto->repeater->generateRepeatData($applicationData);

            if ($field->group_id) {
                $groupField = $data[$field->group_id];
                if (! $groupField instanceof GroupField) {
                    throw new FieldTypeException('The given group id not found', 404);
                }
                $groupField->addChildField($fieldDto);
            } else {
                $data[$field->id] = $fieldDto;
            }
        }

        return array_values($data);
    }

    /**
     * @param  string  $sectionSlug
     * @return FormSectionStep
     */
    public function getFormSectionStep(string $sectionSlug, int $sectionOrder, ?ApplicationData $applicationData = null): FormSectionStep
    {
        $step = new FormSectionStep(
            label: $this->label,
            description: $this->description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            pageSlug: $this->slug,
            pageOrder: $this->order,
            showReview: BasicCheckerForWegro::shouldShowReview($sectionSlug, $applicationData)
        );
        foreach ($this->getFields($applicationData) as $field) {
            $step->addOrUpdateField($field);
        }

        return $step;
    }

    public function getStep(string $sectionSlug, int $sectionOrder, ?ApplicationData $applicationData = null): Step
    {
        if ($this->isNID()) {
            return new NidStep(label: $this->label, description: $this->description, sectionSlug: $sectionSlug, sectionOrder: $sectionOrder, pageSlug: $this->slug, pageOrder: $this->order, showReview: BasicCheckerForWegro::shouldShowReview($sectionSlug, $applicationData));
        }
        if ($this->isInformation()) {
            return new InformationStep(label: $this->label, description: $this->description, sectionSlug: $sectionSlug, sectionOrder: $sectionOrder, pageSlug: $this->slug, pageOrder: $this->order);
        }
        if ($this->isSelfie()) {
            return new Selfie(label: $this->label, description: $this->description, sectionSlug: $sectionSlug, sectionOrder: $sectionOrder, pageSlug: $this->slug, pageOrder: $this->order);
        }
        if ($this->isTradeLicense()) {
            return new TradeLicenseStep(label: $this->label, description: $this->description, sectionSlug: $sectionSlug, sectionOrder: $sectionOrder, pageSlug: $this->slug, pageOrder: $this->order);
        }

        return $this->getFormSectionStep($sectionSlug, $sectionOrder, $applicationData);
    }
}
