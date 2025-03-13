<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LogInvoice;
use Carbon\Carbon;

class InvoiceUmrohTrip extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    const STATUS_UNPAID = 1;
    const STATUS_PAID = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_UPDATED = 4;

    const DIR_RECEIPT = 'web/receipts';
    const DIR_INVOICE = 'web/invoices';
    const DIR_CREDIT_RECEIPT = 'invoice-credit-receipt';

    const PREFIX_ORDER_NUMBER = 'JIBB/INV/';
    const MONTH_ROMAWI = [1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII"];

    protected $casts = [
        'payment_date'  => 'date:Y-m-d',
        'payment_created_at'  => 'date:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'credit_image_receipt_url',
    ];

    public function creditImageReceiptUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['credit_image_receipt'] ?? null
            ),
        );
    }

    protected $fillable = [
        'order_umroh_trip_id',
        'invoice_no',
        'status',
        'description',
        'name',
        'email',
        'no_hp',
        'due_date',
        'due_payment',
        'payment',
        'payment_amount',
        'payment_date',
        'payment_method',
        'payment_note',
        'payment_created_at',
        'credit_status',
        'credit_payment_date',
        'credit_payment_method',
        'credit_payment_note',
        'credit_image_receipt',
        'other_bank',
        'created_by',
        'updated_by',
        'invoice_url',
        'receipt_url',
        'url',
        'usd_convertion',
        'usd_price',
        'invoice_and_receipt_url',
        'deleted_by',
        'additional_dp_pax'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                if(auth()->user())
                    $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                if(auth()->user())
                    $model->updated_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                if(auth()->user())
                    $model->updated_by = auth()->user()->id;
            }
        });
    }

    public function receiptUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['receipt_url'],
        );
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['url'],
        );
    }

    public function invoiceAndReceiptUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['invoice_and_receipt_url'],
        );
    }

    public function credit_image_receipts()
    {
        return $this->hasMany(CreditImageReceipt::class);
    }

    public function setOrderNumber()
    {
        $orderNumber = LogInvoice::getInvoiceNumber($this->id, "invoice_umroh");
        $this->invoice_no = $orderNumber;
        $this->save();
    }

    public function getDescriptionPDF($currency = "IDR")
    {
        if ($this->status == self::STATUS_PAID) {
            if($this->usd_convertion) {
                return "Pembayaran {$this->description} ({$this->payment_date->format('d F Y')}) " . "IDR ".number_format($this->payment_amount, 0, ',', '.') . " Kurs IDR ". number_format($this->usd_price, 0, ',', '.');
            } else {
                return "Pembayaran {$this->description} ({$this->payment_date->format('d F Y')})";
            }
        }

        if ($this->status == self::STATUS_UPDATED) {
            return "{$this->description}";
        }

        if($this->usd_convertion) {
            return "{$this->description} IDR " . number_format($this->payment, 0, ',', '.') . " Kurs IDR ". number_format($this->usd_price, 0, ',', '.');
        }
        return "{$this->description} {$currency} " . number_format($this->payment);
    }

    public function scopeCreditReceipt($query)
    {
        $query->join('order_umroh_trips', 'order_umroh_trips.id', '=', 'invoice_umroh_trips.order_umroh_trip_id')
            ->select('order_umroh_trips.id as order_id', 'order_umroh_trips.order_no', 'order_umroh_trips.total_pax_trip', 'order_umroh_trips.sales_name', 'order_umroh_trips.total_payment', 'invoice_umroh_trips.*')
            ->where('invoice_umroh_trips.credit_status', 1);
        
        $start = Carbon::now()->subDays(7);
        $end = Carbon::now();
        if (!empty(request()->date)) {
            $dateXplode = explode('to', request()->date);
            $exDateStart = str_replace('/','-',$dateXplode[0]);
            if(isset($dateXplode[1]))
                $exDateEnd = str_replace('/','-',$dateXplode[1]);
            $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
            $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
        }
        if (!empty(request()->q)) {
            $query->where(function($q) {
                $q->where('invoice_umroh_trips.name', 'like', '%'.request()->q.'%')
                ->orWhere('invoice_umroh_trips.invoice_no', 'like', '%'.request()->q.'%')
                ->orWhere('order_umroh_trips.sales_name', 'like', '%'.request()->q.'%')
                ->orWhere('order_umroh_trips.order_no', 'like', '%'.request()->q.'%');
            });
        }
        $query->whereBetween('invoice_umroh_trips.created_at', [$start, $end]);
        $query->orderBy('invoice_umroh_trips.created_at', 'asc');
        
    }
}
