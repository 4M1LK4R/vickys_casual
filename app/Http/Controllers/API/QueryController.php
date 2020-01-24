<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\User;
use App\Store;
use App\Subcategory;
use App\Product;
use App\Activity;

class QueryController extends Controller
{

    public function find(Request $request)
    {

        if (Product::where('state', 'ACTIVO')->where('barcode',  $request->barcode)->exists()) {
            
            $lat_ = round($request->lat, 3);
            $lng_ = round($request->lng, 3);

            //Buscar Tienda

            $Store=null;
            


            $Stores = Store::All()->where('state', 'ACTIVO');
    
            foreach ($Stores as $key => $value) {
                $store_lat = round($value->lat, 3);
                $store_lng = round($value->lng, 3);

                if ($store_lat == $lat_ && $store_lng==$lng_) {
                    $Store = $value;    
                }
            }

            if ($Store != null) {




                $Product = Product::where('state', 'ACTIVO')
                ->where('barcode', $request->barcode)
                ->where('store_id', $Store->id)
                ->with('store', 'subcategory')
                ->first();


                //Registro actividad
                $Activity = Activity::create([
                    'user_id' => $request->user_id,
                    'product_id' => $Product->id,
                ]);

// //===================================================================================
// return response()->json(['success' => true, 'msg' => 'LLEGANDO', 'obj' => $Product, 'activity' => $Activity]);
// //===================================================================================


                return response()->json(['success' => true, 'msg' => 'Registro encontrado', 'obj' => $Product]);

            }else {
                return response()->json(['success' => false, 'msg' => 'No se encuntrar registros con la ubicación.']);
            }

        } else {
            return response()->json(['success' => false, 'msg' => 'El código no esta registrado, intente de nuevo']);
        }


    }
}
