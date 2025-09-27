<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ChatMessage
 *
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property string $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $author
 * @property-read \App\Chat $chat
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChatMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChatMessage whereUserId($value)
 * @mixin \Eloquent
 */
class ChatMessage extends Model
{
    protected $guarded = [];

    public function chat()
    {
        return $this->belongsTo('App\Chat');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
