<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ItemAcabando extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Instancia dos itens em falta.
     *
     * @var \App\Models\Inventario
     */
    public $inventarios;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $inventarios)
    {
        $this->inventarios = $inventarios;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.item_acabando');
    }
}
