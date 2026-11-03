<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Attachment;
use Illuminate\Support\Arr;

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
    
    
/**
 * Upload the single image of a supplier category (images page) or of a tour (landing page image).
 * Both show one image, so the new file replaces the previous image(s) of that record.
 */
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

    // The route needs tour.edit (tour landing images); category images are an admin setting
    if ($request->model === 'Attachmenttype') {
        abort_unless($request->user()->can('images.index'), 403, 'You do not have permission to change category images.');
    }

    $files = array_values(array_filter(Arr::wrap($request->file('files')), function ($file) {
        return $file && $file->isValid();
    }));
    // Each record shows one image: several files in one request would replace each other
    if (count($files) !== 1) {
        return response()->json(['message' => 'Upload one image at a time.', 'errors' => ['files' => ['Upload one image at a time.']]], 422);
    }
    $file = $files[0];

    $modelName = 'App\\' . $request->model;
    $model = $modelName::find($request->id);
    if (!$model) {
        return response()->json(['error' => 'The record to attach the image to was not found.'], 404);
    }

    try {
        $originalName = $file->getClientOriginalName();
        $extension = $file->guessExtension() ?: 'jpg'; // from the content, never the client's name
        $uniqueName = time() . '_' . uniqid() . '.' . $extension;
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Store in public/uploads/attachments (served directly, like uploads/avatars;
        // the public/storage link is not always present)
        $storedPath = 'uploads/attachments/' . $uniqueName;
        $file->move(public_path('uploads/attachments'), $uniqueName);

        $attachment = DB::transaction(function () use ($model, $storedPath, $originalName, $mimeType, $size) {
            // Replace only the previous image(s). Older tours may still have other attachments.
            $oldFiles = [];
            foreach ($model->attachments()->get() as $old) {
                if (!$this->isImageAttachment($old)) {
                    continue;
                }
                if (strpos((string) $old->path, 'uploads/attachments/') === 0) {
                    $oldFiles[] = public_path($old->path);
                }
                $old->delete();
            }

            $attachment = new Attachment();
            // Relative: Attachment::url builds the address for the current host and scheme
            $attachment->url = $storedPath;
            $attachment->path = $storedPath;
            $attachment->original_name = $originalName;
            $attachment->mime_type = $mimeType;
            $attachment->size = $size;
            $model->attachments()->save($attachment);

            // Old files go only once the new record is saved
            DB::afterCommit(function () use ($oldFiles) {
                foreach ($oldFiles as $path) {
                    if (is_file($path)) {
                        @unlink($path);
                    }
                }
            });

            return $attachment;
        });

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'files' => [[
                'id' => $attachment->id,
                'url' => $attachment->url,
                'original_name' => $attachment->original_name,
                'size' => $attachment->size,
            ]],
        ]);

    } catch (\Exception $e) {
        // Details (paths, SQL) go to the log, not to the browser
        \Log::error('Image upload failed', ['model' => $request->model, 'id' => $request->id, 'error' => $e->getMessage()]);
        if (isset($storedPath) && is_file(public_path($storedPath)) && empty($attachment)) {
            @unlink(public_path($storedPath));
        }

        return response()->json(['error' => 'The image could not be uploaded. Please try again.'], 500);
    }
}

/** Images by MIME type, or by extension for older rows saved without one */
private function isImageAttachment(Attachment $attachment)
{
    if ($attachment->mime_type) {
        return strpos($attachment->mime_type, 'image/') === 0;
    }

    return (bool) preg_match('/\.(jpe?g|png|gif|webp|bmp)$/i', (string) $attachment->path);
}

}
