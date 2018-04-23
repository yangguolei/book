<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class TempPhone extends Model
{
    protected $table = 'temp_phone';
    protected $primaryKey = 'id';

    //临时手机验证详细不需要时间戳
    public $timestamps = false;
}
