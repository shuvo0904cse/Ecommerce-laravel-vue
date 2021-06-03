<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    use HasFactory, BaseModel, UsesUuid;

    protected $fillable = [
        'product_id',
        'image',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
