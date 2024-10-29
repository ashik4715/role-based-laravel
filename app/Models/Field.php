<?php

namespace App\Models;

use App\Services\Application\Form\Fields\Field as FieldDto;
use App\Services\Application\Form\Fields\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ["page_id", "slug", "label", "description", "type", "group_id", "order", "is_searchable", "rules", "visible_if", "is_repeatable", "possible_values", "is_editable", "cached_key", "dependent_field", "repeatable_text", "repeatable_class", "visibility_dependent_field"];
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function getType(): FieldType
    {
        return FieldType::tryFrom($this->type);
    }

    public function getField(): FieldDto
    {
        return $this->getType()->getFieldByArray($this->toArrayForDto());
    }

    public function toArrayForDto(): array
    {
        $data = $this->toArray();
        $data['rules'] = json_decode($data['rules'], 1);
        $data['visible_if'] = json_decode($data['visible_if'], 1);
        $data['possible_values'] = json_decode($data['possible_values'], 1);
        $data['visibility_dependent_field'] = json_decode($data['visibility_dependent_field'], 1);
        return $data;
    }
}
