<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Repositories\Application\ApplicationRepository;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Services\Application\DTO\BdMobile;
use App\Services\Application\SectionPageChecker;
use Illuminate\Foundation\Http\FormRequest;

class GetStepRequest extends FormRequest
{
    use GetApplication;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'mobile' => ['required'],
            'section_slug' => ['required'],
            'page_slug' => ['string'],
        ];
    }

    /**
     * @throws \Exception
     */
    public function getApplication(): Application
    {
        /** @var ApplicationRepository $applicationRepository */
        $applicationRepository = app(ApplicationRepositoryInterface::class);
        $mobile = new BdMobile($this->mobile);

        return $applicationRepository->getLatestApplicationByMobile($mobile);
    }
}
