<?php

namespace App\Models;

use App\Enums\FeatureType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usage extends Model
{
    
    use SoftDeletes;

    protected array $fillable = ['sim_registration_id', 'usage_date', 'feature_type', 'feature_amount'];

    protected function casts() : array
    {
        return [
            'usage_date' => 'date',
            'feature_type' => FeatureType::class
        ];
    }

    public function sim_registration() : BelongsTo {
        return $this->belongsTo(SimRegistration::class);
    }

}
