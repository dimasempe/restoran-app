<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function index(){
        // return 'test';
        $orders = Order::select('id','customer_name','table_no',
        'order_date','order_time','status','total')->get();
        return response(['data' => $orders]);
    }
    public function store(Request $request){
        $request->validate([
            'customer_name' => 'required|max:100',
            'table_no' => 'required|max:5',
        ]);
        try {
            //code...
            DB::beginTransaction();

            $validateData = $request->only(['customer_name','table_no']);
            $validateData['order_date'] = date('Y-m-d');
            $validateData['order_time'] = date('H:i:s');
            $validateData['status'] = 'ordered';
            $validateData['total'] = 0;
            $validateData['waitress_id'] = auth()->user()->id;
            $validateData['items'] = $request->items;

            $order = Order::create($validateData);

            collect($validateData['items'])->map(function($itemId) use($order) {
                $foodDrink = Item::where('id',$itemId)->first();
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $itemId,
                    'price' => $foodDrink->price
                ]);
            });

            //edit total dari order
            $order->total =$order->sumOrderPrice();
            $order->save();
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response($th);
        }

        // return $validateData;

        return response(['data' => $order]);
    }

    public function show(Order $order){
        return $order->loadMissing('orderDetail');
    }
}
