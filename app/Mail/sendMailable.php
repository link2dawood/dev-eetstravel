<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class sendMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->view('emails.your_template')
                    ->with('data', $this->data);
    }

    public function sendEmail()
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'your_smtp_host';                       //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'your_email_username';                  //SMTP username
            $mail->Password   = 'your_email_password';                  //SMTP password
            $mail->SMTPSecure = 'tls';                                   //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = your_smtp_port;                          //TCP port to connect to, use 587 for `PHPMailer::ENCRYPTION_STARTTLS` above

            //Recipients
            $mail->setFrom('your_email_address', 'Your Name');
            $mail->addAddress('recipient@example.com', 'Recipient Name');     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Your email subject';
            $mail->Body    = view('emails.your_template', ['data' => $this->data])->render();

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
