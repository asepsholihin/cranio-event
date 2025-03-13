<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class InvoiceDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'invoice_id',
        'description',
        'unit',
        'qty',
        'price',
        'total_price'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
