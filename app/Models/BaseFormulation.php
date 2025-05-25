<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseFormulation extends Model
{
    protected $table = 'BaseFormulation';

    protected $primaryKey = 'base_formulation_id';

    public $timestamps = false;

    protected $fillable = ['base_formulation_id', 'base_name'];
    
}
