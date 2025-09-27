<?php

namespace App\Console\Commands;

use App;
use App\Email;
use App\EmailFolder;
use App\User;
use Carbon\Carbon;
use DateInterval;
use DateTimeImmutable;
use Ddeboer\Imap\Exception\AuthenticationFailedException;
use Ddeboer\Imap\Exception\Exception;
use Ddeboer\Imap\Exception\MessageDoesNotExistException;
use Ddeboer\Imap\Exception\UnexpectedEncodingException;
use Ddeboer\Imap\Mailbox;
use Ddeboer\Imap\Message;
use Ddeboer\Imap\Search\Date\After;
use Ddeboer\Imap\Server;
use Ddeboer\Transcoder\Exception\IllegalCharacterException;
use Ddeboer\Transcoder\Exception\UndetectableEncodingException;
use Illuminate\Console\Command;

class ParseEmails extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'email:parse {period?} {--period_type=} {--email=} {--force} {--folder=} {--check_frequency}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parse Emails From Email Server';

	private $period;

	private $periodType;

	private $folder;

	public $server;

	public $vianetServer;

	public $pusher;

	public $newEmailsCount;



	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

//		$this->server = new Server(
//			env( 'IMAP_HOST', 'localhost' ),
//			env( 'IMAP_PORT' ),
//			'/imap/tls/novalidate-cert'
//		);
//
//		$this->vianetServer = new Server(
//			env( 'VIANET_HOST', 'localhost' ),
//			env( 'VIANET_PORT' ),
//			env( 'VIANET_FLAGS' )
//		);
//		$this->pusher         = App::make( 'pusher' );
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */

