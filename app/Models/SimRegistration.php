<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SimRegistration extends Model
{
    
    use SoftDeletes;

    protected $fillable = ['phone_number', 'subscriber_id'];

    public function usages() : HasMany {
        return $this->hasMany(Usage::class);
    }

    public function transactions() : HasMany {
        return $this->hasMany(Transaction::class);
    }

    public function bills() : HasMany {
        return $this->hasMany(Bill::class);
    }

}
