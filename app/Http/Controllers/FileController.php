<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function download(int $id)
    {
        $file = File::findOrFail($id);
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        abort_unless($file->attach_file_name && $disk->exists($file->attach_file_name), 404);

        if ($file->isImage()) {
            return $disk->response($file->attach_file_name);
        }

        return $disk->download($file->attach_file_name, $file->display_name);
    }

    public function delete(int $id){
        $file = File::findOrFail($id);
        // remove the stored upload as well, not only the database row
        if ($file->attach_file_name) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($file->attach_file_name);
        }
        $file->delete();
        // return back();
    }
}
