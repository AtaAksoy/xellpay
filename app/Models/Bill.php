<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    
    use SoftDeletes;

    protected $fillable = ['sim_registration_id', 'bill_date', 'is_paid'];

    protected function casts() : array
    {
        return [
            'bill_date' => 'date'
        ];
    }

    public function sim_registration() : BelongsTo {
        return $this->belongsTo(SimRegistration::class);
    }

    public function details() : HasMany {
        return $this->hasMany(BillDetail::class);
    }

}
