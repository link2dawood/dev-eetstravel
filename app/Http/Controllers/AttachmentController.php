<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Attachment;

class AttachmentController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $attachmenttypes = \App\Attachmenttype::all();

        return view('attachment.index', ['attachmenttypes' => $attachmenttypes]);
    }
    
    
public function saveFile(Request $request)
{
    // Validate the request
    $request->validate([
        'files' => 'required',
        'files.*' => 'file|max:10240', // Max 10MB per file
        'id' => 'required|integer',
        'model' => 'required|string',
    ]);

    try {
        $files = $request->file('files');
        $id = $request->id;
        $modelName = 'App\\' . $request->model;

        // Validate model exists
        if (!class_exists($modelName)) {
            return response()->json(['error' => 'Invalid model specified'], 400);
        }

        $model = $modelName::find($id);
        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $uploadedFiles = [];

        // Ensure files is an array
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $uniqueName = time() . '_' . uniqid() . '.' . $extension;

            // Store file using Laravel's default storage
            $storedPath = $file->storeAs('uploads', $uniqueName, 'public');

            if (!$storedPath) {
                continue;
            }

            // Create attachment record
            $attachment = new \App\Attachment();
            $attachment->url = asset('storage/' . $storedPath);
            $attachment->path = $storedPath;
            $attachment->original_name = $originalName;
            $attachment->mime_type = $file->getMimeType();
            $attachment->size = $file->getSize();
            $attachment->save();

            // Associate with model
            $model->attachments()->save($attachment);

            $uploadedFiles[] = [
                'id' => $attachment->id,
                'url' => $attachment->url,
                'original_name' => $attachment->original_name,
                'size' => $attachment->size
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully',
            'files' => $uploadedFiles
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'File upload failed: ' . $e->getMessage()
        ], 500);
    }
}

}
