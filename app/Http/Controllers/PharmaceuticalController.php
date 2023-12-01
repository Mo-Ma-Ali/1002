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

        $model = Pharmaceutical::create($request->all());

        return response()->json([
            'message' => 'User logged in successfully',
            'midcine'=>$model,
            'code'=> 201]);
    }

    public function serch(Request $request)
    {
        
        $query = $request->input('calssification');

        $results = Pharmaceutical::where('calssification', 'LIKE', "%{$query}%")->get();

        return response()->json($results);
    }

    public function serchCompany(Request $request)
    {
        
        $query = $request->input('commercial_name');

        $results = Pharmaceutical::where('commercial_name', 'LIKE', "%{$query}%")->get();

        return response()->json($results);
    }

    public function getByCalss($calssification) 
    {
     $serch = Pharmaceutical::where('calssification',$calssification)->get();
     //dd($serch);
     $results=[];
     foreach($serch as $data)
     {
        $results=$data;
     }
     return response()->json($results);
    }

    public function getAllClass()
    {
        $Classifications = Pharmaceutical::distinct()->pluck('calssification')->toArray();

         return response()->json([
            
            'classifications'=> $Classifications]);
    }
}
