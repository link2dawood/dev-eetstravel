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
    // Validate the request (images only: the files are served from the public folder)
    $request->validate([
        'files' => 'required',
        'files.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:10240', // Max 10MB per file
        'id' => 'required|integer',
        'model' => 'required|string|in:Attachmenttype,Tour',
    ], [
        'files.*.image' => 'Please choose an image file (JPG, PNG, GIF or WEBP).',
        'files.*.mimes' => 'Please choose an image file (JPG, PNG, GIF or WEBP).',
        'files.*.max' => 'The image must be 10 MB or smaller.',
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
            $extension = $file->guessExtension() ?: 'jpg'; // from the content, never the client's name
            $uniqueName = time() . '_' . uniqid() . '.' . $extension;

            // Store in public/uploads/attachments (served directly, like uploads/avatars;
            // the public/storage link is not always present)
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $storedPath = 'uploads/attachments/' . $uniqueName;
            $file->move(public_path('uploads/attachments'), $uniqueName);

            // "Replace image": the pages show the first attachment, so remove the old ones
            foreach ($model->attachments()->get() as $old) {
                if (strpos((string) $old->path, 'uploads/attachments/') === 0 && is_file(public_path($old->path))) {
                    @unlink(public_path($old->path));
                }
                $old->delete();
            }

            // Create attachment record
            $attachment = new \App\Attachment();
            $attachment->url = asset($storedPath);
            $attachment->path = $storedPath;
            $attachment->original_name = $originalName;
            $attachment->mime_type = $mimeType;
            $attachment->size = $size;
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
