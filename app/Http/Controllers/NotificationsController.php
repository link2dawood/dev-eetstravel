<?php

namespace App\Http\Controllers;

use App\Notification;
use App\NotificationUser;
use Auth;
use Illuminate\Http\Request;
use URL;
use View;

class NotificationsController extends Controller
{
    public function deleteNotification(Request $request){
        $id_notification = $request->get('id', null);

        if($id_notification){
            $user = Auth::user();
            $user->notifications()->detach($id_notification);

            $relation = NotificationUser::query()->where('notification_id', $id_notification)->get();
            if($relation->isEmpty()){
                Notification::query()->where('id', $id_notification)->delete();
            }
        }else{
            return response()->json(false);
        }

        return response()->json(true);
    }

    public function readAllNotifications(){
        $user = Auth::user();

        if($user){
            $notifications = $user->notifications;
            foreach ($notifications as $notification){
                $notification->click = true;
                $notification->save();
            }
        }else{
            return response()->json(false);
        }

        return response()->json(true);
    }

    public function deleteAllNotifications(){
        $user = Auth::user();

        if($user){
            $notifications = $user->notifications;
            foreach ($notifications as $notification){
                $user->notifications()->detach($notification->id);

                $relation = NotificationUser::query()->where('notification_id', $notification->id)->get();
                if($relation->isEmpty()){
                    Notification::query()->where('id', $notification->id)->delete();
                }
            }
        }else{
            return response()->json(false);
        }

        return response()->json(true);
    }

    public function getNotifications(Request $request){
        $notifications = Auth::user()->notifications->sortByDesc('created_at');
        $user = Auth::user();

        $view = View::make(
            'component.list-notification-task',
            [
                'notifications'   => $notifications,
                'user'   => $user,
            ]
        );

        $contents = $view->render();

        return $contents;
    }

    /**
     * Get formatted notifications data for direct display (without DataTables)
     */
    public function getNotificationsData()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->get();

        $formattedNotifications = [];
        foreach ($notifications as $notification) {
            $formattedNotification = [
                'id' => $notification->id,
                'content' => $notification->content,
                'action_buttons' => $this->getButtonForNotifications($notification),
                'link' => $notification->link,
                'created_at' => $notification->created_at ? $notification->created_at->format('Y-m-d H:i:s') : '',
            ];
            $formattedNotifications[] = $formattedNotification;
        }

        return $formattedNotifications;
    }

    /**
     * Show notifications page using direct data (demonstration of refactored approach)
     */
    public function showNotifications()
    {
        $user = Auth::user();
        $notificationsData = $this->getNotificationsData();

        return view('notifications.index', [
            'userName' => $user->name,
            'userId' => $user->id,
            'notificationsData' => $notificationsData
        ]);
    }

    /**
     * Generate action buttons for notifications
     */
    private function getButtonForNotifications($notification)
    {
        return view('component.action-button', [
            'model' => $notification,
            'item' => $notification,
            'routePrefix' => 'notifications'
        ])->render();
    }

    /**
     * This method is no longer needed, but keeping for backward compatibility
     * The deleteMsg route can be removed from web.php
     */
    public function deleteMsg($id, Request $request)
    {
        // Redirect to destroy method
        return $this->destroy($id);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::findOrFail($id);
            
            // Detach from current user
            $user->notifications()->detach($id);
            
            // Check if notification has no more users attached
            $relation = NotificationUser::where('notification_id', $id)->count();
            if ($relation == 0) {
                $notification->delete();
            }
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => trans('main.DeletedSuccessfully') ?? 'Deleted successfully'
                ]);
            }
            
            return redirect()->back()->with('success', trans('main.DeletedSuccessfully') ?? 'Deleted successfully');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting notification: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error deleting notification'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error deleting notification');
        }
    }
}