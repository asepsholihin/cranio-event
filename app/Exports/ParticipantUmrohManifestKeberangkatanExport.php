<?php

namespace App\Exports;

use App\Exceptions\ErrorMessageException;
use App\Exports\Sheets\ParticipantUmrohManifestKeberangkatanSheet;
use App\Models\ParticipantUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;


class ParticipantUmrohManifestKeberangkatanExport implements WithMultipleSheets, WithDefaultStyles
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
        $ticketTypes = ['All Ticket'];

        $umrohTrip = UmrohTrip::find($this->umrohId);
        $participantWithoutTicket = ParticipantUmrohTrip::where('without_ticket', 1)->count();

        foreach ($ticketTypes as $ticketType) {
            $sheets[] = new ParticipantUmrohManifestKeberangkatanSheet($this->umrohId);
        }

        return $sheets;
    }
}
