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
}
