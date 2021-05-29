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
        'image',
        'description'
    ];

    /**
     * Get Lists
     */
    public function getProductLists($request){
        return $this->ofSearch($request)->ofUser()->ofOrder($request)->ofPaginate($request);
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
                $q->where('title', 'LIKE', '%' . $search . '%');
            });
        }
        return $query;
    }
}
