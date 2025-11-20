<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
    * The primary key associated with the table
    *
    * @var string
    */
    protected $primaryKey = 'code';

    /**
    * Type for primary key
    *
    * @var string
    */
    protected $keyType = 'string';


    /**
    * The primary key is not incrementing
    *
    * @var bool
    */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}
