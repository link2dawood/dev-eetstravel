<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements
     */
    public function index()
    {
        try {
            $announcements = Announcement::where('parent_id', null)
                ->orderBy('created_at', 'desc')
                ->with(['author'])
                ->get()
                ->map(function($announcement) {
                    $announcement->sender = $announcement->author ? $announcement->author->name : 'Unknown';
                    
                    $announcement->files = $announcement->getMedia('announcement_files')->map(function($media) {
                        return (object) [
                            'id' => $media->id,
                            'name' => $media->file_name,
                            'url' => $media->getUrl(),
                        ];
                    });
                    
                    return $announcement;
                });
            
            $title = 'Announcements';
            return view('announcements.index', compact('announcements', 'title'));
            
        } catch (\Exception $e) {
            Log::error('Announcement index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading announcements');
        }
    }

    /**
     * Show the form for creating a new announcement
     */
    public function create()
    {
        $title = '';
        $parent_id = request()->get('parent_id', null);
        return view('announcements.create', compact('title', 'parent_id'));
    }

    /**
     * Store a newly created announcement
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'files.*' => 'nullable|file|max:10240' // 10MB max per file
            ]);
            
            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'author' => Auth::id(),
                'parent_id' => $request->parent_id
            ]);
            
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $announcement->addMedia($file)
                        ->toMediaCollection('announcement_files');
                }
            }
            
            Log::info('Announcement created: ' . $announcement->id);
            
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement created successfully');
                
        } catch (\Exception $e) {
            Log::error('Store announcement error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating announcement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified announcement
     *
     * == FIX: Changed $id to $announcement (matches route parameter) ==
     */
    public function show($announcement) // <-- CHANGED
    {
        try {
            // Find the model using the passed ID
            $announcement = Announcement::with(['author', 'childs.author'])
                ->findOrFail($announcement); // <-- CHANGED
            
            return view('announcements.show', compact('announcement'));
            
        } catch (\Exception $e) {
            Log::error('Show announcement error: ' . $e->getMessage());
            return redirect()->route('announcements.index')
                ->with('error', 'Announcement not found');
        }
    }

    /**
     * Show the form for editing the announcement
     *
     * == FIX: Changed $id to $announcement (matches route parameter) ==
     */
    public function edit($announcement) // <-- CHANGED
    {
        try {
            \Log::info('Edit announcement called with ID: ' . $announcement);
            
            // Find the model using the passed ID
            $announcement = Announcement::with('author')->findOrFail($announcement); // <-- CHANGED
            
            \Log::info('Announcement found: ' . $announcement->id);
            
            if (Auth::id() != $announcement->author && !Auth::user()->can('announcements.edit')) {
                \Log::warning('User ' . Auth::id() . ' denied edit permission for announcement ' . $announcement->id);
                return redirect()->route('announcements.index')
                    ->with('error', 'You do not have permission to edit this announcement');
            }
            
            $title = 'Edit Announcement';
            $announcement->author_name = $announcement->author ? $announcement->author->name : 'Unknown';

            $files = [];
            if (Schema::hasTable('media')) {
                $files = $announcement->getMedia('announcement_files')->map(function($media) {
                    return (object) [
                        'id' => $media->id,
                        'name' => $media->file_name,
                        'url' => $media->getUrl(),
                    ];
                })->toArray();
            }
            
            \Log::info('Rendering edit view for announcement: ' . $announcement->id);
            
            return view('announcements.edit', compact('announcement', 'title', 'files'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Announcement not found: ' . $announcement); // <-- CHANGED
            return redirect()->route('announcements.index')
                ->with('error', 'Announcement not found');
        } catch (\Exception $e) {
            \Log::error('Edit announcement error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->route('announcements.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified announcement
     *
     * == FIX: Changed $id to $announcement (matches route parameter) ==
     */
    public function update(Request $request, $announcement) // <-- CHANGED
    {
        try {
            // Find the model using the passed ID
            $announcement = Announcement::findOrFail($announcement); // <-- CHANGED
            
            if (Auth::id() != $announcement->author && !Auth::user()->can('announcements.edit')) {
                return redirect()->route('announcements.index')
                    ->with('error', 'You do not have permission to update this announcement');
            }
            
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'files.*' => 'nullable|file|max:10240'
            ]);
            
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
            
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $announcement->addMedia($file)
                        ->toMediaCollection('announcement_files');
                }
            }

            if ($request->has('deleted_files')) {
                $deleted_ids = explode(',', $request->input('deleted_files'));
                if (count($deleted_ids) > 0) {
                    $mediaItems = $announcement->getMedia('announcement_files');
                    foreach ($mediaItems as $media) {
                        if (in_array($media->id, $deleted_ids)) {
                            $media->delete();
                        }
                    }
                }
            }
            
            Log::info('Announcement updated: ' . $announcement->id);
            
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement updated successfully');
                
        } catch (\Exception $e) {
            Log::error('Update announcement error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating announcement: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified announcement
     *
     * == FIX: Changed $id to $announcement (matches route parameter) ==
     * == FIX: Changed JSON return to a Redirect ==
     */
    public function destroy($announcement) // <-- CHANGED
    {
        try {
            // Find the model using the passed ID
            $announcement = Announcement::findOrFail($announcement); // <-- CHANGED
            
            if (Auth::id() != $announcement->author && !Auth::user()->can('announcements.delete')) {
                // == FIX: Return a redirect with an error ==
                return redirect()->route('announcements.index')
                    ->with('error', 'You do not have permission to delete this announcement');
            }
            
            if (Schema::hasTable('media')) {
                $announcement->clearMediaCollection('announcement_files');
            }
            
            $announcement->delete();
            
            Log::info('Announcement deleted: ' . $announcement->id); // <-- CHANGED
            
            // == FIX: Return a redirect with success ==
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement deleted successfully');
            
        } catch (\Exception $e) {
            Log::error('Delete announcement error: ' . $e->getMessage());
            
            // == FIX: Return a redirect with an error ==
            return redirect()->route('announcements.index')
                ->with('error', 'Error deleting announcement: ' . $e->getMessage());
        }
    }

    /**
     * Handle file deletion
     */
    public function deleteFile(Request $request)
    {
        try {
            $mediaId = $request->input('file_id');
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
            
            $announcement = Announcement::findOrFail($media->model_id);
            if (Auth::id() != $announcement->author && !Auth::user()->can('announcements.edit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $media->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete file error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file'
            ], 500);
        }
    }

    /**
     * Handle announcement reply
     */
    public function reply(Request $request, $id) // This can stay as $id since it's a custom route
    {
        try {
            $request->validate([
                'content' => 'required|string'
            ]);
            
            $parentAnnouncement = Announcement::findOrFail($id);
            
            $reply = Announcement::create([
                'title' => 'Re: ' . $parentAnnouncement->title,
                'content' => $request->content,
                'author' => Auth::id(),
                'parent_id' => $id
            ]);
            
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $reply->addMedia($file)
                        ->toMediaCollection('announcement_files');
                }
            }
            
            return redirect()->route('announcements.show', $id)
                ->with('success', 'Reply posted successfully');
                
        } catch (\Exception $e) {
            Log::error('Reply error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error posting reply');
        }
    }
}