<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
//    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'is_active', 'is_delete', 'id'
    ];

    /**
     * Scope a query to only include active records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('is_active', 1)
            ->where('is_delete', 0);
    }

    /**
     * Get the address record associated with the user
     */
    public function address_details(){
        return $this->hasOne(Addresses::class, 'id', 'address_id');
    }
}
