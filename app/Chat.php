<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Chat
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $type
 * @property int $author
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ChatMessage[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Chat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Chat extends Model
{
    use SoftDeletes;
    protected $table = 'chats';

    protected $guarded = [];
    
    protected $dates = ['deleted_at'];

    const CHAT_TYPE_ALL = 1;
    const CHAT_TYPE_CUSTOM = 2;
    const CHAT_TYPE_DIRECT = 3;

    public $types = [
        self::CHAT_TYPE_ALL => 'all',
        self::CHAT_TYPE_CUSTOM => 'custom',
        self::CHAT_TYPE_DIRECT => 'direct'
    ];

    /**
     * messages.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function messages()
    {
        return $this->hasMany('App\ChatMessage');
    }

    /**
     * Assign a message.
     *
     * @param  $message
     * @return  mixed
     */
    public function assignMessage($message)
    {
        return $this->messages()->attach($message);
    }

    /**
     * Remove a message.
     *
     * @param  $message
     * @return  mixed
     */
    public function removeMessage($message)
    {
        return $this->messages()->detach($message);
    }

    /**
     * users
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'chat_user');
    }

    /**
     * Assign a user.
     *
     * @param  $user
     * @return  mixed
     */
    public function assignUser($user)
    {
        return $this->users()->attach($user);
    }

    /**
     * Remove a user.
     *
     * @param  $user
     * @return  mixed
     */
    public function removeUser($user)
    {
        return $this->users()->detach($user);
    }

    public static function getMainChat()
    {
    	return Chat::where(['type' => Chat::CHAT_TYPE_ALL])->get();
    }

    public static function isUserHasAccess(Chat $chat)
    {
	    return $chat->users->contains(\Auth::user()) || $chat->type == Chat::CHAT_TYPE_ALL;
    }

}
