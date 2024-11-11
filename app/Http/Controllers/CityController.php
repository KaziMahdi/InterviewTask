<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;


class CityController extends Controller
{
   public function index(){
       $states=State::query()->get();
       View::share('title','City List');
       return view('panel.city.list',compact('states'));
   }

    public function fetchCities(){
        $cities=City::with('state')->latest()->get();
        return response()->json($cities);
    }

    public function storeCities(Request $request){


        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer|exists:states,id', // Add exists rule to ensure the state_id is valid
        ]);

        try {
            // Create a new state
            $city = City::create([
                'name' => $validatedData['name'],
                'state_id' => $validatedData['state_id'],
            ]);

            // Load the 'state' relationship so we can include the country name in the response
            $city->load('state');

            // Return a JSON response with the state and related country name
            return response()->json([
                'message' => 'City added successfully!',
                'city' => $city
            ], 201);

        } catch (\PDOException $e) {
            // Log the error and return a user-friendly message
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to add the city: ' . $e->getMessage()
                    : 'Failed to add the city due to a server error.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $city = City::with('state')->findOrFail($id);
        $states = State::all();
        return response()->json(['city' => $city,'states'=>$states]);
    }

    // Update method to edit country data
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        if (!$city){
            return response()->json(['message' => 'City id can not found to updated successfully!']);
        }
        try {
            $city->update($request->validate([
                'name' => 'required|string|max:255',
                'state_id'=>'required|integer|exists:states,id'
            ]));
            $city->load('state');

            // Return a JSON response with the state and related country name
            return response()->json([
                'message' => 'State update successfully!',
                'city' => $city,
                'state_name'=>$city->state->name
            ], 201);
        }catch (\PDOException $e){
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to update the city: ' . $e->getMessage()
                    : 'Failed to update the city due to a server error.'
            ], 500);
        }

    }

    // Destroy method to delete country data
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        if (!$city){
            return response()->json(['message' => 'City id can not found to delete!']);
        }
        try {
            $city->delete();
            return response()->json(['message' => 'City deleted successfully!']);
        }catch (\PDOException $e){
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to delete the city: ' . $e->getMessage()
                    : 'Failed to delete the city due to a server error.'
            ], 500);
        }

    }
}
