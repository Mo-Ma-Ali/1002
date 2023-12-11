<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pharmaceutical;
use PHPUnit\TextUI\Configuration\Merger;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // $request->validate([
        //     'user_id'=>'required|integer',
        //     'pharmaceutical_id'=>'required|integer'
        // ]);
        $order = Order::create($request->all());
        return response()->json([
            'message' => 'Order send successfully',
            'order'=>$order
        ], 200);
          // $order = Order::create();

        // Attach pharmaceuticals to the order with quantities
        // foreach ($request->pharmaceuticals as $pharmaceuticalData) {
        //     $order->pharmaceuticals()->attach(
        //         $pharmaceuticalData['pharmaceutical_id'],
        //         ['quantity' => $pharmaceuticalData['quantity']]
        //     );
        // }

       // return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    public function store(Request $request)
{
    $orders = $request->input('order');

    // Validate the request data if needed

    foreach ($orders as $orderData) {
        Order::create([
            'pharmaceutical_id' => $orderData['pharmaceutical_id'],
            'quantity' => $orderData['quantity'],
        ]);
    }

    return response()->json(['message' => 'Orders created successfully','order'=>$orders]);
}

    public function retrieveOrders()
    {
        // Retrieve all orders with associated pharmaceuticals and their quantities
        $orders = Order::get();

        return response()->json(['orders' => $orders]);
    }
}
