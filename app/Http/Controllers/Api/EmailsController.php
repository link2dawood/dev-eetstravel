<?php
namespace App\Http\Controllers\Api;


use App\Email;
//use Illuminate\Support\Facades\Mail;
use Mail;
use App\Http\Imap\ImapClient;
use App\Tour;
use App\User;
use Ddeboer\Imap\Exception\AuthenticationFailedException;
use Illuminate\Http\Request;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
class EmailsController{

    public $server;

    public static $perPage = 10;

    public static $page = 0;

    public static $folder = 'INBOX';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function setConnectionToServer($userId) {
        $user = User::findOrFail($userId);
		
        try{
      
                $this ->server = new ImapClient(
                    env( 'IMAP_HOST', 'eetstravel.com' ),
                    "booking-test@eetstravel.com", "Ffans82guQ9h",
                    ImapClient::ENCRYPT_SSL
                );
           

            return $this->server->isConnected();


        } catch(AuthenticationFailedException $e){
            echo "Authorization failed : " . $user->email_login . "\r\n";
        }
    }

    public function getFolderList($userId){
        $this ->setConnectionToServer($userId);
        $folders = $this ->server ->getFolders();
		
        return response()->json([
            'folders'=>$folders
        ]);
    }
	public function getTours(Request $request){
		$result = 
			Tour::whereNotIn('status',[46,6,39])
			->get();
		return response()->json([
            'tour' => $result,
            'perpage' => self::$perPage,
            'page' => self::$page
        ]);
	}
	public function getArchiveTours(Request $request){
		$result = 
			Tour::where('status', 6)->orWhere('status',39)
			->get();
		return response()->json([
            'tour' => $result,
            'perpage' => self::$perPage,
            'page' => self::$page
        ]);
	}
    public function getEmails($userId, Request $request){
		$subjects = [];
        if($request->folder AND ($request->folder!= 'INBOX'))
            self::$folder = 'INBOX.'.$request->folder;

        if($request->page )
            self::$page = ($request->page-1);

        $this ->setConnectionToServer($userId);

        $this ->server->selectFolder(self::$folder);
		


         if($request->search ){
			 $subject = 'SUBJECT "'.$request->search.'"';
            $result = $this ->server->getMessagesByCriteria($subject,self::$perPage,self::$page, 'DESC');
		
        } else {
            $result = $this ->server->getMessages(self::$perPage,self::$page, 'DESC', false);
        }
//        $this ->server ->close();
        return response()->json([
            'emails' => $result,
            'perpage' => self::$perPage,
            'count'=> $this ->server -> countMessages(),
            'page' => self::$page
        ]);
    }
	
public function remove_element($string){
    // Remove the first character
    $string = substr($string, 1);

    // Find the position of the "@" character
    $pos = strpos($string, '@');

    if ($pos !== false) {
        // Extract the substring before the "@" character
        $string = substr($string, 0, $pos);
    }

    // Find the position of the "<" character
    $pos = strpos($string, '<');

    if ($pos !== false) {
        // Extract the substring before the "<" character
        $string = substr($string, 0, $pos);
    }

    return $string;
}

   
	public function touremails($userId, Request $request){
		$subjects = [];
        if($request->folder AND ($request->folder!= 'INBOX'))
            self::$folder = 'INBOX.'.$request->folder;
			
        if($request->page )
            self::$page = ($request->page-1);

        $this ->setConnectionToServer($userId);

        $inboxFolder = 'Inbox';
		$sentFolder = 'Inbox.Sent';

		
		$tour = Tour::where("name",$request->search)->first();
		$tourId = $tour->id;  // replace with the actual tour ID

		// Assuming you have a table named 'email_tour' with columns 'email_uid' and 'tour_id'
		$emailIds = DB::table('move_emails')
			->where('tour_id', $tourId)
			->pluck('email_id');
	
		$database_messages = array();
			foreach($emailIds as $emailId){
				$inbox_mes = $this->server->selectFolder('Inbox.Drafts');
				$database_inbox_mess = $this ->server->getMessages(self::$perPage,self::$page, 'DESC', false);
					
				foreach($database_inbox_mess as $database_mess){
					//dd( $database_mess->header->message_id);
					$mess_id = htmlspecialchars($this->remove_element($database_mess->header->message_id));
					$em_id = htmlspecialchars($this->remove_element($emailId));
					//echo $mess_id. "<br>";
					//echo $em_id. "<br>";
					if($mess_id == $em_id){
						array_push($database_messages,$database_mess);
					}
				}
				
				
			}

         if($request->search ){
			 $subject = 'SUBJECT "'.$request->search.'"';
			 $inbox_mes = $this->server->selectFolder($inboxFolder);
            $inboxMessages =  $this->server->getMessagesByCriteria($subject,self::$perPage,self::$page, 'DESC');
			  $inbox_count  = count($inboxMessages);
			 $sent_mes = $this->server->selectFolder($sentFolder);
			 $sentMessages =  $this->server->getMessagesByCriteria($subject,self::$perPage,self::$page, 'DESC');
			 $sent_count  = count($sentMessages);
			 $database_sent_mess = $this->server->selectFolder($sentFolder);
			 $total_count = $inbox_count+$sent_count;
			 
		
        } else {
            $result = $this ->server->getMessages(self::$perPage,self::$page, 'DESC', false);
			 $inbox_mes = $this->server->selectFolder($inboxFolder);
            $inboxMessages =  $this->server->getMessagesByCriteria($subject,self::$perPage,self::$page, 'DESC');
			 $sent_mes = $this->server->selectFolder($sentFolder);
			 $sentMessages =  $this->server->getMessagesByCriteria($subject,self::$perPage,self::$page, 'DESC');
        }
		$result = array_merge($inboxMessages, $sentMessages,$database_messages);
//        $this ->server ->close();
        return response()->json([
            'emails' => $result,
            'perpage' => self::$perPage,
            'count'=> $total_count,
            'page' => self::$page
        ]);
    }

