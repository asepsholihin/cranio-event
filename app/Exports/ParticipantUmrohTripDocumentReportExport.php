<?php

namespace App\Exports;

use App\Exports\Sheets\ParticipantUmrohTripDocumentReportSheet;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantUmrohTripDocumentReportExport implements WithMultipleSheets, WithDefaultStyles
{
    const START_ROW = 4;

    private $umrohId;

    public function __construct($umrohId)
    {
        $this->umrohId = $umrohId;
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
        $umrohTrip = UmrohTrip::find($this->umrohId);
        $packages = PackageUmrohTrip::where('umroh_trip_id', $this->umrohId)->get();
        foreach ($packages as $package) {
            $sheets[] = new ParticipantUmrohTripDocumentReportSheet($umrohTrip, $package);
        }

        return $sheets;
    }

}
