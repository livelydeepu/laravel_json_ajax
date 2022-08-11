<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Storage;
use File;

class ProductController extends Controller
{
    /**
    * Display list of all the products.
    *
    * @param  int  $id
    * @return \Illuminate\View\View
    */
    public function index() {
        $products = Product::all();
        $result = ['page_title'=>'Products', 'products'=>$products];
        //echo("<pre>");
        //print_r($products);
        return view('product', $result);
    }

    /**
    * Create the product form instance.
    *
    * @param  \Illuminate\Http\Request  $request, int  $id
    * @return \Illuminate\View\View
    */
    public function manage(Request $request, $id = '') {
        if( $id > 0 ) {
            $result = Product::where(['id' => $id])->get();
            $result['id'] = $result['0']->id;
            $result['product_name'] = json_decode($result['0']['data'])->product_name;
            $result['quantity_in_stock'] = json_decode($result['0']['data'])->quantity_in_stock;
            $result['price_per_item'] = json_decode($result['0']['data'])->price_per_item;
            //return json_encode(array('result'=>$productData));
        } else {
            $result['id'] = '';
            $result['product_name'] = '';
            $result['quantity_in_stock'] = '';
            $result['price_per_item'] = '';
        }
        return view('manage_product', $result);
    }

    /**
     * Store or Update a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response redirect route to show the products
     */
    public function process(Request $request) {
        $input = $request->validate([
            'product_name' => 'required',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required',
        ]);

        $input = $request->all();
        if($request->post('id') > 0) {
            $product = Product::find($request->post('id'));
            $product->update(["data" => json_encode($input)]); 
            $msg = 'Product Updated Successfully';
        } else {
            $product = new Product(["data" => json_encode($input)]);
            //File::put(public_path(time(). '_products.json'), json_encode($input));
            if(Storage::disk('public')->exists('products.json')) {
                Storage::disk('public')->append('products.json', json_encode($input, JSON_PRETTY_PRINT));
            } else {
                Storage::disk('public')->put('products.json', json_encode($input, JSON_PRETTY_PRINT));
            }
            $product->save();
            $msg = 'Product Created Successfully';
        }       
        return redirect()->route('product')->with('success', $msg);
    }

    /**
     * Delete a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response redirect route to show the products
     */
    public function delete(Request $request, $id) {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('product')->with('success', 'Product Deleted Successfully');
    }

    /**
     * Update or Delete a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response redirect route to show the products
     */
    function updateAjax(Request $request) {
    	if($request->ajax()) {
    		if($request->action == 'edit') {
                $product = Product::find($request->id);
                $input = $request->all();
                $product->update(["data" => json_encode($input)]); 
    		}
    		if($request->action == 'delete') {
    			$product = Product::find($request->id);
                $product->delete();
    		}
    		return response()->json($request);
    	}
    }

    /**
     * Update or Delete a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response redirect route to show the products
     */
    function updateProduct(Request $request) {
    	if($request->ajax()) {
    		if($request->action == 'edit') {
                $product = Product::find($request->id);
                $input = $request->all();
                $product->update(["data" => json_encode($input)]); 
    		}
    		if($request->action == 'delete') {
    			$product = Product::find($request->id);
                $product->delete();
    		}
    		return response()->json($request);
    	}

        if($request->ajax()) {
            $products = Product::all();

            foreach ($products as $product) {
                $product->timestamps = false; // To disable update_at field updation
                $id = $product->id;

                foreach ($request->product as $product) {
                    if ($product['id'] == $id) {
                        $product->update(['product' => $product['position']]);
                    }
                }
            }
        }
        
        return response('Update Successfully.', 200);
    }
}