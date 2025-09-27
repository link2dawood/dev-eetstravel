<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function delete(int $id){
        File::findOrFail($id)->delete();
        // return back();
    }
}