    public function moveEmail($userId, $emailUid, Request $request)
    {
		$moveFolder = 'Inbox.'.$request->moveFolder;
		
        $this ->setConnectionToServer($userId);
	
		self::$folder = 'INBOX.'.$request->folder;
        $this ->server-> selectFolder(self::$folder);
		if (is_numeric($request->moveFolder)) {
		    // replace with the actual UID
				$tourId = 456;  // replace with the actual tour ID
			
				// Using DB facade to perform the insert
				DB::table('move_emails')->insert([
					'email_id' => $request->message_id,
					'tour_id' => $request->moveFolder,
				]);
			 $result = $this->server->moveMessage($emailUid, 'Inbox.Drafts');
        return response()->json(['result'=>'succes','succes'=>$result, 'id'=>$emailUid,'folder'=>$moveFolder]);
		} else {
			echo "$request->all() is not a number or a numeric string.";
		}
        if($request->folder AND ($request->folder!= 'INBOX'))
            self::$folder = 'Inbox.'.$request->folder;


        
        $result = $this->server->moveMessage($emailUid, $moveFolder);
        return response()->json(['result'=>'succes','succes'=>$result, 'id'=>$emailId,'folder'=>$moveFolder]);
    }


    public function deleteEmail($userId, $emailUid, Request $request)
    {
        if($request->folder AND ($request->folder!= 'INBOX'))
            self::$folder = 'INBOX.'.$request->folder;

        $this ->setConnectionToServer($userId);
        $this ->server-> selectFolder(self::$folder);


        $emailId = $this->server->getId($emailUid);
        $res = $this->server->deleteMessage($emailUid);
        return response()->json(['result'=>'succes','succes'=>$res, 'id'=>$emailId]);
    }

    public function getEmail($userId,$emailUid, Request $request)
    {
        if($request->folder AND ($request->folder!= 'INBOX'))
            self::$folder = 'INBOX.'.$request->folder;

        $this ->setConnectionToServer($userId);
        $this ->server-> selectFolder(self::$folder);
        $emailId = $this->server->getId((int)$emailUid);

        $this->server ->setUnseenMessage($emailId, false);
			
		if($emailId == 0){
			$this ->server-> selectFolder('INBOX.Sent');
        	$emailId = $this->server->getId((int)$emailUid);
		
		}
		if($emailId == 0){
			$this ->server-> selectFolder('INBOX.Drafts');
        	$emailId = $this->server->getId((int)$emailUid);
		
		}
        $message = $this->server->getMessage($emailId);

        return response()->json(['result'=>'succes','message'=>$message, 'id'=>$emailId]);
    }
	public function getidEmail($userId,$emailid, Request $request)
    {
        if($request->folder AND ($request->folder!= 'INBOX'))
            self::$folder = 'INBOX.'.$request->folder;

        $this ->setConnectionToServer($userId);
        $this ->server-> selectFolder(self::$folder);
		$message= array();
		$database_inbox_mess = $this ->server->getMessages(self::$perPage,self::$page, 'DESC', false);
					
				foreach($database_inbox_mess as $database_mess){
					$mess_id = htmlspecialchars($this->remove_element($database_mess->header->message_id));
					$em_id = htmlspecialchars($this->remove_element($emailid));
					//echo $mess_id. "<br>";
					//echo $em_id. "<br>";
					if($mess_id == $em_id){
						array_push($message,$database_mess);
					}
				}
        return response()->json(['result'=>'succes','message'=>$message, 'id'=>$emailid]);
    }

