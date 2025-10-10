<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Tour;

class TourEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tour;
    public $emailType;
    public $customContent;

    /**
     * Create a new message instance.
     *
     * @param Tour $tour
     * @param string $emailType
     * @param string|null $customContent
     */
    public function __construct(Tour $tour, $emailType = 'confirmation', $customContent = null)
    {
        $this->tour = $tour;
        $this->emailType = $emailType;
        $this->customContent = $customContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->getEmailSubject();
        $view = $this->getEmailView();

        return $this->subject($subject)
                    ->view($view)
                    ->with([
                        'tour' => $this->tour,
                        'customContent' => $this->customContent,
                    ]);
    }

    /**
     * Get email subject based on type
     *
     * @return string
     */
    private function getEmailSubject()
    {
        switch ($this->emailType) {
            case 'confirmation':
                return 'Tour Confirmation - ' . $this->tour->name;
            case 'quotation':
                return 'Tour Quotation - ' . $this->tour->name;
            case 'invoice':
                return 'Tour Invoice - ' . $this->tour->name;
            case 'reminder':
                return 'Tour Reminder - ' . $this->tour->name;
            case 'cancellation':
                return 'Tour Cancellation - ' . $this->tour->name;
            default:
                return 'Tour Information - ' . $this->tour->name;
        }
    }

    /**
     * Get email view based on type
     *
     * @return string
     */
    private function getEmailView()
    {
        switch ($this->emailType) {
            case 'confirmation':
                return 'emails.tour.confirmation';
            case 'quotation':
                return 'emails.tour.quotation';
            case 'invoice':
                return 'emails.tour.invoice';
            case 'reminder':
                return 'emails.tour.reminder';
            case 'cancellation':
                return 'emails.tour.cancellation';
            default:
                return 'emails.tour.default';
        }
    }
}
