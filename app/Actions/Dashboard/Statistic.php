<?php

namespace App\Actions\Dashboard;

use App\Models\EventAttendance;
use App\Models\Participant;
use App\Models\UmrohTrip;
use App\Models\Crew;

class Statistic
{
    public function __invoke()
    {
        return [
            ['icon' => 'UsersIcon', 'color' => 'light-primary','title'=>0,'subtitle'=>'Participant','customClass'=>'mb-2 mb-xl-0'],
            ['icon' => 'CalendarIcon', 'color' => 'light-warning','title'=>0,'subtitle'=>'Events','customClass'=>'mb-2 mb-xl-0'],
        ];
    }
}
