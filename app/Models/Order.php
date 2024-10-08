<?php

namespace App\Models;

use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    // protected $guarded = ['id'];
    protected $fillable = [
        'customer_name','table_no','order_date','order_time','status','total','waitress_id'
    ];

    public function sumOrderPrice(){
        // return 'test';
        // $orderDetail = OrderDetail::where('order_id',$this->id)->get();
        $priceOrderDetail = OrderDetail::where('order_id',$this->id)->pluck('price');
        // return $orderDetail;
        return $sum = collect($priceOrderDetail)->sum();
    }

    /**
     * Get all of the orderDetails for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
}
