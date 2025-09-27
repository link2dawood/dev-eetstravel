<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachmenttype extends Model
{
    protected $fillable = [
        'name', 'model'
    ];
    
    public function attachments(){
        return $this->morphMany('App\Attachment', 'attachable');
    }
}
