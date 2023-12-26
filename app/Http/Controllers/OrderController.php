<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pharmaceutical;
use App\Models\User;
use PHPUnit\TextUI\Configuration\Merger;
use Symfony\Component\Console\Input\Input;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    $totale_price=$request->header('Price');

        $order= Order::create([
            'user_id'=>$user_id,
            'totale_price'=>$totale_price
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
    $order = Order::findOrFail($orderId);

    if ($order->status == "in process" && $newStatus == "cancel") {
        $order->update(['status' => $newStatus]);
        return response()->json(['message' => 'Order canceled.']);
    }
    elseif ($order->status == "in process" && $newStatus == "in preparation") {
        $order->update(['status' => $newStatus]);
        return response()->json(['message' => 'Order status In preparation.']);
    }

        // Update the status of the order to "send"


    elseif (($order->status == "in preparation" && $newStatus == "send")||($order->status == "in process" && $newStatus == "send")) {
        $pharmaceuticals = $order->pharmaceuticals()->withPivot('quantity')->get();
        foreach ($pharmaceuticals as $pharmaceutical) {
            $pharmaceuticalId = $pharmaceutical->id;
            $pivotQuantity = $pharmaceutical->pivot->quantity;
            Pharmaceutical::where('id', $pharmaceuticalId)
                ->decrement('quantity_available', $pivotQuantity);
        }
        $order->update(['status' => $newStatus]);
        return response()->json(['message' => 'Order status: send.']);
    }
    else {
        return response()->json(['message' => 'Invalid operation.']);
    }
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
            'order_id'=>$order->id,
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







    public function salesReport(Request $request)
{
    // Validate the request data
    $request->validate([
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer',
    ]);

    // Get the selected month and year from the request
    $selectedMonth = $request->input('month');
    $selectedYear = $request->input('year');

    // Calculate the start and end date of the selected month and year
    $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();

    // Retrieve orders and calculate total sales for the selected month and year
    $orders = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', '=', 'send')
        ->get();

    $totalSales = $orders->sum('totale_price');

    return response()->json([
        'total_sales' => $totalSales,
        'orders' => $orders,
    ]);
}




public function salesReportPharamcy(Request $request)
{
    // Validate the request data
    $request->validate([
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer',
    ]);
    $token = $request->header('Authorization');
    $user = User::where('api_token', $token)->first();

    // Get the selected month and year from the request
    $selectedMonth = $request->input('month');
    $selectedYear = $request->input('year');

    // Calculate the start and end date of the selected month and year
    $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();

    // Retrieve orders and calculate total sales for the selected month and year
    $orders = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('user_id',$user->id)
        ->where('status','send')
        // ->where('payment','paid')
        ->get();

    $totalSales = $orders->sum('totale_price');

    return response()->json([
        'total_paid' => $totalSales,
        'orders' => $orders,
    ]);
}





public function quantityReport(Request $request)
{
    $request->validate([
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer',
    ]);

    // Get the selected month and year from the request
    $selectedMonth = $request->input('month');
    $selectedYear = $request->input('year');

    // Calculate the start and end date of the selected month and year
    $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();

    // Retrieve orders and calculate total quantities for the selected month and year
    $pharmaceuticals = DB::table('order_pharmaceutical')
        ->join('pharmaceuticals', 'order_pharmaceutical.pharmaceutical_id', '=', 'pharmaceuticals.id')
        ->join('orders', 'order_pharmaceutical.order_id', '=', 'orders.id')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->where('orders.status', '=', 'send')
        ->select('pharmaceuticals.id', 'pharmaceuticals.commercial_name', DB::raw('SUM(order_pharmaceutical.quantity) as total_ordered_quantity'))
        ->groupBy('pharmaceuticals.id', 'pharmaceuticals.commercial_name')
        ->get();

    return response()->json(['data' => $pharmaceuticals]);
}
}
//quantity
// {
//     "data": [
//       {
//         "id": 1,
//         "commercial_name": "ssd",
//         "total_ordered_quantity": "19"
//       },
//       {
//         "id": 2,
//         "commercial_name": "hdd",
//         "total_ordered_quantity": "303"
//       }
//     ]
//   }


//total sales
// {
//     "total_sales": 1110,
//     "orders": [
//       {
//         "id": 1,
//         "user_id": 1,
//         "status": "send",
//         "payment": "unpaid",
//         "totale_price": "222.00",
//         "created_at": "2023-12-26T08:13:07.000000Z",
//         "updated_at": "2023-12-26T08:13:07.000000Z"
//       },
//       {
//         "id": 2,
//         "user_id": 1,
//         "status": "send",
//         "payment": "unpaid",
//         "totale_price": "222.00",
//         "created_at": "2023-12-26T08:13:41.000000Z",
//         "updated_at": "2023-12-26T08:13:41.000000Z"
//       },
//       {
//         "id": 3,
//         "user_id": 1,
//         "status": "send",
//         "payment": "unpaid",
//         "totale_price": "222.00",
//         "created_at": "2023-12-26T08:14:32.000000Z",
//         "updated_at": "2023-12-26T08:14:32.000000Z"
//       },
//       {
//         "id": 7,
//         "user_id": 1,
//         "status": "send",
//         "payment": "unpaid",
//         "totale_price": "222.00",
//         "created_at": "2023-12-26T13:31:37.000000Z",
//         "updated_at": "2023-12-26T13:31:37.000000Z"
//       },
//       {
//         "id": 8,
//         "user_id": 1,
//         "status": "send",
//         "payment": "unpaid",
//         "totale_price": "222.00",
//         "created_at": "2023-12-26T13:31:48.000000Z",
//         "updated_at": "2023-12-26T13:31:48.000000Z"
//       }
//     ]
//   }
