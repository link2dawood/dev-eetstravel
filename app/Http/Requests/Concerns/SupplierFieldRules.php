<?php

namespace App\Http\Requests\Concerns;

use App\CriteriaType;
use Illuminate\Validation\Rule;

/**
 * Rules shared by the supplier (event, restaurant...) create and edit requests.
 */
trait SupplierFieldRules
{
    /**
     * The forms post criterias[] (checkboxes); older callers post a comma-separated string.
     */
    protected function normalizeCriterias()
    {
        if (!$this->exists('criterias')) {
            return;
        }
        $value = $this->input('criterias');
        $list = is_array($value) ? $value : explode(',', (string) $value);
        // Leave malformed nested values for the integer validator instead of throwing in trim().
        $this->merge(['criterias' => array_values(array_filter(array_map(function ($id) {
            return is_scalar($id) ? trim((string) $id) : $id;
        }, $list), function ($id) {
            return $id !== '';
        }))]);
    }

    /**
     * @param string $serviceType criteria_types.service_type, e.g. "event" or "restaurant"
     */
    protected function attachmentAndCriteriaRules($serviceType)
    {
        return [
            'attach' => 'nullable|array',
            // Same types as tour attachments; no SVG/HTML that would run on the app's domain
            'attach.*' => 'file|max:10240|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx',
            'criterias' => 'nullable|array',
            'criterias.*' => ['integer', Rule::exists('criterias', 'id')->whereIn(
                'criteria_type', CriteriaType::where('service_type', $serviceType)->pluck('id')->all()
            )],
        ];
    }

    protected function attachmentAndCriteriaMessages($serviceType)
    {
        return [
            'attach.*.mimes' => 'Attachments must be images, PDF, Word or Excel files.',
            'attach.*.max' => 'Each attachment must not exceed 10MB.',
            'criterias.*.integer' => 'One of the selected criteria is invalid.',
            'criterias.*.exists' => "One of the selected criteria is not a {$serviceType} criterion.",
        ];
    }
}
