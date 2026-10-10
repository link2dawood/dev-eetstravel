<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
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
