<?php

namespace App\Exports;

use App\Exports\Sheets\ParticipantSheet;
use App\Exports\Sheets\MasterProvKabSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantExport implements WithMultipleSheets
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
        
        $sheets[] = new ParticipantSheet($this->umrohId);
        $sheets[] = new MasterProvKabSheet();

        return $sheets;
    }

}
