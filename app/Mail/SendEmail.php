<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{   
    public $email;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        //
        $this->email= $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //  return $this->view('view.email');
         return $this->from('emps.mobile22@gmail.com')
                     ->subject('Konfirmasi transaksi')
                     ->view('view.email');
    }
}
