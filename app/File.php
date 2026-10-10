<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\File
 *
 * @property int $id
 * @property int|null $flight_id
 * @property int|null $hotel_id
 * @property int|null $restaurant_id
 * @property int|null $event_id
 * @property int|null $guide_id
 * @property int|null $transfer_id
 * @property int|null $cruises_id
 * @property int|null $tour_id
 * @property int|null $comment_id
 * @property int|null $task_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $attach_file_name
 * @property int|null $attach_file_size
 * @property string|null $attach_content_type
 * @property string|null $attach_updated_at
 * @property int|null $announcement_id
 * @property int|null $client_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereAnnouncementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereAttachContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereAttachFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereAttachFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereAttachUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCruisesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereFlightId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereGuideId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereTourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    protected $guarded = [];

    /** Public URL of the stored file (uploads live on the "public" disk). */
    public function getUrlAttribute()
    {
        if (!$this->attach_file_name) {
            return null;
        }
        // Older uploads (Stapler) store only the bare file name under public/system/App/File/attaches/000/000/<id>/original/
        if (strpos($this->attach_file_name, '/') === false) {
            $legacy = 'system/App/File/attaches/' . implode('/', str_split(str_pad($this->id, 9, '0', STR_PAD_LEFT), 3)) . '/original/' . $this->attach_file_name;
            if (is_file(public_path($legacy))) {
                return asset($legacy);
            }
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->attach_file_name);
    }

    public function getDisplayNameAttribute()
    {
        return basename((string) $this->attach_file_name);
    }

    public function isImage()
    {
        return in_array($this->attach_content_type, ['image/png', 'image/jpeg', 'image/gif', 'image/webp']);
    }
}
