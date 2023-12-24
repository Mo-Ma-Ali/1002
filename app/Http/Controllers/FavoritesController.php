<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{




    public function addToFavorites(Request $request, $pharmaceuticalId)
{
    $token = $request->header('Authorization');
    $user = User::where('api_token', $token)->first(); // Assuming you are using Laravel's built-in authentication

    // Check if the pharmaceutical is already in the user's favorites
    if ($user->favorites()->where('pharmaceutical_id', $pharmaceuticalId)->exists()) {
        $user->favorites()->detach($pharmaceuticalId);
        return response()->json(['message' => 'Pharmaceutical removed from favorites successfully']);
    }

    // Attach the pharmaceutical to the user's favorites
    $user->favorites()->attach($pharmaceuticalId);

    return response()->json(['message' => 'Pharmaceutical added to favorites successfully']);
}



public function getFavorites(Request $request)
{
    // Get the authenticated user
    $token = $request->header('Authorization');
    $user = User::where('api_token', $token)->first();

    // Retrieve all favorite pharmaceuticals for the user
    $favorites = $user->favorites;

    $favoritesWithIsFavorite = $favorites->map(function ($favorite) {
        $favoriteArray = $favorite->toArray();
        $favoriteArray['is_favorite'] = true;
        return $favoriteArray;
    });

    return response()->json(['favorites' => $favoritesWithIsFavorite]);
}



// public function removeFavorite(Request $request,$pharmaceuticalId)
//     {
//         $token = $request->header('Authorization');
//         $user = User::where('api_token', $token)->first();
//         //Check if the item exist
//         if ($user->favorites()->where('pharmaceutical_id', $pharmaceuticalId)->exists()) {
//              // Detach the pharmaceutical from the user's favorites
//             $user->favorites()->detach($pharmaceuticalId);
//             return response()->json(['message' => 'Pharmaceutical removed from favorites successfully']);
//         }
//        //If the item was not exist
//         return response()->json(['message' => 'Pharmaceutical does not exsit']);

//     }
}
