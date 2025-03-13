<?php

namespace App\Actions\Dashboard;

use App\Models\EventAttendance;
use App\Models\Participant;
use App\Models\UmrohTrip;
use App\Models\Crew;
use App\Models\OrderUmrohTrip;
use App\Models\InvoiceUmrohTrip;

class Booking
{
    public function __invoke()
    {
        return [
            ['icon' => 'TagIcon', 'color' => 'light-primary','title'=>number_format(0,0, ',', '.'),'subtitle'=>'Bookings','customClass'=>'mb-2'],
            ['icon' => 'UsersIcon', 'color' => 'light-info','title'=>number_format(0,0, ',', '.'),'subtitle'=>'Pax','customClass'=>'mb-2'],
            ['icon' => 'ShieldIcon', 'color' => 'light-success','title'=>number_format(0,0, ',', '.'),'subtitle'=>'Booking Paid','customClass'=>'mb-2'],
            ['icon' => 'ShieldOffIcon', 'color' => 'light-warning','title'=>number_format(0,0, ',', '.'),'subtitle'=>'Booking Unpaid','customClass'=>'mb-2'],
            ['icon' => 'DollarSignIcon', 'color' => 'light-info','title'=>number_format(0, 0, ',', '.'),'subtitle'=>'Total Transaction','customClass'=>'mb-2'],
            ['icon' => 'CheckCircleIcon', 'color' => 'light-success','title'=>number_format(0, 0, ',', '.'),'subtitle'=>'Total Paid Amount','customClass'=>'mb-2'],
            ['icon' => 'AlertCircleIcon', 'color' => 'light-warning','title'=>number_format(0, 0, ',', '.'),'subtitle'=>'Total Unpaid', 'customClass'=>'mb-2'],
        ];
    }
}
