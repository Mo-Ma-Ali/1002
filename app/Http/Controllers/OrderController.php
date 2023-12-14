<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pharmaceutical;
use App\Models\User;
use PHPUnit\TextUI\Configuration\Merger;
use Symfony\Component\Console\Input\Input;

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
    $token = $request->header('Authorization');
    $user = User::where('api_token', $token)->first();
    $user_id=$user->id;

    // Validate the request data if needed

        $order= Order::create([
            'user_id'=>$user_id
        ]);
    foreach ($request->input('order') as $pharmaceutical) {
        $order->pharmaceuticals()->attach($pharmaceutical['pharmaceutical_id'], [
            'quantity' => $pharmaceutical['quantity'],
        ]);
    }
    $order->load('pharmaceuticals');

    return response()->json(['message' => 'Orders created successfully','order'=>$order]);
}
    public function stauts()
    {

    }


   public function getClients(Request $request)
{
    $userDetails = [];

    // Retrieve unique user IDs associated with orders
    $userIds = Order::distinct()->pluck('user_id')->toArray();

    // Get user details for each unique user ID
    foreach ($userIds as $userId) {
        $user = User::find($userId);

        if ($user) {
            $userDetails[] = [
                'name' => $user->name,
                'id' => $user->id,
            ];
        }
    }

    return response()->json(['users' => $userDetails], 200);
}
public function getDate(Request $request)
{
    $user_id = $request->input('id');

    // Retrieve orders for the specified user and select only the 'created_at' column
    $orderDates = Order::where('user_id', $user_id)->pluck('created_at');

    // Transform the collection to an associative array
    $formattedDates = $orderDates->map(function ($date) {
        return ['date' => $date];
    });

    return response()->json(['order_dates' => $formattedDates], 200);
}



public function getOrder(Request $request)
{
    $date = $request->input('date');
    // $user_id = $request->input('user_id');

    // Retrieve orders for the specified user and date
    $order = Order::where('created_at', $date)->first();

    if (!$order) {
        return response()->json(['message' => 'No order found for the specified date and user.'], 404);
    }

    // Format date with time
    $formattedDateTime = $order->created_at->format('Y-m-d\TH:i:s.u\Z');

    // Retrieve pharmaceuticals associated with the order
    $pharmaceuticals = $order->pharmaceuticals;

    return response()->json(['order' => ['Date' => $formattedDateTime], 'pharmaceuticals' => $pharmaceuticals], 200);
}


 public function retrieveOrders()
    {
        // Retrieve all orders with associated pharmaceuticals and their quantities
        $orders = Order::with('pharmaceuticals')->get();
        return response()->json(['orders' => $orders]);
    }
}
