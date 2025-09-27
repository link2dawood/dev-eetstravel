<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\HelperTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Announcement extends Model implements HasMedia
{
    use SoftDeletes;
    use HelperTrait;
    use InteractsWithMedia;

    // Only declare once
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    /**
     * Get child announcements
     */
    public function childs()
    {
        return $this->hasMany('App\Announcement', 'parent_id', 'id');
    }

    /**
     * Get the author of the announcement
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'author');
    }

    /**
     * Override delete to remove children
     */
    public function delete()
    {
        // Delete child announcements first
        $this->childs()->delete();

        // Then delete this announcement
        parent::delete();
    }
}
