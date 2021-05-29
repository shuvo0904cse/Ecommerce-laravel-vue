<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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
            $results = $this->productModel()->getProductLists($request->all());
            return new ProductCollection($results);
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * Store User
     */
    public function store(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'title'         => 'required|string|max:191',
            'code'          => 'required|string|max:191|unique:products,code',
            'image'         => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'   => 'required'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage(null, $validator->errors());

        try {
            //save Image
            if ($request->hasFile('image')) {
                $image = $this->uploadImage()->upload($request->file("image"), config("setting.product_path"));
            }

            //store
            $productArray = [
                'title'         => $request->title,
                'code'          => $request->code,
                'image'         => isset($image) ? $image : null,
                'description'   => $request->description
            ];
            $user = $this->productModel()->storeData($productArray);

            return $this->message::successMessage(config("messages.save_message"), $user);
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * Update User
     */
    public function update(Request $request, $productId)
    {
        //user check
        $details = $this->productModel()->detailsById($productId);
        if (empty($details)) return $this->message::errorMessage(config("messages.not_exists"));

        //validation
        $validator = Validator::make($request->all(), [
            'title'         => 'required|string|max:191',
            'code'          => 'required|string|max:191|unique:products,code,' . $productId,
            'description'   => 'required'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage(null, $validator->errors());

        try {
            //save Image
            if ($request->hasFile('image')) {
                $image = $this->uploadImage()->upload($request->file("image"), config("setting.product_path"));
            }

            //update
            $productArray = [
                'title'         => $request->title,
                'code'          => $request->code,
                'image'         => isset($image) ? $image : $details->image,
                'description'   => $request->description
            ];
            $this->productModel()->updateData($productArray, $productId);

            return $this->message::successMessage(config("messages.save_message"));
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * delete User
     */
    public function delete($userId)
    {
        $details = $this->productModel()->detailsById($userId);
        if (empty($details)) return $this->message::errorMessage(config("messages.not_exists"));

        try {
            $this->productModel()->deleteData($userId);
            return $this->message::successMessage(config("messages.delete_message"));
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    private function productModel(){
        return new Product();
    }

    private function uploadImage(){
        return new ImageUploadService();
    }
}