    public function createFolder($userId, Request $request)
    {
        $this ->setConnectionToServer($userId);
        $message = $this->server->addFolder("Inbox.".$request->folder);
        return response()->json(['result'=>'succes','message'=>$message, 'id'=>$request->folder]);
    }

       public function sendEmail($userId, Request $request)
    {
//        return true;
	
		   
		try{
			
			$user = User::findOrFail($userId);

        $this ->setConnectionToServer($userId);
			

        

        $this ->server->selectFolder(self::$folder);

        $server = $this ->server;

        
			
		
            $client_name = $user ? $user->name : 'EETS Travel';

            $toArray   = explode( ',', $request->to );
            $emails_to = [];
            foreach ( $toArray as $item ) {
                $emails_to[] = trim( $item );
            }

			
		$to = $request->to;
		$subject = $request->subject;
		$content = $request->content;
/*
		$headers = "Content-Type: text/html; charset=UTF-8\r\n".
					"From:". $user->email_login."\r\n" .
				   "Reply-To:". $user->email_login."\r\n" .
				   "X-Mailer: PHP/" . phpversion();

		$emailData = [
			'to' => $to,
			'subject' => $subject,
			'message' => $content,
			'headers' => $headers,
		];
		imap_mail($emailData['to'], $emailData['subject'], $emailData['message'], $emailData['headers']);
		date_default_timezone_set("Asia/Karachi");

		$message = "From:". $user->email_login."\r\n";
		$message .= "To: ". $to."\r\n";
		$message .= "Subject:" .$subject."\r\n";
		$message .= "Date: " . date("r") . "\r\n";
		$message .= "\r\n";
		$message .= $content;
	
	   $server->saveMessageInSent( $message );
		*/	
        $mail = Mail::send( 'email.mail_template', compact( 'content' ), function ( $message ) use ( $request, $user, $server ) {
		

            $client_name = $user ? $user->name : 'EETS Travel';

            $toArray   = explode( ',', $request->to );
            $emails_to = [];
            foreach ( $toArray as $item ) {
                $emails_to[] = trim( $item );
            }
            $message->to( $emails_to );
            $message->from( $user->email_login, $client_name );
            $message->sender( $user->email_login, $client_name );
            if ( $request->file( 'files' ) ) {
                foreach ( $request->file( 'files' ) as $attachment ) {
                    $message->attach( $attachment->getRealPath(), [
                        'as'   => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getMimeType()
                    ] );
                }
            }
            $message->subject( $request->subject );
            $server->saveMessageInSent( $message->getSwiftMessage()->toString() );
            return $message;
        } );
        $this ->server->selectFolder('INBOX.Sent');
        $result = $this ->server->getMessages(self::$perPage,self::$page, 'DESC', false);
	  
        return response()->json([
            'emails' => $result,
            'perpage' => self::$perPage,
            'count'=> $this ->server -> countMessages(),
            'page' => self::$page
        ]);
        return response()->json(['result'=>'succes']);
			
		}catch (\Exception $e) {
            return $e;
        }
		
    }
    public function parseEmails(){
        $users = User::whereNotNull('email_login')->whereNotNull('email_password')->orderBy('id')->get();

        $pusher = \App::make( 'pusher' );
        if($users->isNotEmpty()) {
            foreach ($users as $user) {
                if(!$this->setConnectionToServer($user->id)){
                    continue;
                }
                $this ->server-> selectFolder('INBOX');//self::$folder);

                if(!$user->lastUID){
                    $messages = $this ->server->getMessages(1, 0, 'DESC', false);
//                    dd('11');
                } else {
                    $messages = $this ->server->getMessagesByCriteriaLatest($user->lastUID.':*',self::$perPage,self::$page, 'DESC');
                }
//                dd($messages);
                if(($count = count($messages)) > 1){

                    $currentMessage = current($messages);
                    $user->lastUID = $currentMessage->header->uid;
                    $user->save();
                    $pusher ->trigger( 'notification', 'new-emails', ['newEmailCount' => count($messages)-1, 'user' => $user->email_login, 'server' => request()->getHttpHost(), ]);
//                    $pusher ->trigger( 'notification', 'new-emails', ['newEmailCount' => count($messages), 'user' => $user->email_login, 'server' => request()->getHttpHost(), 'messages'=>$messages ]);
//                    return ['newEmailCount' => count($messages), 'user' => $user->email_login, 'server' => request()->getHttpHost(), 'messages'=>$messages ];
                }
                die;
            }
            die;
        }
    }

    public function downloadAttachments(Request $request)
    {
        file_put_contents(public_path('attach').'/'.$request-> attach['name'], base64_decode($request-> attach['body']));
        return url('attach').'/'.$request-> attach['name'];
    }
	
	   
	  
}