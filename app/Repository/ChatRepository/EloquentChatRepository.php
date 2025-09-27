<?php

namespace App\Repository\ChatRepository;

use App\Chat;
use App\Repository\Contracts\ChatRepository;

class EloquentChatRepository implements ChatRepository {
	/**
	 * @inheritDoc
	 */
	public function getAll() {
		return Chat::all();
	}

	/**
	 * @inheritDoc
	 */
	public function showById( int $id ) {
		return Chat::findOrFail( $id );
	}

	/**
	 * @inheritDoc
	 */
	public function create( array $data ) {
		return Chat::create( $data );
	}

	/**
	 * @inheritDoc
	 */
	public function destroy( int $id ) {
		return Chat::destroy( $id );
	}

	/**
	 * @inheritDoc
	 */
	public function updateById( int $id, array $data ) {
		return Chat::findOrFail( $id )->update( $data );
	}

	/**
	 * @return mixed
	 */
	public function getMainChat() {
		return Chat::where( [ 'type' => Chat::CHAT_TYPE_ALL ] )->get();
	}

	public function getDirectChats(int $userId = null) {
		if(!$userId) {
			$userId = \Auth::id();
		}
		$chatsResult =  \DB::table( 'chat_user' )
		          ->select( 'chat_user.chat_id as id' )
		          ->leftJoin( 'chats', 'chat_user.chat_id', 'chats.id' )
		          ->distinct()
		          ->where( 'user_id', $userId )
		          ->where( 'chats.type', Chat::CHAT_TYPE_DIRECT )
		          ->groupBy( 'chat_id' )->get()->toArray();
		$chatsIds = [];
		array_walk($chatsResult, function($chatResult) use (&$chatsIds){
			$chatsIds[] = $chatResult->id;
		});
		$chats =  Chat::whereIn('id', $chatsIds)->get();
		foreach ($chats as &$chat) {
			if (count($chat->users) == 1) {
				unset($chat);
				break;
			}


			foreach ($chat->users as $user) {
				if ($user->id !== $userId) {
					$chat->title = $user->name;
					break;
				}
			}

		}

		return $chats;

	}

	public function getCustomChats( int $userId = null) {
		if(!$userId) {
			$userId = \Auth::id();
		}
		$chatsResult =  \DB::table( 'chat_user' )
		                   ->select( 'chat_user.chat_id as id' )
		                   ->leftJoin( 'chats', 'chat_user.chat_id', 'chats.id' )
		                   ->distinct()
		                   ->where( 'user_id', $userId )
		                   ->where( 'chats.type', Chat::CHAT_TYPE_CUSTOM )
		                   ->groupBy( 'chat_id' )->get()->toArray();
		$chatsIds = [];
		array_walk($chatsResult, function($chatResult) use (&$chatsIds){
			$chatsIds[] = $chatResult->id;
		});
		return Chat::whereIn('id', $chatsIds)->get();
	}

}
