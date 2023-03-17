<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class settingModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'accountId',
        'tokenMs',
        'authtoken',
        'CashboxUniqueNumber',
    ];

}
