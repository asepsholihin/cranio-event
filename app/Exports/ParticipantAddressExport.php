<?php

namespace App\Exports;

use App\Exports\Sheets\ParticipantAddressSheet;
use App\Exports\Sheets\MasterProvKabSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantAddressExport implements WithMultipleSheets
{

    private $umrohId;

    public function __construct($umrohId)
    {
        $this->umrohId = $umrohId;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        $sheets[] = new ParticipantAddressSheet($this->umrohId);
        $sheets[] = new MasterProvKabSheet();

        return $sheets;
    }

}
