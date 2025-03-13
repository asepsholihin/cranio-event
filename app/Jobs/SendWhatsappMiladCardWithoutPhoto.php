<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Inisiatif\WhatsappQontakPhp\Client;
use Inisiatif\WhatsappQontakPhp\Credential;
use Inisiatif\WhatsappQontakPhp\Message\Body;
use Inisiatif\WhatsappQontakPhp\Message\Button;
use Inisiatif\WhatsappQontakPhp\Message\Header;
use Inisiatif\WhatsappQontakPhp\Message\Message;
use Inisiatif\WhatsappQontakPhp\Message\Receiver;
use Inisiatif\WhatsappQontakPhp\Message\Language;

use Carbon\Carbon;
use App\Models\ParticipantFile;
use App\Models\Participant;
use Illuminate\Support\Str;
use App\File\Image\MiladCard;
use App\File\Image\MiladCardWithoutPhoto;

class SendWhatsappMiladCardWithoutPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phoneNumber;
    public $name;
    public $templateId;
    public $channelId;
    private $title;
    private $photo;

    public function __construct($participantId)
    {
        $participant = Participant::find($participantId);

        $this->phoneNumber = $participant->no_hp;
        $this->name = $participant->name;

        $age = (new Carbon($participant->birth_date))->age;
        if ($age >= 22) {
            $this->templateId = ($participant->gender == 1) ? '2d89176f-548f-4bab-a356-dfbeb2feb13e' : 'da05944e-075e-44a1-b76b-02129b4f37e2';
        } else {
            $this->templateId = 'e5fdf87a-77cd-4b79-9e7c-422f48571d4b';
        }
        $this->photo = (new MiladCard($participant, false))->stream();

        $this->channelId = '96f2fe5f-fcd8-4e97-ba92-fa2d66741010';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $credentials = new Credential(\config('qontak.username'), \config('qontak.password'), \config('qontak.client_id'), \config('qontak.client_secret'));
        $client = new Client($credentials);

        if (Str::startsWith($this->phoneNumber, '0')) {
            $this->phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $this->phoneNumber);
        }

        // Create message receiver
        $name_formated = rtrim(preg_replace(array('/\s{2,}/', '/[\t\n]/', '/[^a-zA-Z0-9\']/','/[^\p{L}\p{N}]/u', '/\s*(?:[\d_]|[^\w\s])+/', '!\s+!'), ' ', $this->name), " ");
            $receiver = new Receiver($this->phoneNumber, $name_formated);

        // [Optional] Create language, supported 'en' and 'id', default is 'id'
        $language = new Language('id');

        // [Optional] Create params message body
        $body = [
            new Body($this->name)
        ];

        // [Optional] Create header message, support "DOCUMENT", "VIDEO", "IMAGE"
        $header = new Header(
            Header::TYPE_IMAGE,
            $this->photo,
            $this->name . ' Milad Card.jpg'
        );

        // [Optional] Create buttons
        $buttons = [];

        $message = new Message($receiver, $language, $body, $header, $buttons);
        $response = $client->send($this->templateId, $this->channelId, $message);

        // Message Id and Receiver Name
        // echo $response->getMessageId();
        // echo $response->getName();

    }
}
