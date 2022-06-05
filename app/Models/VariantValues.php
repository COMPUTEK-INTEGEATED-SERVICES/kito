<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantValues extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id', 'value'
    ];
}
