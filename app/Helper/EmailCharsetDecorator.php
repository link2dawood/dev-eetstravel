<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 09.10.17
 * Time: 15:24
 */

namespace App\Helper;


use Ddeboer\Imap\Mailbox;
use Ddeboer\Imap\Message;

class EmailCharsetDecorator
{
    private $email;

    /**
     * EmailCharsetDecorator constructor.
     * @param $message
     */
    public function __construct(Message $message)
    {
        $this->email = $message;
    }

    private function changeCharset($text){
        $text = mb_convert_encoding($text, "UTF-8");
        $text = iconv("UTF-8", "UTF-8//IGNORE", $text);
        return $text;
    }

    public function getId()
    {
        return $this->email->getId();
    }

    public function getFrom() : string
    {
        return $this->changeCharset($this->email->getFrom()->getAddress());
    }

    public function getNumber() : string
    {
        return $this->changeCharset($this->email->getNumber());
    }

    public function getSubject() : string
    {
        return $this->changeCharset($this->email->getSubject());
    }

    public function getDate()
    {
        return $this->email->getDate();
    }


    public function getTo()
    {
        return $this->email->getTo();
    }


    public function getCc()
    {
        return $this->email->getCc();
    }


    public function getSize()
    {
        return $this->email->getSize();
    }


    public function getContent()
    {
        return $this->email->getContent();
    }


    public function isAnswered()
    {
        return $this->email->isAnswered();
    }


    public function isDeleted()
    {
        return $this->email->isDeleted();
    }


    public function isDraft()
    {
        return $this->email->isDraft();
    }


    public function isSeen()
    {
        return $this->email->isSeen();
    }



    public function getHeaders()
    {
        return $this->email->getHeaders();
    }


    public function getBodyHtml()
    {
        return $this->email->getBodyHtml();
    }


    public function getBodyText()
    {
        return $this->email->getBodyText();
    }


    public function getAttachments()
    {
        return $this->email->getAttachments();
    }


    public function hasAttachments()
    {
        return $this->email->hasAttachments();
    }


    public function delete()
    {
        return $this->email->delete();
    }


    public function move(Mailbox $mailbox)
    {
        return $this->email->move($mailbox);
    }


    public function keepUnseen()
    {
        return $this->email->keepUnseen();
    }



}