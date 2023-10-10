<?php

namespace Roghumi\Press\Crud\Validations;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Json object structure validation rule
 */
class JsonKeysIn implements ValidationRule
{
    public function __construct(
        protected Collection $heyStack,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            $jsonKeys = array_keys($value);
            foreach ($jsonKeys as $key) {
                if (! $this->heyStack->keys()->contains($key)) {
                    $fail('press.validation.json_keys_in.failed')->translate([
                        ':heyStack' => $this->heyStack->join(','),
                        ':key' => $key,
                    ]);
                }
            }
        } else {
            $fail('press.validation.json_keys_in.not_object')->translate();
        }
    }
}
