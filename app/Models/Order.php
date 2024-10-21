<?php

namespace App\Models;

use App\Models\User;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        $details = OrderDetail::where('order_id',$this->id)->get();
        // $priceOrderDetail = OrderDetail::where('order_id',$this->id)->pluck('price');

        // return $orderDetail;
        // return $sum = collect($priceOrderDetail)->sum();
        $sum = $details->sum(fn($detail) => $detail->price * $detail->quantity);

        return $sum;
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

    /**
     * Get the waitress that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function waitress(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waitress_id', 'id');
    }

    /**
     * Get the waitress that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
