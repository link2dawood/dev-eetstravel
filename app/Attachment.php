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
        // Build the URL from where the file is on this server, so it follows the current host/scheme.
        // Images page uploads live in public/uploads/attachments; tour landing images on the public
        // storage disk (served under /storage).
        $relative = ltrim((string) ($this->attributes['path'] ?? ''), '/');
        if ($relative !== '' && strpos($relative, 'uploads/') === 0) {
            if (is_file(public_path($relative))) {
                return asset($relative);
            }
            if (is_file(storage_path('app/public/' . $relative))) {
                return asset('storage/' . $relative);
            }
        }
        // New uploads store a relative url as well: never an absolute host
        if ($value !== null && $value !== '' && !preg_match('#^[a-z][a-z0-9+.-]*://#i', (string) $value)) {
            return asset(ltrim((string) $value, '/'));
        }

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
