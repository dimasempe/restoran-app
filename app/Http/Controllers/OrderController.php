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
    public function index()
    {
        // return 'test';
        $orders = Order::select(
            'id',
            'customer_name',
            'table_no',
            'order_date',
            'order_time',
            'status',
            'total',
            'waitress_id',
            'cashier_id'
        )->with(['waitress:id,name', 'cashier:id,name'])->get();
        return response(['data' => $orders]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|max:100',
            'table_no' => 'required|numeric|max:5',
        ]);
        try {
            //code...
            DB::beginTransaction();

            $validateData = $request->only(['customer_name', 'table_no']);
            $validateData['order_date'] = date('Y-m-d');
            $validateData['order_time'] = date('H:i:s');
            $validateData['status'] = 'ordered';
            $validateData['total'] = 0;
            $validateData['waitress_id'] = auth()->user()->id;
            $validateData['items'] = $request->items;

            $order = Order::create($validateData);

            collect($validateData['items'])->map(function ($item) use ($order) {
                $foodDrink = Item::where('id', $item['id'])->first();
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'price' => $foodDrink->price,
                    'quantity' => $item['quantity']
                ]);
            });

            //edit total dari order
            $order->total = $order->sumOrderPrice();
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

    public function show(Order $order)
    {
        return response([
            'data' =>
                $order->loadMissing('orderDetail:order_id,price,item_id,quantity', 'orderDetail.item:id,name', 'waitress:id,name', 'cashier:id,name')
        ]);


    }

    public function setAsDone(Order $order)
    {
        if ($order->status != 'ordered') {
            return response('order cannot set to done because the status is not ordered', 403);
        }
        $order->status = 'done';
        $order->save();

        return response(['data' => $order->loadMissing('orderDetail:order_id,price,item_id,quantity', 'orderDetail.item:id,name', 'waitress:id,name', 'cashier:id,name')]);
    }

    public function payment(Order $order)
    {
        if ($order->status != 'done') {
            return response('payment cannot be done because the status is not done', 403);
        }
        $order->status = 'paid';
        $order->cashier_id = auth()->user()->id;
        $order->save();

        return response(['data' => $order->loadMissing('orderDetail:order_id,price,item_id,quantity', 'orderDetail.item:id,name', 'waitress:id,name', 'cashier:id,name')]);
    }
    public function orderReport(Request $request)
    {
        $orders = Order::whereMonth('order_date', $request->month)->select(
            'id',
            'customer_name',
            'table_no',
            'order_date',
            'order_time',
            'status',
            'total',
            'waitress_id',
            'cashier_id'
        )->with(['waitress:id,name', 'cashier:id,name'])->get();
        
        $ordersCount = Order::whereMonth('order_date',$request->month)->count();
        $maxPayment = Order::whereMonth('order_date',$request->month)->max('total');
        $minPayment = Order::whereMonth('order_date',$request->month)->min('total');

        // Total keseluruhan di bulan tersebut
        $ordersSumTotal = Order::whereMonth('order_date',$request->month)->sum('total');

        // Total Teh
        $itemsWithTeh = Item::where('name', 'like', 'Teh%')->pluck('id');
        $totalTeh = OrderDetail::whereIn('item_id', $itemsWithTeh)->sum('quantity');

        // Total Bakmie Premium
        $itemsWithPremium = Item::where('name', 'like', 'Bakmie Sambal Matah%')->pluck('id');
        $totalPremium = OrderDetail::whereIn('item_id', $itemsWithPremium)->sum('quantity');

        // Total Bakmie Afford
        $itemsWithAfford = Item::where('name', 'like', 'Bakmie Original%')
        ->orWhere('name', 'like', 'Bakmie Chili%')
        ->orWhere('name', 'like', 'Bakmie Yamien%')
        ->pluck('id');
        $totalAfford = OrderDetail::whereIn('item_id', $itemsWithAfford)->sum('quantity');

        // Total Dimsum
        $itemsWithDimsum = Item::where('name', 'like', 'Dimsum%')->pluck('id');
        $totalDimsum = OrderDetail::whereIn('item_id', $itemsWithDimsum)->sum('quantity');

        // Total Pangsit
        $itemsWithPangsit = Item::where('name', 'like', 'Pangsit%')->pluck('id');
        $totalPangsit = OrderDetail::whereIn('item_id', $itemsWithPangsit)->sum('quantity');

        // Total Gohyong
        $itemsWithGohyong = Item::where('name', 'like', 'Gohyong%')->pluck('id');
        $totalGohyong = OrderDetail::whereMonth('created_at',$request->month)->whereIn('item_id', $itemsWithGohyong)->sum('quantity');


        $result = [
            'orders' => $orders,
            'ordersCount' => $ordersCount,
            'maxPayment' => $maxPayment,
            'minPayment' => $minPayment,
            'ordersSumTotal' => $ordersSumTotal,
            'totalTeh' => $totalTeh,
            'totalPremium' => $totalPremium,
            'totalAfford'=> $totalAfford,
            'totalDimsum' => $totalDimsum,
            'totalPangsit' => $totalPangsit,
            'totalGohyong' => $totalGohyong,
            'sumTotal' => $ordersSumTotal

        ];
        return response(['data' => $result]);
        


    }
}
