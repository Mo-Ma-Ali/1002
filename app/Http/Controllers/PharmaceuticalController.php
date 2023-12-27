<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pharmaceutical;
use App\Models\User;

class PharmaceuticalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'scientific_name' => 'required|string|max:255',
            'commercial_name' => 'required|string|unique:pharmaceuticals|max:255',
            'calssification' => 'required|string|max:255',
            'manufacture_company' => 'required|string|max:255',
            'quantity_available' => 'required|integer',
            'expire_date' => 'required|date_format:Y-m-d',
            'price' => 'required',
        ]);
       // $data=$request->input('calssification');
        //$search=Pharmaceutical::where('calssification',$data)->first();
      //  dd($data);
        //if($search)
       // return response()->json(['message'=>'the medicine is already exist'],200);
   // else{
        $model = Pharmaceutical::create($request->all());


        return response()->json([
            'message' => 'item added successfully',
            'midcine' => $model,
            'code' => 201
        ]);
    }


    //This function to insert many items at once
    public function storeMany(Request $request)
{
    $request->validate([
        'pharmaceuticals.*.scientific_name' => 'required|string|max:255',
        'pharmaceuticals.*.commercial_name' => 'required|string|unique:pharmaceuticals|max:255',
        'pharmaceuticals.*.calssification' => 'required|string|max:255',
        'pharmaceuticals.*.manufacture_company' => 'required|string|max:255',
        'pharmaceuticals.*.quantity_available' => 'required|integer',
        'pharmaceuticals.*.expire_date' => 'required|date_format:Y-m-d',
        'pharmaceuticals.*.price' => 'required',
    ]);
    $pharmaceuticalsData = $request->input('pharmaceuticals');

    $models = [];

    foreach ($pharmaceuticalsData as $pharmData) {
        $model = Pharmaceutical::create($pharmData);
        $models[] = $model;
    }

    return response()->json([
        'message' => 'Items added successfully',
        'medicines' => $models,
        'code' => 201
    ]);
}


    public function quantity(Request $request)
    {
        $id = $request->input('id');
        $number=$request->input('quantity');
        $medicine = Pharmaceutical::find($id);


        //check if the medicine is not exist
        if (!$medicine)
        {
            return response()->json(['message'=>'the medicine is not found'],404);
        }


        //if the user want to remove all the quantity
        //$quantity=$medicine->quantity_available;
        if($request->input('quantity')=="all")
        {
            $medicine->quantity_available=0;
            $medicine->save();
            return response()->json(['message'=>'the quntity removed succesfully','medicine'=>$medicine],200);
        }
        //if the user want to add or remove a number of the quantity
        $medicine->quantity_available+=$number;


        //check if the quntity_availble is a negative number
        if($medicine->quantity_available<0)

        return response()->json(['message'=>'the quntity is not alivabel'],400);


        //save the editting
        else{
        $medicine->save();
        return response()->json(['message'=>'the quntity added succesfully','medicine'=>$medicine],200);
    }
    }


//     public function quantity(Request $request)
// {
//     $id = $request->input('id');
//     $quantity = $request->input('quantity');

//     // Retrieve the medicine
//     $medicine = Pharmaceutical::find($id);

//     // Check if the medicine is not found
//     if (!$medicine) {
//         return response()->json(['message' => 'The medicine is not found'], 404);
//     }

//     // If the user wants to remove all the quantity
//     if ($quantity === "all") {
//         $medicine->quantity_available = 0;
//     } else {
//         // If the user wants to add or remove a specific number of quantity
//         $medicine->quantity_available += $quantity;

//         // Check if the quantity_available is a negative number
//         if ($medicine->quantity_available < 0) {
//             return response()->json(['message' => 'The quantity is not available'], 400);
//         }
//     }

//     // Save the changes
//     $medicine->save();

//     return response()->json(['message' => 'The quantity has been updated successfully', 'medicine' => $medicine], 200);
// }




    public function serch(Request $request)
    {
        $query = $request->input('calssification');

        $results = Pharmaceutical::where('calssification', 'LIKE', "%{$query}%")->get();
        if($results->isEmpty())
            return response()->json(['message'=>'the calssification does not found'],404);
        $calssification = $results->pluck('calssification')->unique(); // Get all unique 'calssification' fields from the results
        return response()->json(['the results of the calssification:'=> $calssification->values()],200);
    }




    public function serchCompany(Request $request)
    {
        $query = $request->input('commercial_name');

        $results = Pharmaceutical::where('commercial_name', 'LIKE', "%{$query}%")->get();
        if($results->isEmpty())
            return response()->json(['message'=>'the commercial name does not found'],404);

        return response()->json(['the results of the commercial name:' => $results],200);
    }




    // public function getByCalss($calssification)
    // {
    //  $serch = Pharmaceutical::where('calssification',$calssification)->get();
    //  //dd($serch);
    //  $results=[];
    //  foreach($serch as $data)
    //  {
    //     $results=$data;
    //  }
    //  return response()->json($results);
    // }




    public function getTheClass(Request $request)
    {
        $classification = $request->input('calssification');
        $token = $request->header('Authorization');
        $user = User::where('api_token', $token)->first();

        // Retrieve medicines for the specified classification
        $medicines = Pharmaceutical::where('calssification', $classification)->get();

        // Retrieve the favorite pharmaceuticals for the user
        $userFavorites = $user->favorites->pluck('id')->toArray();

        // Map each medicine to include a flag indicating whether it's a favorite
        $medicinesWithFavorites = $medicines->map(function ($medicine) use ($userFavorites) {
            $medicineArray = $medicine->toArray();
            $medicineArray['is_favorite'] = in_array($medicine->id, $userFavorites);
            return $medicineArray;
        });

        return response()->json(['medicines' => $medicinesWithFavorites], 200);
    }







    public function getAllClass()
    {

        $Classifications = Pharmaceutical::distinct()->pluck('calssification')->toArray();

        return response()->json([

            'classifications' => $Classifications
        ]);
    }
}
