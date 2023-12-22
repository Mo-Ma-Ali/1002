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





public function status(Request $request)
{
    $orderId = $request->input('id');
    $newStatus = $request->input('status');
    //dd($newStatus);
    $order = Order::findOrFail($orderId);
    if ($order->status == "in process" && $newStatus == "cancel")
    {
        $order->update(['status' => $newStatus]);

        return response()->json(['message' => 'Order canceled.']);
    }
    elseif ($order->status == "in process" && $newStatus == "in preparation") {
        // Update the status of the order
        $order->update(['status' => $newStatus]);
        return response()->json(['message' => 'Order status In preparation.']);
    }
    elseif (($order->status == "in preparation" && $newStatus == "send")||($order->status == "in process" && $newStatus == "send"))  {
        // Update the status of the order to "send"
        $pharmaceuticals = $order->pharmaceuticals()->withPivot('quantity')->get();
        // Update the quantity in the Pharmaceutical table
        foreach ($pharmaceuticals as $pharmaceutical) {
            $pharmaceuticalId = $pharmaceutical->id;
            $pivotQuantity = $pharmaceutical->pivot->quantity;

            // Update the quantity_available in the Pharmaceutical table
            Pharmaceutical::where('id', $pharmaceuticalId)
                ->decrement('quantity_available', $pivotQuantity);
        }
        $order->update(['status' => $newStatus]);

        return response()->json(['message' => 'Order status: send.']);
    }
    return response()->json(['message' => 'Invalid operation.']);
}



public function payment(Request $request)
{
    $request->validate([
        'id' => 'required|exists:orders,id',
        'payment' => 'required|in:paid',
    ]);

    $orderId = $request->input('id');
    $paid = $request->input('payment');
    $order = Order::findOrFail($orderId);

    if ($order->payment == "unpaid" && $paid == "paid")
    {
        $order->update(['payment' => $paid]);
        return response()->json(['message' => 'Order paid successfully.']);
    }

    return response()->json(['message' => 'Invalid request.']);
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




public function getToken(Request $request)
{
    $token = $request->header('Authorization');
    $user = User::where('api_token', $token)->first();
    $user_id=$user->id;

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

    // Retrieve orders for the specified user and date
    $order = Order::where('created_at', $date)->first();

    if (!$order) {
        return response()->json(['message' => 'No order found for the specified date and user.'], 404);
    }

    // Format date with time
    $formattedDateTime = $order->created_at->format('Y-m-d\TH:i:s.u\Z');

    // Retrieve pharmaceuticals associated with the order
    $pharmaceuticals = $order->pharmaceuticals;

    return response()->json([
        'order' => [
            'Date' => $formattedDateTime,
            'status' => $order->status,
            'payment' => $order->payment,
            'order_id'=>$order->id
        ],
        'pharmaceuticals' => $pharmaceuticals
    ], 200);
}




 public function retrieveOrders()
    {
        // Retrieve all orders with associated pharmaceuticals and their quantities
        $orders = Order::with('pharmaceuticals')->get();
        return response()->json(['orders' => $orders]);
    }
}
/*{
  "order": {
    "Date": "2023-12-15T14:03:48.000000Z",
    "status": "send",
    "payment": "paid",
    "order_id": 1
  },
  "pharmaceuticals": [
    {
      "id": 1,
      "scientific_name": "rrer",
      "commercial_name": "lew",
      "calssification": "maajed",
      "manufacture_company": "majaed",
      "quantity_available": 1005,
      "expire_date": "2032-01-11",
      "price": 1010,
      "created_at": "2023-12-15T14:03:09.000000Z",
      "updated_at": "2023-12-15T14:16:05.000000Z",
      "pivot": {
        "order_id": 1,
        "pharmaceutical_id": 1,
        "quantity": 5
      }
    },
    {
      "id": 2,
      "scientific_name": "rrer",
      "commercial_name": "lssew",
      "calssification": "maajed",
      "manufacture_company": "majaed",
      "quantity_available": 1007,
      "expire_date": "2032-01-11",
      "price": 1010,
      "created_at": "2023-12-15T14:03:16.000000Z",
      "updated_at": "2023-12-15T14:16:05.000000Z",
      "pivot": {
        "order_id": 1,
        "pharmaceutical_id": 2,
        "quantity": 3
      }
    }
  ]
}
*/
