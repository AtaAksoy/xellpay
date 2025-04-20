<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    
    use SoftDeletes;

    protected array $fillable = ['sim_registration_id', 'bill_date'];

    protected function casts() : array
    {
        return [
            'bill_date' => 'date'
        ];
    }

    public function sim_registration() : BelongsTo {
        return $this->belongsTo(SimRegistration::class);
    }

}
