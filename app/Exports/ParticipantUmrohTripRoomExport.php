<?php

namespace App\Exports;

use App\Exports\Sheets\ParticipantUmrohTripRoomPackageSheet;
use App\Exports\Sheets\ParticipantUmrohTripRoomSheet;
use App\Exports\Sheets\ParticipantUmrohTripRoomHotelSheet;
use App\Exports\Sheets\ParticipantUmrohTripRoomDefaultSheet;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\HotelUmrohTrip;
use App\Models\ParticipantUmrohTrip;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantUmrohTripRoomExport implements WithMultipleSheets, WithDefaultStyles
{
    const START_ROW = 7;

    private $umrohId;
    private $category;

    public function __construct($umrohId, $category)
    {
        $this->umrohId = $umrohId;
        $this->category = $category;
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

        if($this->category == 'default') {
            $packages = PackageUmrohTrip::where('umroh_trip_id', $this->umrohId)->get();
            foreach ($packages as $package) {
                $sheets[] = new ParticipantUmrohTripRoomDefaultSheet($umrohTrip, $package);
            }
        }
        if($this->category == 'package') {
            $packages = PackageUmrohTrip::where('umroh_trip_id', $this->umrohId)->get();
            foreach ($packages as $package) {
                $sheets[] = new ParticipantUmrohTripRoomPackageSheet($umrohTrip, $package);
            }
        }
        if($this->category == 'trip') {
            $packages = PackageUmrohTrip::where('umroh_trip_id', $this->umrohId)->get();
            foreach ($packages as $package) {
                $sheets[] = new ParticipantUmrohTripRoomSheet($umrohTrip, $package);
            }
            // Check upgrade hotel
            $checkUpgradeHotel = ParticipantUmrohTrip::getParticipantUpgradeHotel($this->umrohId);
            if($checkUpgradeHotel) {
                $sheets[] = new ParticipantUmrohTripRoomSheet($umrohTrip, null, $checkUpgradeHotel);
            }
        }
        if($this->category == 'hotel') {
            $hotels = HotelUmrohTrip::select('star','check_in','check_out','city_name','hotel_name')->where('umroh_trip_id', $this->umrohId)->get()->toArray();
            $packages = PackageUmrohTrip::where('umroh_trip_id', $this->umrohId)->get();

            $hotelMakkah = [];
            $hotelMadinah = [];
            foreach ($packages as $package) {
                $hotelMakkah[] = array(
                    'star' => $package->star_hotel_makkah,
                    'check_in' => $package->check_in_makkah,
                    'check_out' => $package->check_out_makkah,
                    'city_name' => 'MAKKAH',
                    'hotel_name' => $package->hotel_makkah
                );
                $hotelMadinah[] = array(
                    'star' => $package->star_hotel_madinah,
                    'check_in' => $package->check_in_madinah,
                    'check_out' => $package->check_out_madinah,
                    'city_name' => 'MADINAH',
                    'hotel_name' => $package->hotel_madinah
                );
            }
            $result = array_merge($hotelMakkah, $hotelMadinah);
            $result = array_merge($result, $hotels);

            foreach ($result as $hotel) {
                $sheets[] = new ParticipantUmrohTripRoomHotelSheet($umrohTrip, $hotel);
            }
        }

        return $sheets;
    }

}
