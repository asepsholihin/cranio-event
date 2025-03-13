<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\LogInvoice;

class Invoice extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const STATUS_UNPAID = 1;
    const STATUS_PAID = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_UPDATED = 4;

    const DIR_CREDIT_RECEIPT = 'invoice-credit-receipt';

    const PREFIX_ORDER_NUMBER = 'JIBB/INV/';
    const MONTH_ROMAWI = [1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII"];

    protected $casts = [
        'payment_date'  => 'date:Y-m-d',
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
        'invoice_no',
        'participant_id',
        'name',
        'no_hp',
        'due_payment',
        'total_payment',
        'payment_amount',
        'total_discount',
        'payment_date',
        'payment_method',
        'payment_notes',
        'payment_status',
        'credit_image_receipt',
        'tax',
        'created_by',
        'updated_by'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }

    public function items()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function setInvoiceNumber()
    {
        $orderNumber = LogInvoice::getInvoiceNumber($this->id, "invoice_empty");
        $this->invoice_no = $orderNumber;
        $this->save();
    }

    public function getDescriptionPDF($currency = "IDR")
    {
        if ($this->status == self::STATUS_PAID) {
            if($this->usd_convertion) {
                return "Pembayaran {$this->description} ({$this->payment_date->format('d F Y')}) " . $this->payment_amount/$this->usd_price;
            } else {
                return "Pembayaran {$this->description} ({$this->payment_date->format('d F Y')})";
            }
        }

        if ($this->status == self::STATUS_UPDATED) {
            return "{$this->description}";
        }

        return "{$this->description} {$currency} " . number_format($this->payment);
    }

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('name',  request()->query('q'))
            ->orWhere('name', 'like', $search);
    }
}
