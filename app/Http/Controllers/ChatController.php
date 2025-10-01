<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Chat;
use App\ChatMessage;

use App\Helper\PermissionHelper;
use App\Repository\Contracts\ChatRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller {
	public $pusher;
	public $user;
	public $chatChannel;
	public $chatRepository;

	const DEFAULT_CHAT_CHANNEL = 'chat';

	public function __construct( ChatRepository $chatRepository ) {
        $this->middleware('permissions.required');
        $this->pusher         = App::make( 'pusher' );
        $this->user           = Session::get( 'user' );
        $this->chatChannel    = self::DEFAULT_CHAT_CHANNEL;
        $this->chatRepository = $chatRepository;
	}

	public function index() {
		// Get all chats data (same as the AJAX data method)
		$chatsData = Chat::distinct()
			->leftJoin( 'users', 'users.id', '=', 'chats.author' )
			->select(
				'chats.id as id',
				'chats.title as title',
				'chats.description as description',
				'chats.type as type',
				'users.name as author'
			)
			->get();

		// Add action buttons to each chat
		$chatsData->each(function ($chat) {
			$chat->action_buttons = $this->getActionButtons($chat->id);
		});

		return view( 'chats.index', compact('chatsData') );
	}

	public function getActionButtons( $id ) {
		$url = array(
			'show'       => route( 'chat.show', [ 'id' => $id ] ),
			'edit'       => route( 'chat.edit', [ 'id' => $id ] ),
			'delete_msg' => "/chat/{$id}/deleteMsg"
		);

		return DatatablesHelperController::getActionButton( $url );
	}



	public function getIndex() {
		$chats = Chat::all();

		return view( 'chat', [ 'chatChannel' => $this->chatChannel ] );
	}

	public function postMessage( Request $request ) {
		$message = $this->saveMessage( $request->all() );
		if ($message) {
			$message = [
				'message' => $message->id,
				'chat'    => $message->chat_id
			];
			$this->pusher->trigger( $this->chatChannel, 'new-message', $message );
		}
	}

	public function saveMessage( array $data ) {
		if($data && !is_null($data['message'])){
		$chat = Chat::findOrFail($data['chat']);
		if(!Chat::isUserHasAccess($chat)) {
			return '';
		}
		$message = [
			'message' => $data['message'],
			'chat_id' => $data['chat'],
			'user_id' => \Auth::id()
		];

		return ChatMessage::create( $message );
		}
	}

	public function getMessage( $id, Request $request ) {
		$message = ChatMessage::findOrFail( $id );
		if(!Chat::isUserHasAccess($message->chat)) {
			return '';
		}
			return ['chat_id' => $message->chat->id, 'data' => view( 'chats.component.message', compact( 'message' ))->render()] ;

	}

	public function show( $id, Request $request ) {
		$chat = Chat::findOrFail( $id );

        if($chat == null){
            return abort(404);
        }
		return view( 'chats.show', compact( 'chat' ) );
	}

	public function main() {
		$chats = $this->getEnabledChats();
        
		return view( 'chats.main', compact( 'chats' ) );
	}

	public function renderChat( $id, Request $request ) {
		$chat = Chat::findOrFail( $id );
		$dashboard = $request->dashboard;
		return view( 'chats.component.chat', compact( 'chat', 'dashboard') );
	}

	public function renderCustomChatCreateFrom(Request $request)
	{
		return view('chats.component.create_custom_chat');
	}
    
	public function renderCustomChatDeleteFrom(Request $request)
	{
		return view('chats.component.deletechat');
	}

	public function createCustomChat(Request $request)
	{
		$chatName = $request->get('chatName');
		$chatDescription = $request->get('chatDescription');
            $newChat = Chat::create(
                [
                    'title' => $chatName,
                    'description' => $chatDescription,
                    'type'  => Chat::CHAT_TYPE_CUSTOM,
                    'author'    => \Auth::id()

                ]
            );
		$newChat->users()->sync([\Auth::id()]);
		return ['id' => $newChat->id, 'name' => $newChat->title];
	}

	public function renderUsersForChat() {
		$users = User::all()->except(\Auth::id());
		foreach ( $users as $user ) {
			$chats = \DB::table( 'chat_user' )
			            ->select( "*" )
			            ->distinct()
			            ->where( 'user_id', $user->id );
		}

		return view( 'chats.component.direct_chat_users', compact( 'users' ) );
	}

	public function getOrCreateChat( Request $request ) {
		$userId        = $request->get( 'userId' );
		$user = User::findOrFail($userId);
		$chat = $this->getOrCreateDirectChat($userId);

		return [ 'id' => $chat->id, 'name' => $user->name ];
    }

	public function deleteChat(Request $request ) {
        $id = $request->id;
        $chat = Chat::find($id);
        $chat->users()->sync( [] );
        $chat = Chat::destroy($id);
		
		return 'Success';
	}

	public function getDirectChat( $firstUser, $secondUser = null ) {
		if ( ! $secondUser ) {
			$secondUser = \Auth::id();
		}
		if ( $firstUser == $secondUser ) {
			return null;
		}

		$existedChat = \DB::table( 'chat_user' )
		                  ->select( '*' )
		                  ->leftJoin( 'chats', 'chat_user.chat_id', 'chats.id' )
		                  ->distinct()
		                  ->whereIn( 'user_id', [ $firstUser, $secondUser ] )
		                  ->where( 'chats.type', Chat::CHAT_TYPE_DIRECT )
		                  ->groupBy( 'chat_id' )
		                  ->havingRaw( 'COUNT(user_id) = 2' )->get()->first();

		return $existedChat;
	}

	/**
	 *
	 */
	public function getEnabledChats() {
		/** @var Collection $chats */
		$chats = $this->chatRepository->getMainChat();
//	    dd($chats);
		$directChats = $this->chatRepository->getDirectChats();
		if ($directChats) {
			$chats       = $chats->merge( $directChats );
		}
		$customChats = $this->chatRepository->getCustomChats();
		if ($customChats) {
			$chats = $chats->merge($customChats);
		}

		return $chats;
	}

	public function renderUsersForCustomChat(int $id, Request $request)
	{
		$chatId = $id;
		$chat = Chat::findOrFail( $id );
		$chatUsers = $chat->users->pluck('id')->toArray();

		$users = User::all()->except($chatUsers);
		return view( 'chats.component.custom_chat_users', compact( 'users', 'chatId') );
	}

	public function addUserToCustomChat(Request $request)
	{
		$userId = $request->get( 'userId' );
		$chatId = $request->get( 'chatId' );
		$chat = Chat::findOrFail($chatId);
		$user = User::findOrFail($userId);
		if ($chat) {
			$chat->assignUser($userId);
		}
		$message = ChatMessage::create(
			[
				'chat_id'   => $chatId,
				'user_id'   => \Auth::id(),
				'message'   => sprintf("User %s added to chat" , $user->name)
			]
		);

		$message = [
			'message' => $message->id,
			'chat'    => $message->chat_id
		];
		$this->pusher->trigger( $this->chatChannel, 'new-message', $message );
		$message = [
			'chat'  => $chatId,
			'user'  => $userId
		];
		$this->pusher->trigger( $this->chatChannel, 'new-chat', $message );
	}

	public function removeUserFromChat(Request $request)
	{
		$userId = $request->get( 'userId' );
		$chatId = $request->get( 'chatId' );
		$chat = Chat::findOrFail($chatId);

		$user = User::findOrFail($userId);
		$chat->removeUser($userId);
		$message = ChatMessage::create(
			[
				'chat_id'   => $chatId,
				'user_id'   => \Auth::id(),
				'message'   => sprintf("User %s removed from chat" , $user->name)
			]
		);

		$message = [
			'message' => $message->id,
			'chat'    => $message->chat_id
		];
		$this->pusher->trigger( $this->chatChannel, 'new-message', $message );
	}

	public function getNewChat(int $id, Request $request)
	{
		$chat = Chat::findOrFail($id);
		$userId = $request->get( 'userId' );
		$user = User::findOrFail($userId);
		if ($userId == \Auth::id()) {
			return "<li><a href=\"#\" data-id=\"$chat->id\" class=\"chat-title\">
                                        $chat->title</a></li>";
		}
		return '';
	}

	public function renderDirectChat(int $id, Request $request)
	{
		$userId = $id;
		$chat = $this->getOrCreateDirectChat($userId);
		$chat = Chat::findOrFail($chat->id);
		$dashboard = $request->dashboard;
		return view('chats.component.chat', compact('chat', 'dashboard'));
	}

	public function getOrCreateDirectChat($firstUser, $secondUser = null)
	{
		$user = User::where( 'id', $firstUser )->first();
		if (!$secondUser) {
			$currentUserId = \Auth::id();
			$secondUser = $currentUserId;
		}
		$existedChat   = $this->getDirectChat( $firstUser );
		if ( ! $existedChat ) {
			$newChat = Chat::create(
				[
					'title'       => 'Direct Chat',
					'description' => 'Direct Chat',
					'type'        => Chat::CHAT_TYPE_DIRECT
				]
			);
			$newChat->users()->sync( [ $firstUser, $secondUser ] );

			return $newChat;
		} else {
			return $existedChat;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id, Request $request)
	{
		$chat = Chat::find($id);
		if ($chat->type == Chat::CHAT_TYPE_CUSTOM) {
			$chat = Chat::destroy($id);
		}
        
		return route('chat.main');
	}

	public function delete($id, Request $request)
	{
//		$msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove this chat?', route('chat.destroy_chat', ['id' => $id], false));
		$msg = Ajaxis::BtDeleting('Warning!!', trans('main.Warning').'!!',trans('main.Wouldyouliketoremovethischat').'?', route('chat.destroy_chat', ['id' => $id], false));
		if ($request->ajax()) {
			return $msg;
		}
	}

    public function getChatNotifications(Request $request)
    {
        $chatUsers = User::all()->except(\Auth::id());
        $chatMessages = [];
        $mainMessages = 0;
        $sendUser = ($request->user)?$request->user:false;
//        $logs = Activity::orderBy('created_at', 'desc')->simplePaginate(5);
        $mydt = date('Y-m-d H:i:s', strtotime('-20 seconds'));
        $mainChat = ChatMessage::where('chat_id',1)->where('created_at', ">", $mydt)->get();

        if($mainChat) {
            $mainMessages = count($mainChat);
        }
        foreach ($chatUsers as $user) {

            $existedChat = \DB::table('chat_user')
                ->select('*')
                ->leftJoin('chats', 'chat_user.chat_id', 'chats.id')
                ->distinct()
                ->whereIn('user_id', [$user->id, \Auth::id()])
                ->where('chats.type', Chat::CHAT_TYPE_DIRECT)
                ->groupBy('chat_id')
                ->havingRaw('COUNT(user_id) = 2')->get()->first();

//            dd($existedChat = \DB::table('chat_user')
//                ->select('*')
//                ->leftJoin('chats', 'chat_user.chat_id', 'chats.id')
//                ->distinct()
//                ->whereIn('user_id', [$user->id, \Auth::id()])
//                ->where('chats.type', Chat::CHAT_TYPE_DIRECT)
//                ->groupBy('chat_id')
//                ->havingRaw('COUNT(user_id) = 2')->get()->first());
            $chat_by_user = ($existedChat) ? Chat::where('id', $existedChat->chat_id)->first() : null;



            if ($chat_by_user) {
//                dump($chat_by_user->toArray());
                //$mydt = date('Y-m-d H:i:s', strtotime('10 seconds ago'));
                $mess = ChatMessage::where('chat_id', $chat_by_user->id)->where('user_id','!=', \Auth::id())->where('created_at', ">", $mydt)->get();
                $chatMessages[] = count($mess);
            } else {
                $chatMessages[] = 0;
            }
        }
//        dd($chatUsers->toArray(), $user->id, \Auth::id(),$chatMessages);
//        dd($mainMessages);

        return json_encode(['direct_chats' => $chatMessages,'main_chat' => $mainMessages, 'sendUser'=>$sendUser]);
    }
}