<?php

namespace App\Mail;

use Log;
use App\Models\SuratEdaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class KirimSuratEdaran extends Mailable
{
    use Queueable, SerializesModels;

    public $surat;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SuratEdaran $surat)
    {
        $this->surat = $surat;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filePath = $this->surat->file;


        $fullPath = Storage::disk('public')->path($filePath);


        if (file_exists($fullPath)) {
            return $this->subject('Surat Edaran: ' . $this->surat->judul)
                ->markdown('emails.kirim-surat')
                ->attach($fullPath, [
                    'as' => $this->surat->judul . '.' . pathinfo($filePath, PATHINFO_EXTENSION),
                    'mime' => mime_content_type($fullPath),
                ]);
        } else {

            Log::error("Lampiran surat edaran tidak ditemukan: {$fullPath}");

            return $this->subject('Surat Edaran: ' . $this->surat->judul)
                ->markdown('emails.kirim-surat');
        }
    }
}