//	public function handle() {
//		echo "------------ PARSING ----------".Carbon::now()."\r\n";
//		$this->folder = $this->option( 'folder' );
//
//		$period = ( $this->argument( 'period' ) );
//		$parseFrequency = App\Setting::where('name', 'email_refresh_frequency')->first();
//
//		if (Carbon::now()->minute % $parseFrequency->value != 0
//		&& !$this->folder && $this->option('check_frequency')
//		) {
//			echo "Pass minute \r\n";
//			return false;
//		}
//
//
//		if ( ! $period ) {
//			$period = 1;
//		}
//
//		$periodType = ( $this->option( 'period_type' ) );
//		$force        = $this->option( 'force' );
//		switch ($periodType) {
//			case 'H' :
//				$this->period = 'PT' . $period . 'H';
//				break;
//			case 'M' :
//				$this->period = 'PT' . $period . 'M';
//				break;
//			case 'D' :
//			default:
//				$this->period = 'P' . $period . 'D';
//			break;
//
//		}
//		echo "Period : " . $this->period . "\r\n";
//		if ( $this->option( 'email' ) ) {
//			$users = $this->getUserCredentials( $this->option( 'email' ) );
//		} else {
//			$users = $this->getAllUsersCredentials();
//		}
//		foreach ( $users as $user ) {
//			try {
//				$this->newEmailsCount = 0;
//				$this->syncEmails( $user, $force );
//				if ($this->newEmailsCount) {
//					echo "User ".$user['email_login']." New : ". $this->newEmailsCount."\r\n";
//					$this->pusher->trigger( 'notification', 'new-emails', ['newEmailCount' => $this->newEmailsCount, 'user' => $user['email_login'], 'server' => request()->getHttpHost() ]);
//				}
//
//			} catch ( AuthenticationFailedException $e ) {
//				echo "Authorization failed : " . $user['email_login'] . "\r\n";
//			}
//		}
//	}



	public function handle(){
        $emailsController = new App\Http\Controllers\Api\EmailsController();
        $emailsController->parseEmails();
    }




	private function getUserCredentials( $emailLogin ) {
		$user = User::where( 'email_login', $emailLogin )->first( [ 'email_login', 'email_password', 'email_server' ] );
		if ( $user ) {
			return [ $user->toArray() ];
		}

		return false;
	}

	private function getAllUsersCredentials() {
		$users  = User::all( [ 'email_login', 'email_password', 'email_server' ] )->toArray();
		$result = [];
		foreach ( $users as $user ) {
			if ( $user['email_login'] && $user['email_password'] ) {
				if ( ! in_array( $user['email_login'], array_column( $result, 'email_login' ) ) ) {
					$result[] = $user;
				}
			}
		}

		return $result;
	}

	private function syncEmails( $user, $force ) {
		echo "Client : " . $user['email_login'] . "\r\n";
		if($user['email_server'] == Email::TYPE_EETS) {
			$client = $this->server->authenticate(
				(string) $user['email_login'],
				(string) $user['email_password']
			);
		}
		if ($user['email_server'] == Email::TYPE_VIANET) {
			$client = $this->vianetServer->authenticate(
				(string) $user['email_login'],
				(string) $user['email_password']
			);
		}
		if(isset($client) && $client) {
			if ( ! $this->folder ) {
				$mailBoxes = $client->getMailboxes();
			} else {
				$mailBoxes[] = $client->getMailbox( $this->folder );
			}
			foreach ( $mailBoxes as $mailBox ) {
				//try {
				$dbFolder = $this->getFolderFromDatabase( $user, $mailBox->getName() );
				echo "Folder -" . $mailBox->getName() . "\r\n";
				$this->saveEmails( $dbFolder, $mailBox, $user['email_login'], $force, $user['email_server']);

//		    } catch (IllegalCharacterException $e) {
//			    echo "Folder problem :";
//		    }
			}
		}



	}

	private function saveEmails( EmailFolder $dbFolder, Mailbox $folder, $userLogin, $force, $serverType ) {
		$today    = new DateTimeImmutable();
		$halfYear = $today->sub( new DateInterval( $this->period ) );

		$emails = $folder->getMessages(
			new \Ddeboer\Imap\Search\Date\Since( $halfYear ),
			\SORTDATE, // Sort criteria
			true // Descending order
		);


		echo "FOLDER COUNT:" . count( $emails ) . "\r\n";
		$newEmailsCount = 0;

		foreach ( $emails as $key => $email ) {
			if ( $key % 50 == 0 ) {
				echo $key . "\r\n";
			}

			try {
				$time        = Carbon::now()->timestamp;
				$emailExists = Email::where( 'user_login', '=', $userLogin )
				                    ->where( 'folder', '=', $folder->getName() )
				                    ->where( 'message_id', '=', $email->getNumber() )
				                    ->where( 'email_server', '=', $serverType )
				                    ->first();

			} catch ( IllegalCharacterException $e ) {
				echo "EmailsExists" . $e->getMessage() . "\r\n";
			}
			if ( ! $emailExists ) {
				try {
					//delete old email when moved
					$movedEmail = Email::where( 'user_login', '=', $userLogin )
					                    ->where( 'folder', '!=', $folder->getName() )
					                    ->where( 'message_id', '=', $email->getNumber() )
										->where( 'email_server', '=', $serverType )
					                    ->first();
					if ($movedEmail) {
						$movedEmail->delete();
					}

					$message                  = new Email();
					$message->user_login      = $userLogin;
					$message->message_id      = $email->getNumber();
					$message->folder          = $folder->getName();
					$message->date            = $email->getDate();
					$message->body_text       = $email->getBodyText();
					$message->body_html       = $email->getBodyHtml();
					$message->date            = $email->getDate()->setTimezone(new \DateTimeZone(env('SERVER_TIMEZONE', 'Europe/London')));
					$message->subject         = $email->getSubject();
					$message->to              = $this->getTo( $email->getTo() );
					$message->content         = $email->getContent();
					$message->from            = $email->getFrom()->getAddress();
					$message->has_attachments = $email->hasAttachments();
					$message->mail_id         = $email->getId();
					$message->size            = $email->getSize();
					$message->is_answered     = $email->isAnswered();
					$message->is_deleted      = $email->isDeleted();
					$message->is_draft        = $email->isDraft();
					$message->is_seen         = $email->isSeen();
					$message->email_server    = $serverType;
					$message->save();
					$this->newEmailsCount++;

				} catch ( UndetectableEncodingException $e ) {
//				    echo "Email $folder->getName() : $email->getNumber() has undetectable encoding";
				} catch ( IllegalCharacterException $e ) {
					echo "Illegal Character";
//		    		echo "Email $folder->getName() : $email->getNumber() has illegal character";
				} catch ( MessageDoesNotExistException $e ) {
//			        echo "Email $folder->getName() : $email->getNumber() does not exist";
				}
			 catch ( UnexpectedEncodingException $e ) {
//			        echo "Email $folder->getName() : $email->getNumber() does not exist";
			}
			catch ( Exception $e ) {
//				    echo $e->getMessage();
//				    echo "Email $folder->getName() : $email->getNumber() has other exception";
				}

			} else {

				if ( $force ) {
					$message                  = $emailExists;
					$message->user_login      = $userLogin;
					$message->message_id      = $email->getNumber();
					$message->folder          = $folder->getName();
					$message->date            = $email->getDate();
					$message->body_text       = $email->getBodyText();
					$message->to              = $this->getTo( $email->getTo() );
					$message->body_html       = $email->getBodyHtml();
					$message->date            = $email->getDate();
					$message->subject         = $email->getSubject();
					$message->content         = $email->getContent();
					$message->from            = $email->getFrom()->getAddress();
					$message->has_attachments = $email->hasAttachments();
					$message->mail_id         = $email->getId();
					$message->size            = $email->getSize();
					$message->is_answered     = $email->isAnswered();
					$message->is_deleted      = $email->isDeleted();
					$message->is_draft        = $email->isDraft();
					$message->is_seen         = $message->is_seen ?: $email->isSeen();
					$message->email_server         = $serverType;
					$message->update();
				}
			}
		}
	}

	public function getTo( $to ) {
		$result = [];
		if ( $to ) {

			/** @var Message\EmailAddress $item */
			foreach ( $to as $item ) {
				$result[] = $item->getAddress();
			}
		}

		return implode( ', ', $result );
	}

	private function insertEmail( Message $message ) {

	}


	private function getFolderFromDatabase( $user, $folderName ) {

		$folderExists = EmailFolder::where( 'name', '=', $folderName )
		                           ->where( 'user_login', '=',  $user['email_login'] )->first();
		if ( ! $folderExists ) {
			$emailFolder             = new EmailFolder();
			$emailFolder->user_login = $user['email_login'];
			$emailFolder->name       = $folderName;
			$emailFolder->email_server       = $user['email_server'];
			$emailFolder->save();

			return $emailFolder;
		}

		return $folderExists;
	}
}
