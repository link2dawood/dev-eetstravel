<?php

namespace App\Helper;

use App\Comment;
use App\Email;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\Exception\Exception;
use Illuminate\Support\Facades\Route;

class AdminHelper
{

    public static function getBackButton($listRoute = null)
    {
        $currentPath = \Request::fullUrl();
        $referer = request()->headers->get('referer');
        
        if ($currentPath == $referer || !$referer) {
            return $listRoute;
        } else {
            return $referer;
        }
    }

    /** ToDo:make check */
    public static function emailCheck($email) {
//    	try {
//    		$email->getHeaders();
//    		return true;
//	    } catch (\Exception $e) {
//
//    		return false;
//	    }
	    return true;
    }

    public function validateEmailCorrect($email_login, $email_password, $email_server){
        $client = null;

        try {
            $server = null;

            if($email_server == Email::TYPE_EETS){
                $server = new Server(
                    env('IMAP_HOST', 'localhost'),
                    env('IMAP_PORT'),
                    '/imap/tls/novalidate-cert'
                );
            }

            if ($email_server == Email::TYPE_VIANET) {
                $server = new Server(
                    env( 'VIANET_HOST', 'localhost' ),
                    env( 'VIANET_PORT' ),
                    env( 'VIANET_FLAGS' )
                );
            }

            if($server){
                $client = $server->authenticate(
                    (string)$email_login,
                    (string)$email_password);
            }

        } catch (\Exception $e) {
            return false;
        }

        return $client ? true : false;

    }

    public static function getComparisonRowCommentsCount($comparisonRowId) {
    	return Comment::query()->where(
    		[
    			'reference_type' => Comment::$services['comparison'],
			    'reference_id' => $comparisonRowId
		    ])->count();
    }
}
