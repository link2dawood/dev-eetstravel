<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
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
                'action_buttons' => $this->getButtonForNotifications($notification->id, $notification->link, $notification),
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
    private function getButtonForNotifications($id, $link, $notification)
    {
        $url = array(
            'show' => $link,
            'delete_msg' => '/notifications/'.$id.'/deleteMsg'
        );

        return \App\Http\Controllers\DatatablesHelperController::getActionButton($url, false, $notification);
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/notifications/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/notifications/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }


    public function destroy($id)
    {
        Notification::query()->where('id', $id)->delete();
        return URL::to('profile');
    }
}
