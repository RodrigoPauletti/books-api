<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BookIncidesRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $this->validateIndices($value) || $fail('The :attribute is invalid');
    }

    protected function validateIndices($indices) {
        foreach ($indices as $index) {
            if (!isset($index['title']) || !is_string($index['title']) ||
                !isset($index['page']) || !is_int($index['page']) || $index['page'] < 1 ||
                !isset($index['subindices']) || !is_array($index['subindices'])){
                return false;
            }

            // Recursively validate subindices
            if (!$this->validateIndices($index['subindices'])) {
                return false;
            }
        }
        return true;
    }

}
