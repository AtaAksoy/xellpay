<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    
    use SoftDeletes;

    protected $fillable = ['sim_registration_id', 'transaction_date', 'transaction_amount'];

    protected function casts() : array {
        return [
            'transaction_date' => 'date'
        ];
    }

    public function sim_registration() : BelongsTo {
        return $this->belongsTo(SimRegistration::class);
    }

}
