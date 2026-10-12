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

    /**
     * Older uploads stored an absolute URL to the server they were uploaded on
     * (e.g. http://dev.eetstravel.com/uploads/x.jpg). When the file is in this
     * server's public/uploads, serve it from the current domain instead.
     */
    public function getUrlAttribute($value)
    {
        $path = parse_url((string) $value, PHP_URL_PATH);
        if ($path && strpos($path, '/uploads/') === 0) {
            $file = rawurldecode(substr($path, strlen('/uploads/')));
            if ($file !== '' && is_file(public_path('uploads/' . $file))) {
                return asset('uploads/' . $file);
            }
        }
        return $value;
    }
}
