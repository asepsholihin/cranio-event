<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class PaymentApprovalDocument
{
    private $pdf;
    private $paymentApproval;
    private $data;

    public function __construct($paymentApproval)
    {
        App::setLocale('id');
        $this->paymentApproval = $paymentApproval;
        $paymentApproval->date = $this->changeDate(date('D-m-Y', strtotime($paymentApproval->submitted_at_name)), date('d', strtotime($paymentApproval->submitted_at_name)));
        $data = [
            'paymentApproval' => $paymentApproval,
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.payment_approval_document', $data);
        $this->pdf->setOption('enable-local-file-access', true)
                   ->setOption('page-size', 'A4')
                   ->setOption('margin-bottom', '2cm');
    }

    public function download()
    {
        return $this->pdf->download('PAYMENT APPROVAL '. strtoupper($this->paymentApproval->pa_number).'.pdf');
    }

    public function html()
    {
        return view('pdf.payment_approval_document', $this->data)->render();
    }

    public function output()
    {
        return $this->pdf->output();
    }

    public function stream()
    {
        return $this->pdf->stream();
    }

    private function changeDate($year, $day){
        $edx = explode('-', $year);
        $month = [];
        array_push($month, array('id'=>1, 'name'=>'Januari'));
        array_push($month, array('id'=>2, 'name'=>'Febuari'));
        array_push($month, array('id'=>3, 'name'=>'Maret'));
        array_push($month, array('id'=>4, 'name'=>'April'));
        array_push($month, array('id'=>5, 'name'=>'Mei'));
        array_push($month, array('id'=>6, 'name'=>'Juni'));
        array_push($month, array('id'=>7, 'name'=>'Juli'));
        array_push($month, array('id'=>8, 'name'=>'Agustus'));
        array_push($month, array('id'=>9, 'name'=>'September'));
        array_push($month, array('id'=>10, 'name'=>'Oktober'));
        array_push($month, array('id'=>11, 'name'=>'November'));
        array_push($month, array('id'=>12, 'name'=>'Desember'));

        $cha = "";
        $days = [];
        array_push($days, array('id'=>'Mon', 'name'=>'Senin'));
        array_push($days, array('id'=>'Thu', 'name'=>'Selasa'));
        array_push($days, array('id'=>'Wed', 'name'=>'Rabu'));
        array_push($days, array('id'=>'Tue', 'name'=>'Kamis'));
        array_push($days, array('id'=>'Fri', 'name'=>'Jumat'));
        array_push($days, array('id'=>'Sat', 'name'=>'Sabtu'));
        array_push($days, array('id'=>'Sun', 'name'=>'Minggu'));

        foreach($days as $k){
            if($edx[0] == $k['id']){
                $cha .= $k['name'];
                break;
            }
        }

        $cha .= ', ' . $day . ' ';

        foreach($month as $k){
            if($edx[1] == $k['id']){
                $cha .= $k['name'];
                break;
            }
        }

        $cha .= ' '. $edx[2];
        return $cha;
    }
}
