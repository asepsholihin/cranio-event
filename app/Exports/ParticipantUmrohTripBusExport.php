<?php

namespace App\Exports;

use App\Exceptions\ErrorMessageException;
use App\Exports\Sheets\ParticipantUmrohTripBusSheet;
use App\Models\ParticipantUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantUmrohTripBusExport implements WithMultipleSheets, WithDefaultStyles
{
    const START_ROW = 7;

    private $umrohId;
    private $busRoutes;

    public function __construct($umrohId, $busRoutes)
    {
        $this->umrohId = $umrohId;
        $this->busRoutes = $busRoutes;
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
        $buses = ParticipantUmrohTrip::where('umroh_trip_id', $this->umrohId)->whereNotNull('group_bus')->groupBy('group_bus')->get(['group_bus']);
        if (empty($buses->toArray())) {
            throw new ErrorMessageException('There is no manifest');
        }

        foreach ($buses as $bus) {
            $sheets[] = new ParticipantUmrohTripBusSheet($umrohTrip, $bus->group_bus, $this->busRoutes);
        }

        return $sheets;
    }

}
