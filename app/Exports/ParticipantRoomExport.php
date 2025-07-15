<?php

namespace App\Exports;

use App\Exports\Sheets\ParticipantRoomSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantRoomExport implements WithMultipleSheets, WithDefaultStyles
{
    const START_ROW = 7;

    private $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [ 'font' => [ 'size' => 16 ] ];
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new ParticipantRoomSheet($this->eventId);

        return $sheets;
    }

}
