<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pharmaceutical;

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
            'price' => 'required|integer',
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


    public function quantity(Request $request)
    {
        $id = $request->input('id');
        $number=$request->input('quantity');
        $medicine=Pharmaceutical::where('id',$id)->first();

        
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

        $medicines = Pharmaceutical::where('calssification', $classification)->get();

        return response()->json(
            [

                'medicines' => $medicines
            ]
        );
    }





    public function getAllClass()
    {
        $Classifications = Pharmaceutical::distinct()->pluck('calssification')->toArray();

        return response()->json([

            'classifications' => $Classifications
        ]);
    }
}