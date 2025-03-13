<?php

namespace App\Exports;

use App\Exceptions\ErrorMessageException;
use App\Exports\Sheets\ParticipantUmrohManifestSheet;
use App\Models\ParticipantUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;


class ParticipantUmrohManifestExport implements WithMultipleSheets, WithDefaultStyles
{
    private $umrohId;
    private $umrohTrip;

    public function __construct($umrohId)
    {
        $this->umrohId = $umrohId;
        $this->umrohTrip = UmrohTrip::find($umrohId);
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
        $ticketTypes = ['All Ticket','Economy','Business'];

        $umrohTrip = UmrohTrip::find($this->umrohId);
        $participantWithoutTicket = ParticipantUmrohTrip::where('without_ticket', 1)->count();
        if($participantWithoutTicket > 0) {
            $ticketTypes = ['All Ticket','Economy','Business','Without Ticket'];
        }

        foreach ($ticketTypes as $ticketType) {
            $sheets[] = new ParticipantUmrohManifestSheet($this->umrohId, $ticketType);
        }

        return $sheets;
    }
}
