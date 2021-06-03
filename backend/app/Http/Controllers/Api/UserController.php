<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $message;

    public function __construct()
    {
        $this->message = new MessageHelper();
    }

    /**
     * Lists
     */
    public function index(Request $request)
    {
        try {
            $results = $this->userModel()->getUserLists($request->all());
            return new UserCollection($results);
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email',
            'password'  => 'sometimes|required|min:6',
            'role'      => 'required|in:ADMIN,CUSTOMER'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage(null, $validator->errors());

        DB::beginTransaction();
        try {
            //upload Image
            if(!empty($request->photo)){
                $photo = ImageHelper::base64ToImage($request->photo, config("settings.user_upload_path"));
            }

            //store
            $userArray = [
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => bcrypt($request->password),
                'role'              => $request->role,
                'photo'             => isset($photo) ? $photo : null,
                'email_verified_at' => now()->toDateTimeString()
            ];
            $user = $this->userModel()->storeData($userArray);

            DB::commit();
            return $this->message::successMessage(config("messages.save_message"), $user);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $userId)
    {
        //user check
        $details = $this->userModel()->detailsById($userId);
        if (empty($details)) return $this->message::errorMessage(config("messages.not_exists"));

        if ($details->id == auth('api')->user()->id) return $this->message::errorMessage(config("messages.unable_to_update_information"));

        //validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email,' . $userId,
            'role'      => 'required|in:ADMIN,CUSTOMER'
        ]);

        if ($validator->fails()) return $this->message::validationErrorMessage(null, $validator->errors());

        DB::beginTransaction();
        try {
            //upload Image
            if(!empty($request->photo)){
                $photo = ImageHelper::base64ToImage($request->photo, config("settings.user_upload_path"));
            }

            //Update
            $userArray = [
                'name'              => $request->name,
                'email'             => $request->email,
                'role'              => $request->role,
                'photo'             => isset($photo) ? $photo : $details->photo
            ];
            $this->userModel()->updateData($userArray, $userId);
            DB::commit();
            return $this->message::successMessage(config("messages.update_message"));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * delete
     */
    public function delete($userId)
    {
        $details = $this->userModel()->detailsById($userId);
        if (empty($details)) return $this->message::errorMessage(config("messages.not_exists"));

        if ($details->id == auth('api')->user()->id) return $this->message::errorMessage(config("messages.unable_to_delete_yourself"));

        try {
            $this->userModel()->deleteData($userId);
            return $this->message::successMessage(config("messages.delete_message"));
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    private function userModel(){
        return new User();
    }
}
