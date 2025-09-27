<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';
    protected $fillable = [
        'url', 'path', 'original_name', 'mime_type', 'size'
    ];  
    
    public function attachable()
    {
        return $this->morphTo();
    }    
}
