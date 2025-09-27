<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Console\Commands\ParseEmails;
use App\Email;
use App\EmailFolder;
use App\User;

use App\Helper\ImapService;
use App\Helper\LaravelFlashSessionHelper;
use App\Jobs\ParseEmail;
use App\ParseRequest;
use App\Repository\Contracts\EmailRepository;
use Auth;
use Ddeboer\Imap\Exception\AuthenticationFailedException;
use Ddeboer\Imap\Message;
use Ddeboer\Imap\Search\State\NewMessage;
use Ddeboer\Imap\Server;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Redirect;
use Response;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Exception\Exception;


class EmailController extends Controller {
	public $client;

	public $folders;

	public $inbox;

	public $use;

	/** @var EmailRepository $emailRepository */
	public $emailRepository;

	public $imapConnected = false;
	const INBOX_FOLDER = 'INBOX';
	const TRASH_FOLDER = 'INBOX.Trash';
	const PER_PAGE_MAILS_COUNT = 15;
	/**
	 * @var ParseEmails
	 */
	private $parseEmails;

	private $emailServer;
	/**
	 * EmailController constructor.
	 *
	 * @param EmailRepository $emailRepository
	 * @param ParseEmails $parseEmails
	 */
	public function __construct( EmailRepository $emailRepository, ParseEmails $parseEmails ) {

        $this->middleware('preventBackHistory');
        $this->middleware('auth');
      //  $this->middleware('VueParse');

		$this->emailRepository = $emailRepository;
//		$this->middleware( function ( $request, $next ) {
//			$this->user = Auth::user();
//
//			try {
//				$this->folders       = EmailFolder::where(
//					'user_login', Auth::user()->email_login
//				)
//				->where( 'email_server', Auth::user()->email_server )
//				->get();
//
//				$this->imapConnected = true;
//				$this->emailServer = Auth::user()->email_server;
//			} catch ( \Exception $e ) {
//
//			}
//
//			return $next( $request );
//		} );


		$this->parseEmails = $parseEmails;
	}

	public function parseEmails( Request $request ) {

		if ( ! is_numeric( $request->period ) ) {
			return [ 'status' => 'error', 'message' => 'Wrong format' ];
		}

		$parseRequest              = new ParseRequest();
		$parseRequest->user        = Auth::user()->email_login;
		$parseRequest->period      = $request->period;
		$parseRequest->period_type = $request->period_type;
		$parseRequest->save();

		return '';
	}

	public function setClient() {
		$emailServer = Auth::user()->email_server;
		try {
			
				$server = new Server(
					env( 'IMAP_HOST', 'localhost' ),
					env( 'IMAP_PORT' ),
					'/imap/tls/novalidate-cert'
				);
			
            

			$this->client = $server->authenticate(
				"booking-test@eetstravel.com",
				"Ffans82guQ9h"
			);
		} catch (AuthenticationFailedException $e) {
			return false;
		}



		return true;


	}

	/**
	 * Show the profile for the given user.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function index( $page = 1 ) {

		$mails = [];/*\DB::table( 'emails' )
		            ->where( 'folder', '=', 'INBOX' )
		            ->where( 'user_login', Auth::user()->email_login )
		            ->where( 'email_server', Auth::user()->email_server )
		            ->orderBy( 'date', 'DESC' )
		            ->paginate( self::PER_PAGE_MAILS_COUNT );*/
//        $this->setClient();
//        $mailboxes = $this->client->getMailboxes();
//		foreach ($mailboxes as $mailb){
//            foreach ($mailb->getMessages()  as $mail){
//                dd($mail->getFrom());
//            }
//            dd($mailb->getAttributes(),$mailb->getName(), $mailb->count(),$mailb->getMessages());
//        }
	
		$emails = Email::where( 'to',Auth::user()->email )->get();

