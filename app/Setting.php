<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Setting
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $fillable = ['name', 'description', 'value'];

    /**
     * Settings read by name in scheduled commands; deleting one breaks or silently changes them.
     */
    const SYSTEM = [
        'tour_service_notification',          // TourPackageNotification (days)
        'hotel_seasons_date',                 // RunImport (date and time)
        'client_fio_text',
        \App\Helper\SettingsHelper::TASK_DEADLINE, // TaskDeadlineNotification (days)
    ];

    /**
     * The value type follows the name, the same way the edit form picks its input.
     */
    public static function valueType($name)
    {
        if (preg_match('/date/', (string) $name)) {
            return 'date';
        }

        return preg_match('/text/', (string) $name) ? 'text' : 'number';
    }

    public static function valueRules($name)
    {
        switch (self::valueType($name)) {
            case 'date':
                return 'required|date_format:Y-m-d H:i:s';
            case 'text':
                return 'required|string|max:191';
            default:
                // Day counts are passed to Carbon::addDays(); keep them whole and non-negative.
                return 'required|integer|min:0|max:3650';
        }
    }

    public function isSystem()
    {
        return in_array($this->name, self::SYSTEM, true);
    }
}
