<?php

namespace App\Http\Requests\Concerns;

use App\RoomTypes;

/**
 * Rules shared by the tour create and edit requests.
 */
trait TourFieldRules
{
    /** Letters (any language), numbers, spaces and the punctuation used in real tour names */
    public static $nameRegex = '/^[\pL\pN\s\-#.\'&\/,()+_\[\]:]+$/u';

    public static $nameRegexMessage = 'Tour name can only contain letters, numbers, spaces and - # . \' & / , ( ) + _ [ ] :';

    /** Children and room-type quantities posted by the tour forms */
    protected function childAndRoomRules()
    {
        return [
            'child_count' => 'nullable|integer|min:0|max:50',
            'ages' => 'nullable|array|max:50',
            'ages.*' => 'nullable|integer|min:0|max:17',
            'prices' => 'nullable|array|max:50',
            'prices.*' => 'nullable|numeric|min:0|max:99999.99',
            'room_types_qty' => ['nullable', 'array', function ($attribute, $value, $fail) {
                $ids = array_keys(array_filter((array) $value, function ($qty) {
                    return $qty !== null && $qty !== '';
                }));
                if (!$ids) {
                    return;
                }
                $known = RoomTypes::whereIn('id', $ids)->pluck('id')->map(function ($id) {
                    return (string) $id;
                })->all();
                if (array_diff(array_map('strval', $ids), $known)) {
                    $fail('One of the selected room types does not exist.');
                }
            }],
            'room_types_qty.*' => 'nullable|integer|min:0|max:500',
        ];
    }
}
