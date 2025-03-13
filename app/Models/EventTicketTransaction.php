<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketTransaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    const PREFIX_ORDER_NUMBER = 'PJI';

    protected $fillable = [
        'event_id',
        'transaction_id',
        'transaction_date',
        'name',
        'no_hp',
        'email',
        'is_alumni',
        'pax',
        'payment_method',
        'total_payable',
        'send_email_invoice',
        'send_ticket',
        'transaction_status',
        'invoice_url',
        'last_umroh_trip',
        'notes',
        'pax_ikhwan',
        'pax_akhwat'
    ];

    public function setTransactionCode()
    {
        $recordNumber = self::whereYear('created_at', date('Y'))
        ->withTrashed()
        ->count();
        
        $transactionId = self::PREFIX_ORDER_NUMBER . date('ym') . str_pad($recordNumber, 4, 0, STR_PAD_LEFT);
        $this->transaction_id = $transactionId;
        $this->save();
    }

    public function scopeTableSearch($query)
    {
        $query->leftJoin('event_open_registrations', 'event_open_registrations.uuid', '=', 'event_ticket_transactions.event_id')
            ->select(['event_ticket_transactions.*']);

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where(function($q) use($search) {
            $q->where('event_ticket_transactions.name', 'like', $search)
            ->orWhere('transaction_id', 'like', $search)
            ->orWhere('no_hp', 'like', $search);
        });
    }
}
