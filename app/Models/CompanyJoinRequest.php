<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyJoinRequest extends Model
{
    protected $fillable = ['user_id', 'target_company_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
