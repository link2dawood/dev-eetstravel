<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Announcement;
use App\Helper\PermissionHelper;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Services\BaseService;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessLogicException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use View;
use Auth;

class AnnouncementController extends Controller
{
    /**
     * AnnouncementController constructor.
     */
    public function __construct()
    {
        $this->middleware('permissions.required');
    }

    public function index(Request $request)
    {
        $announcements = Announcement::with(['author', 'media'])
            ->where('parent_id', null)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($announcement) {
                // Get the sender information
                $announcement->sender = $announcement->author;

                // Get files/media associated with this announcement
                $announcement->files = $announcement->getMedia('announcement_files')
                    ->map(function($media) {
                        return (object) [
                            'url' => $media->getUrl(),
                            'name' => $media->file_name,
                        ];
                    });

                return $announcement;
            });

        $title = 'Index - Announcements';
        return view('announcements.index', compact('title', 'announcements'));
    }

    public function getActionButtons($id, $announcement)
    {
        $url = [
            'show'       => route('announcements.show', ['id' => $id]),
            'edit'       => route('announcements.edit', ['id' => $id]),
            'delete_msg' => "/announcement/{$id}/delete_msg"
        ];

        return DatatablesHelperController::getActionButton($url, false, $announcement);
    }

   public function data(Request $request)
{
    $query = Announcement::distinct()
        ->leftJoin('users', 'users.id', '=', 'announcements.author')
        ->select(
            'announcements.id as id',
            'announcements.title as title',
            'announcements.content as content',
            'announcements.created_at as created_at',
            'users.name as sender',
            'announcements.id as ann_id' // keep ID for media lookup
        )
        ->where('parent_id', null);

    // Get pagination parameters
    $perPage = $request->get('length', 15);
    $page = $request->get('start', 0) / $perPage + 1;

    // Get total count
    $total = $query->count();

    // Apply pagination
    $announcements = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

    // Process each announcement
    foreach($announcements as $announcement) {
        $announcement->action = $this->getActionButtons($announcement->id, $announcement);

        // fetch related media
        $mediaItems = Announcement::find($announcement->ann_id)
                        ->getMedia('announcement_files');

        if ($mediaItems->isEmpty()) {
            $announcement->files = [];
        } else {
            $announcement->files = $mediaItems->map(function ($media) {
                return [
                    'url' => $media->getUrl(),
                    'name' => $media->file_name,
                ];
            });
        }
    }

    return response()->json([
        'data' => $announcements,
        'recordsTotal' => $total,
        'recordsFiltered' => $total
    ]);
}

    public function deleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/announcement/' . $id . '/delete');
        if ($request->user()->id != Announcement::find($id)->author) return $msg;

        if ($request->ajax()) {
            return $msg;
        }
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrfail($id);

        $return_route = $announcement->parent_id
            ? route('announcements.show', ['announcement' => $announcement->parent_id])
            : route('announcements.index');

        // also delete associated media files
        $announcement->clearMediaCollection('files');

        $announcement->delete();

        return $return_route;
    }

    public function create()
    {
        return view('announcements.create', ['parent_id' => null, 'title' => '']);
    }

   public function store(StoreAnnouncementRequest $request)
{
    try {
        return DB::transaction(function () use ($request) {
            // Prepare announcement data
            $data = $request->validated();
            $data['author'] = Auth::id();

            // Create announcement
            $announcement = Announcement::create($data);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    try {
                        $announcement->addMedia($file)->toMediaCollection('announcement_files');
                    } catch (\Exception $e) {
                        Log::error('File upload failed', [
                            'file' => $file->getClientOriginalName(),
                            'error' => $e->getMessage(),
                            'announcement_id' => $announcement->id,
                        ]);
                        throw new BusinessLogicException('Failed to upload file: ' . $file->getClientOriginalName());
                    }
                }
            }

            // Handle announcement_files (alternative file input)
            if ($request->hasFile('announcement_files')) {
                foreach ($request->file('announcement_files') as $file) {
                    try {
                        $announcement->addMedia($file)->toMediaCollection('announcement_files');
                    } catch (\Exception $e) {
                        Log::error('Announcement file upload failed', [
                            'file' => $file->getClientOriginalName(),
                            'error' => $e->getMessage(),
                            'announcement_id' => $announcement->id,
                        ]);
                        throw new BusinessLogicException('Failed to upload announcement file: ' . $file->getClientOriginalName());
                    }
                }
            }

            Log::info('Announcement created successfully', [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'author_id' => Auth::id(),
            ]);

            return redirect()->route('announcements.index')
                ->with('success', 'Announcement created successfully.');
        });

    } catch (BusinessLogicException $e) {
        return redirect()->back()
            ->withInput($request->except('files', 'announcement_files'))
            ->withErrors(['error' => $e->getMessage()]);

    } catch (\Exception $e) {
        Log::error('Failed to create announcement', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id(),
            'request_data' => $request->except('files', 'announcement_files'),
        ]);

        return redirect()->back()
            ->withInput($request->except('files', 'announcement_files'))
            ->withErrors(['error' => 'Failed to create announcement. Please try again.']);
    }
}

    public function reply($id, Request $request)
    {
        $parentAnnouncement = Announcement::findOrFail($request->parent_id);
        $mainParent = $this->getMainParent($parentAnnouncement);

        $newComment = Announcement::create([
            'title' => 'Re: ' . $parentAnnouncement->title,
            'parent_id' => $request->parent_id,
            'content' => $request->content ?? '',
            'author' => Auth::id()
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $newComment->addMedia($file)->toMediaCollection('files');
            }
        }

        $childs = Announcement::where('parent_id', $id)->get();
        $view = View::make('announcements.show_main', [
            'announcement' => $mainParent,
            'childs' => $childs
        ]);
        $content = $view->render();
        return response()->json(['content' => $content, 'announcement' => true]);
    }

    public function show($id, Request $request)
    {
        $announcement = Announcement::findOrFail($id);
        if (!$announcement) {
            return abort(404);
        }
        $mainParent = $this->getMainParent($announcement);

        return view('announcements.show_tree_view', compact('mainParent', 'announcement'));
    }

    public function getMainParent(Announcement $announcement)
    {
        if (!$announcement->parent_id) {
            return $announcement;
        } else {
            $parentAnnouncement = Announcement::findOrFail($announcement->parent_id);
            return $this->getMainParent($parentAnnouncement);
        }
    }

    public function edit($id, Request $request)
    {
        $title = 'Edit - announcement';
        $announcement = Announcement::findOrFail($id);
        if ($request->user()->id != $announcement->author) return back();

        if ($request->ajax()) {
            return URL::to('announcement/' . $id . '/edit');
        }

        return view('announcements.edit', compact('title', 'announcement'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'title'   => 'required|string',
            'content' => 'required|string'
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update($request->only(['title', 'content']));

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $announcement->addMedia($file)->toMediaCollection('files');
            }
        }

        return response()->json(['route' => route('announcements.show', ['id' => $id])]);
    }

    public function generateAnnouncements(Request $request, $id)
    {
        $current = Announcement::findOrfail($id);
        $announcement = $this->getMainParent($current);
        $childs = Announcement::where('parent_id', $id)->get();
        return view('announcements.show_main', ['announcement' => $announcement, 'childs' => $childs]);
    }
}
