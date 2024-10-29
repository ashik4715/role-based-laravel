<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    private $page;

    private $fields;

    public function __construct($resource, $page, $fields)
    {
        $this->fields = $fields;
        $this->page = $page;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'page' => new PageResource($this->page, $this->fields),

        ];
    }
}
