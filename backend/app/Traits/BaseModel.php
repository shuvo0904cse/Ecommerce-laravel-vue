<?php


namespace App\Traits;


use App\Helpers\AppHelper;
use App\Helpers\UserHelper;
use Illuminate\Support\Facades\Auth;

trait BaseModel
{
    /**
     * of Paginate
     */
    public function scopeOfPaginate($query, $request){
        $perPage = isset($request['per_page'])? $request['per_page']: config('settings.pagination.per_page');
        return $query->paginate($perPage);
    }

    /**
     * of Order
     */
    public function scopeOfOrder($query, $request){
        $orderBy = isset($request['order_by'])? $request['order_by']: config('settings.pagination.order_by');
        $orderDirection = isset($request['order_direction'])? $request['order_direction']: config('settings.pagination.order_direction');
        return $query->orderBy($orderBy, $orderDirection);
    }

    /**
     * Get All Lists
     */
    public function getAllLists($select = "*", $orderBy = "id", $orderDirection = "DESC")
    {
        return $this->select($select)->orderBy($orderBy, $orderDirection)->get();
    }

    /**
     * Exists Check By Id
     */
    public function existsCheckById($id, $columnName = "id")
    {
        return $this->where($columnName, $id)->exists();
    }

    /**
     * Details By Id
     */
    public function detailsById($id = "", $select = "*", $columnName = "id")
    {
        $query = $this->select($select);
        if(!empty($id)) $query = $query->where($columnName, $id);
        return $query->first();
    }

    /**
     * Details By Multi Id
     */
    public function detailsByMultiId($select = "*", $array)
    {
        return $this->select($select)->where($array)->first();
    }

    /**
     * Store Data
     */
    public function storeData($array)
    {
        return $this->create($array);
    }

    /**
     * Update Data
     */
    public function updateData($array, $id, $columnName = "id")
    {
        return $this->where($columnName, $id)->update($array);
    }

    /**
     * Delete Data
     */
    public function deleteData($id, $columnName= "id")
    {
        return $this->where($columnName, $id)->delete();
    }

    /**
     * Delete All Data
     */
    public function deleteAllData($isForceDelete = false)
    {
        $lists = $this->get();
        foreach ($lists as $list){
            if($isForceDelete == true){
                $list->forceDelete();
            }else{
                $list->delete();
            }

        }
        return true;
    }
    
    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        $user = auth('api')->user();

        //creating
        self::creating(function ($model) use ($user){
            $model->created_by = isset($user) ? $user->id : null;
        });

        //updating
        self::updated(function ($model) use ($user){
            $model->updated_by = isset($user) ? $user->id : null;
        });

        //deleting
        self::deleting(function ($model) use ($user){
            $model->deleted_by = isset($user) ? $user->id : null;
        });
    }
}