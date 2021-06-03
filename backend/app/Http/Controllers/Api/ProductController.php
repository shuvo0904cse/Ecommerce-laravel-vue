<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'code'          => 'required|string|max:191|unique:products,code'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage(null, $validator->errors());

        DB::beginTransaction();
        try {
            //store
            $productArray = [
                'title'         => $request->title,
                'code'          => $request->code,
                'description'   => $request->description
            ];
            $product = $this->productModel()->storeData($productArray);

            //upload Image
            if(!empty($request->images)){
                foreach($request->images as $image){
                    $this->uploadImage($image, $product->id);
                }
            }
            DB::commit();
            return $this->message::successMessage(config("messages.save_message"), $product);
        } catch (\Exception $ex) {
            DB::rollBack();
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

        DB::beginTransaction();
        try {
            //update
            $productArray = [
                'title'         => $request->title,
                'code'          => $request->code,
                'description'   => $request->description
            ];
            $this->productModel()->updateData($productArray, $productId);

            //upload Image
            if(!empty($request->images)){
                foreach($request->images as $image){
                    $this->uploadImage($image, $details->id);
                }
            }

            DB::commit();
            return $this->message::successMessage(config("messages.update_message"));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    protected function uploadImage($image, $productId){
        $imageName = ImageHelper::base64ToImage($image, config("settings.product_upload_path"));
        //store Image
        $imageArray = [
            'image'         => $imageName,
            'product_id'    => $productId
        ];
        $this->productImageModel()->storeData($imageArray);
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

    private function productImageModel(){
        return new ProductImage();
    }
}
