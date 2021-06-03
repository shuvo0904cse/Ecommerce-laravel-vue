<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, BaseModel, UsesUuid;

    protected $fillable = [
        'title',
        'code',
        'description',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get Lists
     */
    public function getProductLists($request){
        return $this->ofSearch($request)->ofRelation()->ofUser()->ofOrder($request)->ofPaginate($request);
    }

    /**
     * Search
     */
    public function scopeOfUser($query){
        $user = Auth::user();
        if (!empty($user) && $user->role == config("settings.customer_role")) {
            $query->where('created_by', $user->id);
        }
        return $query;
    }

    /**
     * Search
     */
    public function scopeOfSearch($query, $request){
        $search = isset($request['search'])? $request['search']: "";
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('code', 'LIKE', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * Relation
     */
    public function scopeOfRelation($query){
        return $query->with(['images']);
    }
}
