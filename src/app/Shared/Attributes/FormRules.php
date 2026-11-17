<?php

namespace App\Shared\Attributes;

use Attribute;
use Spatie\LaravelData\Data;

/**
 * Put on a controller method that renders a form page: the Data class's required fields
 * are shared with React as `form_rules.required` (consumed by useRequired()).
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class FormRules
{
    /**
     * @param  class-string<Data>  $data
     */
    public function __construct(public string $data) {}
}
