<?php

namespace App\Models;

use App\Models\Field as FieldModel;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Button\Button;
use App\Services\Application\Form\Button\ButtonAction;
use App\Services\Application\Form\Fields\CheckBoxField;
use App\Services\Application\Form\Fields\RadioField;
use App\Services\Application\HasStepType;
use App\Services\Application\Step\ErrorStep;
use App\Services\Application\Step\OtpStep;
use App\Services\Application\Step\ReviewStep;
use App\Services\Application\Step\SignatureStep;
use App\Services\Application\Step\Step;
use App\Services\Application\Step\StepType;
use App\Services\Application\Step\SuccessStep;
use App\Services\Application\Step\SurveyStep;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, HasStepType, SoftDeletes;

    protected $casts = [
        'type' => StepType::class,
        'buttons' => 'array'
    ];

    protected $fillable = ["label", "order", "slug", "type", "is_editable", "visible_if", "description", "buttons"];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * @param  Builder  $query
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
     * @param  string  $slug
     * @return Builder
     */
    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @return Page|null
     */
    public function getFirstPage(): ?Page
    {
        if ($this->doesntHaveMultiplePage()) {
            return null;
        }
        /** @var Page|null $page */
        $page = $this->pages()->where('order', 1)->first();

        return $page;
    }

    public function getLastPage(): ?Page
    {
        if ($this->doesntHaveMultiplePage()) {
            return null;
        }
        /** @var Page|null $page */
        $page = $this->pages()->orderBy('order', 'DESC')->first();

        return $page;
    }

    /**
     * @return SurveyStep
     */
    public function getSurveyStep(): SurveyStep
    {
        /** @var FieldModel $field */
        $field = $this->pages->first()->fields->first();
        /** @var RadioField $mcqField */
        $surveyField = $field->getField();

        return new SurveyStep(label: $this->label, description: $this->description, sectionSlug: $this->slug, sectionOrder: $this->order, field: $surveyField);
    }

    /**
     * @param  Page|null  $page
     * @return Step
     */
    public function getStep(?Page $page, ?ApplicationData $applicationData = null): Step
    {
        if ($this->isSurvey()) {
            return $this->getSurveyStep();
        }
        if ($this->isOtp()) {
            return new OtpStep(label: $this->label, description: $this->description, sectionSlug: $this->slug, sectionOrder: $this->order, applicationData: $applicationData);
        }
        if ($this->isSignature()) {
            return new SignatureStep(label: $this->label, description: $this->description, sectionSlug: $this->slug, sectionOrder: $this->order);
        }
        if ($this->isReview()) {
            return $this->getReviewStep($applicationData);
        }

        if ($this->isSuccess()) {
            return new SuccessStep(label: $this->label, description: $this->description, sectionSlug: $this->slug, sectionOrder: $this->order);
        }

        if ($this->isError()) {
            $buttons = [];
            foreach ($this->buttons as $button) {
                $buttons[] = new Button(title: $button['title'], action: ButtonAction::from(($button['action'])), link: $button['link'] ?? null);
            }

            return new ErrorStep(label: $this->label, description: $this->description, buttons: $buttons);
        }

        return $page->getStep($this->slug, $this->order, $applicationData);
    }

    public function getReviewStep(?ApplicationData $applicationData): ReviewStep
    {
        return new ReviewStep(label: $this->label, description: $this->description, sectionSlug: $this->slug, sectionOrder: $this->order, applicationData: $applicationData);
    }

    public function getNextSection(): ?self
    {
        return Section::order($this->order + 1)->first();
    }

    public function doesntHaveMultiplePage(): bool
    {
        return ! $this->hasMultiplePage();
    }

    public function hasMultiplePage(): bool
    {
        return $this->isFormSection() || $this->isNID() || $this->isInformation();
    }
}
