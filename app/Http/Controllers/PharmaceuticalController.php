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
            'commercial_nmae' => 'required|string|unique:pharmaceuticals|max:255',
            'calssification' => 'required|string|max:255',
            'manufacture_company' => 'required|string|max:255',
            'quantity_available' => 'required|integer',
            'expire_date' => 'required|date_format:Y-m-d',
            'price' => 'required|integer',
        ]);

        $model = Pharmaceutical::create($request->all());

        return response()->json($model, 201);
    }
}
