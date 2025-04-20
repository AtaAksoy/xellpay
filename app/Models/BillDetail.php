<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillDetail extends Model
{
    
    use SoftDeletes;

    protected array $fillable = ['bill_id', 'usage_id', 'amount'];

    public function bill() : BelongsTo {
        return $this->belongsTo(Bill::class);
    }

    public function usage() : BelongsTo {
        return $this->belongsTo(Usage::class);
    }

}