		$sentemails = Email::where( 'user_login', Auth::user()->email )->get();

		
		return view( 'email.email_index', [
//			'folderName'    => 'INBOX',
//			'folders'       => $this->folders,
//			'mails'         => $mails,
//			'page'          => $page,
//			'per_page'      => self::PER_PAGE_MAILS_COUNT,
//			'mailsCount'    => count( $mails ),
//			'currentFolder' => 'INBOX',
			'imapConnected' => true,//$this->imapConnected,
			'sentemails'         => $sentemails,
			'emails'         => $emails,
			
		] );
	}

	public function folder( $name, $page = 1 ) {
		$mails = \DB::table( 'emails' )->where( 'folder', '=', $name )
		            ->orderBy( 'message_id', 'DESC' )
		            ->where( 'user_login', Auth::user()->email_login )
					->where( 'email_server', Auth::user()->email_server )
		            ->paginate( self::PER_PAGE_MAILS_COUNT );

		return view( 'email.index', [
			'folders'       => $this->folders,
			'mails'         => $mails,
			'page'          => $page,
			'per_page'      => self::PER_PAGE_MAILS_COUNT,
			'mailsCount'    => count( $mails ),
			'folderName'    => $this->folders,
			'currentFolder' => $name,
			'imapConnected' => $this->imapConnected
		] );
	}

	public function mail(Request $request, $id, $currentFolder = self::INBOX_FOLDER ) {
	    $builder = \DB::table( 'emails' )
            ->select('*')
            ->where( 'folder', '=', $currentFolder )
            ->where( 'message_id', $id )
            ->where( 'user_login', Auth::user()->email_login );

		$mail = $mails = $builder->first();
		if(!$mail) return abort(404);
//		if(!$mail->is_seen){
//            $builder->update(['is_seen' => true]);
//        }

		return view( 'email.mail', [
			'mail'          => $mail,
			'folders'       => $this->folders,
			'currentFolder' => $currentFolder,
			'imapConnected' => $this->imapConnected
		] );
	}


    public function ajaxMail($id, $currentFolder = self::INBOX_FOLDER ) {
        $builder = \DB::table( 'emails' )
            ->select('*')
            ->where( 'folder', '=', $currentFolder )
            ->where( 'message_id', $id )
            ->where( 'user_login', Auth::user()->email_login );

        $mail = $mails = $builder->first();

        $view           =   View::make(
            'email.ajax_mail_template',
            [
                'mail'          => $mail,
                'folders'       => $this->folders,
                'currentFolder' => $currentFolder,
                'imapConnected' => $this->imapConnected
            ]
        );
        $contents = $view->render();

        return $contents;
    }

    public function readAll(){
	    Email::query()
            ->where('is_seen', '!=', true)
            ->where( 'user_login', Auth::user()->email_login )
            ->where( 'email_server', Auth::user()->email_server )->update(['is_seen' => true]);

	    return redirect('home');
    }


	public function getFolderByName( $folderName ) {
		$mailbox = new ImapService( $folderName );
		$folders = $this->client->getFolders();
		foreach ( $folders as $folder ) {
			if ( $folder->has_children ) {
				foreach ( $folder->children as $childFolder ) {
					if ( $childFolder->fullName == $folderName ) {
						return $childFolder;
					}
				}
			}
		}

		return false;
	}

	public function attachment( $folderName, $id, $attachmentName ) {
		$this->setClient();
		$mail = $this->client->getMailbox( $folderName )->getMessage( $id );

		$attachments = $mail->getAttachments();
		$attachment  = array_filter( $attachments, function ( $item ) use ( $attachmentName ) {
			if ( preg_replace( "/[^a-zA-Z]/", "", $item->getFilename() ) == $attachmentName ) {
				return $item;
			}
		} );
		reset( $attachment );
		if ( $attachment ) {
			$response = Response::make( current( $attachment )->getDecodedContent(), 200 );
			$response->header( 'Content-Disposition', 'attachment; filename="' . current( $attachment )->getFilename() . '"' );

			return $response;
		}
	}

	public function send( Request $request ) {
	
		if(!$this->setClient()) {
			LaravelFlashSessionHelper::setFlashMessage('Authorization failed. Set correct credentials in profile', 'error');
			return Redirect::route( 'email.index' );
		}

		$content   = $request->input( 'content' );
		$to        = $request->input( 'to' );
		$subject   = $request->input( 'subject' );
		$userLogin = $this->user->email_login;
		$serverEmail = $this->user->email_server;
		$folder_sent = 'INBOX.Sent';
		try{
            $this->client->getMailbox( $folder_sent );
        }catch(\Exception $exception){
            $this->client->createMailbox( $folder_sent );
        }
		/** Illuminate\Mail\Message $message */
		Mail::send( 'email.mail_template', compact( 'content' ), function ( $message ) use ( $request, $to, $subject, $folder_sent ) {

		    $fio = Auth::user();
		    $client_name = $fio ? $fio->name : 'EETS Travel';

			$toArray   = explode( ',', $to );
			$emails_to = [];
			foreach ( $toArray as $item ) {
				$emails_to[] = trim( $item );
			}
			$message->to( $emails_to );
			$message->from( Auth::user()->email_login, $client_name );
			$message->sender( Auth::user()->email_login, $client_name );
			if ( $request->file( 'attachment' ) ) {
				foreach ( $request->file( 'attachment' ) as $attachment ) {
					$message->attach( $attachment->getRealPath(), [
						'as'   => $attachment->getClientOriginalName(),
						'mime' => $attachment->getMimeType()
					] );
				}
			}
			$message->subject( $subject );
			$sendedMailbox = $this->client->getMailbox( $folder_sent );
			$sendedMailbox->addMessage( $message->getSwiftMessage()->toString() );

		} );

		$mailBox = $this->client->getMailbox( $folder_sent );
		$search  = new SearchExpression();
		$search->addCondition( new NewMessage() );

		$messages = $mailBox->getMessages( $search );

		$emailId = array();

		foreach ( $messages as $email ) {
			$emailId[] = $email->getNumber();
		}

		$emailExists = Email::query()->where( 'user_login', '=', $userLogin )
		                    ->where( 'folder', '=', $mailBox->getName() )
		                    ->whereIn( 'message_id', $emailId )
		                    ->get();

		if ( $emailExists->isEmpty() ) {
			foreach ( $messages as $email ) {
				$message                  = new Email();
				$message->user_login      = $userLogin;
				$message->message_id      = $email->getNumber();
				$message->folder          = $mailBox->getName();
				$message->date            = $email->getDate();
				$message->body_text       = $email->getBodyText();
				$message->body_html       = $email->getBodyHtml();
				$message->date            = $email->getDate();
				$message->subject         = $email->getSubject();
				$message->to              = $this->parseEmails->getTo( $email->getTo() );
				$message->content         = $email->getContent();
				$message->from            = $email->getFrom()->getAddress();
				$message->has_attachments = $email->hasAttachments();
				$message->mail_id         = $email->getId();
				$message->size            = $email->getSize();
				$message->is_answered     = $email->isAnswered();
				$message->is_deleted      = $email->isDeleted();
				$message->is_draft        = $email->isDraft();
				$message->is_seen         = $email->isSeen();
				$message->email_server    = $serverEmail;
				$message->save();
			}
		}

      // LaravelFlashSessionHelper::setFlashMessage("Email sended to $message->to", 'success');

        $data = ['result' => 'Email sended to '.$message->to ];

        return response()->json($data);
	}

	public function composeEmailTemplate( \Request $request ) {
		return view( 'email.mail_template' );
	}

	public function emailDeleteMsg( $id, $currentFolder = self::INBOX_FOLDER, Request $request ) {
		$msg = Ajaxis::BtDeleting( 'Warning!!', 'Would you like to remove this email?', route( 'email.remove', [ 'id'            => $id,
		                                                                                                         'currentFolder' => $currentFolder
		], false ) );

		if ( $request->ajax() ) {
			return $msg;
		}
	}

	public function removeEmail($id, $currentFolder = self::INBOX_FOLDER ) {
		if(!$this->setClient()) {
			LaravelFlashSessionHelper::setFlashMessage('Authorization failed. Set correct credentials in profile', 'error');
			return route('email.index');
		}

        try{
            $this->client->getMailbox( self::TRASH_FOLDER );
        }catch(\Exception $exception){
            $this->client->createMailbox( self::TRASH_FOLDER );
        }
        if(URL::previous() == route('email.search')){
            $mail = Email::query()->where('message_id', $id)->first();
            $currentFolder = $mail ? $mail->folder : $currentFolder;
        }
        $mail        = $this->client->getMailbox($currentFolder)->getMessage($id);
        $mail_to = $this->parseEmails->getTo( $mail->getTo() );
		$trashFolder = $this->client->getMailbox( self::TRASH_FOLDER );
		$mail->move( $trashFolder );
//		$folder = $this->client->getMailbox($currentFolder);
		$this->client->expunge();
		$localMail = $mails = \DB::table( 'emails' )
		                         ->where( 'folder', '=', $currentFolder )
		                         ->where( 'message_id', $id )
		                         ->where( 'user_login', Auth::user()->email_login )
								 ->where( 'email_server', Auth::user()->email_server )
		                         ->first();
		if ( $localMail ) {
			\DB::table( 'emails' )
			   ->where( 'id', $localMail->id )->delete();
		}

        LaravelFlashSessionHelper::setFlashMessage("Email {$mail_to} deleted", 'success');

        if(URL::previous() == route('email.search')){
            return [
                'email_search' => true
            ];
        }

		return route( 'email.folder', [ 'name' => $currentFolder ] );

	}

	public function moveEmail( Request $request ) {
		if(!$this->setClient()) {
			LaravelFlashSessionHelper::setFlashMessage('Authorization failed. Set correct credentials in profile', 'error');
			return Redirect::route( 'email.index' );
		}

		$id            = $request->get( 'messageId' );
		$currentFolder = $request->get( 'messageFolder' );

		$destinationFolderName = $request->get( 'folder' );
		/** @var Message $mail */

        if(URL::previous() == route('email.search')){
            $mail = Email::query()->where('message_id', $id)->first();
            $currentFolder = $mail ? $mail->folder : $currentFolder;
        }
        $mail              = $this->client->getMailbox( $currentFolder )->getMessage( $id );
        $destinationFolder = $this->client->getMailbox( $destinationFolderName );
		$mail->move( $destinationFolder );
		$localMail = $mails = \DB::table( 'emails' )
		                         ->where( 'folder', '=', $currentFolder )
		                         ->where( 'message_id', $id )
		                         ->where( 'user_login', Auth::user()->email_login )
			->where( 'email_server', Auth::user()->email_server )
		                         ->first();
		if ( $localMail ) {
			\DB::table( 'emails' )
			   ->where( 'id', $localMail->id )->delete();
		}
		LaravelFlashSessionHelper::setFlashMessage(
			sprintf( 'Message #%s moved from folder %s to %s. Moved message will appear soon', $id, $currentFolder, $destinationFolderName )
		);

		$this->client->expunge();


		return Redirect::route( 'email.folder', [ 'name' => $currentFolder ] );
	}

	public function getComposeForm( Request $request ) {
		$replyMessageId = $request->get( 'reply_message' );
		$replyFolder    = $request->get( 'reply_folder' );
		$replyTo        = $request->get( 'reply_to' );
		$replyMessage   = '';
		if ( $replyMessageId ) {
			$replyFolder = $replyFolder ?? self::INBOX_FOLDER;
			$mail        = $mails = \DB::table( 'emails' )
			                           ->where( 'folder', '=', $replyFolder )
			                           ->where( 'message_id', $replyMessageId )
			                           ->where( 'user_login', Auth::user()->email_login )
										->where( 'email_server', Auth::user()->email_server )
			                           ->first();
			if ( $mail ) {
				$replyMessage = $mail->body_html;
			}

		}

		return view( 'email.compose_template', compact( 'replyMessage', 'replyTo', 'mail' ) );
	}

	public function getMoveToForm( Request $request ) {
		$messageId     = $request->get( 'message_id' );
		$messageFolder = $request->get( 'message_folder' );

		return view( 'email.move_to_form', [
			'folders'       => $this->folders,
			'messageId'     => $messageId,
			'messageFolder' => $messageFolder
		] );
	}

	public function addFolderForm( Request $request ) {
		return view( 'email.add_folder_form' );
	}

	public function addFolder( Request $request ) {
		$this->setClient();
		$folderName = $request->input( 'folderName' );
		$this->client->createMailbox( 'INBOX.' . $folderName );

		LaravelFlashSessionHelper::setFlashMessage(
			sprintf( 'Folder #%s created. New folder will appear soon ', $folderName )
		);

		return Redirect::route( 'email.index' );

	}

	public function emailSearch( Request $request ) {
		( $request->input( 'search' ) ) ? $search_value = $request->input( 'search' ) : $search_value = $request->input( 'searched' );
		( $request->input( 'page' ) ) ? $page = $request->input( 'page' ) : $page = 1;

		$mails = \DB::table( 'emails' )
		            ->where( 'user_login', Auth::user()->email_login )
					->where( 'email_server', Auth::user()->email_server )
		            ->where( function ( $query ) use ( $search_value ) {
			            $query->where( 'subject', 'like', "%$search_value%" )
			                  ->orWhere( 'body_text', 'like', "%$search_value%" )
			                  ->orWhere( 'to', 'like', "%$search_value%" )
			                  ->orWhere( 'from', 'like', "%$search_value%" );
		            } )
		            ->orderBy( 'date', 'DESC' )
		            ->paginate( self::PER_PAGE_MAILS_COUNT );

		return view( 'email.index', [
			'folderName'    => 'INBOX',
			'folders'       => $this->folders,
			'mails'         => $mails,
			'page'          => $page,
			'search'        => $search_value,
			'per_page'      => self::PER_PAGE_MAILS_COUNT,
			'mailsCount'    => count( $mails ),
			'currentFolder' => 'INBOX',
			'imapConnected' => $this->imapConnected

		] );

	}

	public function attachmentList( Request $request, $folderName, $id ) {

		$this->setClient();
		//		$server = new Server(
//					env('IMAP_HOST', 'localhost'),
//					env('IMAP_PORT'),
//					'/imap/tls/novalidate-cert'
//				);
//
//				$this->client = $server->authenticate(
//					(string)$this->user->email_login,
//					(string)$this->user->email_password
//				);
		$mail = null;
		if ( $this->client ) {
			/** @var Message $mail */
			$mail = $this->client->getMailbox( $folderName )->getMessage( $id );

			return view( 'email.attachments', [ 'mail' => $mail, 'currentFolder' => $folderName ] );
		}
	}

	public function another( Request $request ) {
		$server = new Server(
			"partner.outlook.cn",
			993,
			'/imap/ssl/validate-cert'
		);
		$client = $server->authenticate(
			"sarah@eets.cn",
			"Qx901102"
		);
		dd( $client->getMailboxes() );

		return '';
	}
	public function getEmailById($id,$mailtype){
	try{
		$email = Email::find($id);
		$username = "Outside User";
		if($mailtype == "inbox"){
		$users = User::where("email",$email->from)->get();
			foreach($users as $user){
				$username = $user->name;
			}
		}
		else{
			$users = User::where("email",$email->to)->get();
			foreach($users as $user){
				$username = $user->name;
			}
		}
		return array("email"=>$email,"users"=>$username);
		}
		catch (\Exception $e) {
            return $e;
        }
	}
	public function sendingEmail($userId, Request $request)
    {
        try {
            $user = User::findOrFail($userId);
            $auth_user=Auth::getUser();

            // Create PhpImap\Mailbox instance for all further actions
            //$this->setConnectionToServer($userId);
            Email::create([
                "message_id"=>"",
                "folder"=>"",
                "user_login"=>$auth_user->email,
                "subject"=>$request->subject,
                "content"=>$request->message,
                "attachments"=>"",
                "body_text"=> $request->message,
                "body_html"=> $request->message,
				"date"=>  date('Y-m-d H:i:s'),
                "from"=>$auth_user->email,
                "to"=>$request->to,
                "number"=>"",
                "message_number"=>"",
                "email_server"=>"",

            ]);
            $email = $request->to;
            $content = $request->message;
            $mail = Mail::send('email.mail_template', compact('content'), function ($message) use ($request, $user, $email) {

                $client_name = $user ? $user->name : 'EETS Travel';

                $toArray   = explode(',', $request->to);
                $emails_to = [];

                foreach ($toArray as $item) {
                    $emails_to[] = trim($item);
                }

                $message->to($emails_to);

                $message->from($user->email, $client_name);
                $message->sender($user->email, $client_name);

                if ($request->file('files')) {
                    foreach ($request->file('files') as $attachment) {
                        $message->attach($attachment->getRealPath(), [
                            'as'   => $attachment->getClientOriginalName(),
                            'mime' => $attachment->getMimeType()
                        ]);
                    }
                }
                $message->subject($request->subject);

                // $server->saveMessageInSent( $message->getSwiftMessage()->toString() );
                return $message;
            });
          
            //$this ->setConnectionToServer($userId);
        } catch (\Exception $e) {
            return $e;
        }
    }
	public function  deleteEmail($id){
		$delete_email = Email::find($id)->delete();
		return Redirect::route( 'email.index' );
	}
	public function replyEmail($userId,Request $request){
        try{
        $user = User::findOrFail($userId);
        $email = $request->email_sent;
       
        $content = $request->body;
        $mail = Mail::send( 'email.mail_template', compact( 'content' ), function ( $message) use ( $request, $user,$email ) {

            $client_name = $user ? $user->name : 'EETS Travel';
			
            $toArray   = explode( ',', $request->email_sent );
            $emails_to = [];
			
            foreach ( $toArray as $item ) {
                $emails_to[] = trim( $item );
            }
			$emails_to = $request->email_sent;
            $emails_to = "muhammadmustufa555@gmail.com";
            $message->to($emails_to);
            $message->from($user->email, $client_name );
            $message->sender( $user->email, $client_name );
            $message->replyTo($emails_to, $client_name );
			 
            if ( $request->file( 'files' ) ) {
                foreach ( $request->file( 'files' ) as $attachment ) {
                    $message->attach( $attachment->getRealPath(), [
                        'as'   => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getMimeType()
                    ] );
                }
            }
            $message->subject( $request->subject );
			$auth_user = Auth::getUser();
			$user_login = $auth_user->email;
            $add_email = new Email();
            $add_email->folder = 'INBOX';
            $add_email->user_login = $user_login;
            $add_email->subject = $request->subject;
            $add_email->content = $request->body;
            $add_email->attachments = '';
            $add_email->body_text = strip_tags($request->body);
            $add_email->body_html = $request->body;
            $add_email->date = date('Y-m-d H:i:s');
            $add_email->from = $auth_user->email;
            $add_email->size = strlen($request->body);
            $add_email->to = $emails_to;
            $add_email->has_attachments = 1;
            $add_email->is_answered = 0;
            $add_email->is_deleted = 0;
            $add_email->is_draft = 0;
            $add_email->is_seen = 0;
            $add_email->message_number = 0;
            $add_email->email_server = 1;
            $add_email->save();

            
            return $message;
        } );
        }
        catch(\Exception $e){
            return $e;
        }
	}

}