<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    // EDIT METHOD - Show edit form
    public function edit($announcement)
    {
        try {
            // Find announcement - can be ID or Eloquent model
            if (is_numeric($announcement)) {
                $announcement = Announcement::findOrFail($announcement);
            }
            
            $title = 'Edit Announcement';
            
            // Get files
            $files = $announcement->getMedia('announcement_files')->map(function($media) {
                return (object) [
                    'id' => $media->id,
                    'name' => $media->file_name,
                    'url' => $media->getUrl(),
                ];
            })->toArray();
            
            return view('announcements.edit', compact('announcement', 'title', 'files'));
            
        } catch (\Exception $e) {
            Log::error('Edit announcement error: ' . $e->getMessage());
            return redirect()->route('announcements.index')->with('error', 'Announcement not found');
        }
    }

    // UPDATE METHOD - Save changes
    public function update($announcement, Request $request)
    {
        try {
            if (is_numeric($announcement)) {
                $announcement = Announcement::findOrFail($announcement);
            }
            
            $request->validate([
                'title' => 'required|string',
                'content' => 'required|string'
            ]);
            
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
            
            // Handle new files
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $announcement->addMedia($file)->toMediaCollection('announcement_files');
                }
            }
            
            Log::info('Announcement updated: ' . $announcement->id);
            
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement updated successfully');
                
        } catch (\Exception $e) {
            Log::error('Update announcement error: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error updating announcement: ' . $e->getMessage());
        }
    }

    // DESTROY METHOD - Delete announcement
    public function destroy($announcement)
    {
        try {
            if (is_numeric($announcement)) {
                $announcement = Announcement::findOrFail($announcement);
            }
            
            // Delete media files
            $announcement->clearMediaCollection('announcement_files');
            
            // Delete the announcement
            $announcement->delete();
            
            Log::info('Announcement deleted: ' . $announcement->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete announcement error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // INDEX METHOD - List announcements
    public function index()
    {
        $announcements = Announcement::where('parent_id', null)
            ->orderBy('created_at', 'desc')
            ->with('author')
            ->get()
            ->map(function($announcement) {
                $announcement->sender = $announcement->author ? $announcement->author->name : 'Unknown';
                return $announcement;
            });
        
        $title = 'Announcements';
        return view('announcements.index', compact('announcements', 'title'));
    }

    // SHOW METHOD
    public function show($announcement)
    {
        if (is_numeric($announcement)) {
            $announcement = Announcement::findOrFail($announcement);
        }
        return view('announcements.show', compact('announcement'));
    }

    // CREATE METHOD
    public function create()
    {
        return view('announcements.create');
    }

    // STORE METHOD
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'content' => 'required|string'
            ]);
            
            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'author' => Auth::id()
            ]);
            
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $announcement->addMedia($file)->toMediaCollection('announcement_files');
                }
            }
            
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement created successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error creating announcement: ' . $e->getMessage());
        }
    }
}