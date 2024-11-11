<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CountryController extends Controller
{
    public function index(){
        View::share('title','Country List');
        return view('panel.country.list');
    }

    public function fetchCountries(){
        $countries=Country::query()->latest()->get();
        return response()->json($countries);
    }

    public function storeCountries(Request $request){


        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new country
        try {
            $country = Country::create([
                'name' => $validatedData['name'],
            ]);
            // Return a JSON response
            return response()->json(['message' => 'Country added successfully!', 'country' => $country], 201);
        }catch (\PDOException $e){
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to add the country: ' . $e->getMessage()
                    : 'Failed to add the country due to a server error.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $country = Country::findOrFail($id);
        return response()->json(['country' => $country]);
    }

    // Update method to edit country data
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        if (!$country){
            return response()->json(['message' => 'Country id can not found to updated successfully!']);
        }
        try {
            $country->update($request->validate(['name' => 'required|string|max:255']));
            return response()->json(['message' => 'Country updated successfully!', 'country' => $country]);
        }catch (\PDOException $e){
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to add the country: ' . $e->getMessage()
                    : 'Failed to add the country due to a server error.'
            ], 500);
        }


    }

    // Destroy method to delete country data
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        if (!$country){
            return response()->json(['message' => 'Country id can not found to delete!']);
        }
        try {
            $country->delete();
            return response()->json(['message' => 'Country deleted successfully!']);
        }catch (\PDOException $e){
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to add the country: ' . $e->getMessage()
                    : 'Failed to add the country due to a server error.'
            ], 500);
        }

    }
}
