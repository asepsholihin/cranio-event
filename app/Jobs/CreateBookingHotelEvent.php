<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\BookingHotelEvent;
use Illuminate\Support\Facades\Log;


class CreateBookingHotelEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $formFill;

    public function __construct($formFill)
    {
        $this->formFill = $formFill;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            BookingHotelEvent::createBookingHotelEvent($this->formFill);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
        }
    }

    public function failed($e)
    {
        
    }
}
