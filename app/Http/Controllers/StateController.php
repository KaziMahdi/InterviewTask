<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class StateController extends Controller
{
    public function index()
    {
        $countries = Country::query()->get();
        View::share('title', 'State List');
        return view('panel.state.list', compact('countries'));
    }

    public function fetchStates()
    {
        $states = DB::select(
            'SELECT states.id, states.name AS state_name, countries.name AS country_name
         FROM states
         LEFT JOIN countries ON states.country_id = countries.id
         ORDER BY states.created_at DESC'
        );

        return response()->json($states);
    }

    public function storeStates(Request $request)
    {


        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        try {
            // Insert the new state into the database
            DB::insert(
                'INSERT INTO states (name, country_id, created_at, updated_at)
             VALUES (?, ?, NOW(), NOW())',
                [$validatedData['name'], $validatedData['country_id']]
            );

            // Fetch the newly created state along with its country name
            $state = DB::selectOne(
                'SELECT states.id, states.name AS name, countries.name AS country_name
             FROM states
             JOIN countries ON states.country_id = countries.id
             WHERE states.id = LAST_INSERT_ID()'
            );

            // Return the JSON response with the state and related country name
            return response()->json([
                'message' => 'State added successfully!',
                'state' => $state
            ], 201);

        } catch (\PDOException $e) {
            // Log the error and return a user-friendly message
            Log::error($e);
            return response()->json([
                'message' => config('app.debug')
                    ? 'Failed to add the state: ' . $e->getMessage()
                    : 'Failed to add the state due to a server error.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $state = DB::selectOne(
            'SELECT states.id, states.name AS name, countries.name AS country_name, countries.id AS country_id
         FROM states
         JOIN countries ON states.country_id = countries.id
         WHERE states.id = ?', [$id]
        );

        $countries = DB::select('SELECT id, name FROM countries');

        return response()->json([
            'state' => $state,
            'countries' => $countries
        ]);
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        try {
            DB::update(
                'UPDATE states
             SET name = ?, country_id = ?, updated_at = NOW()
             WHERE id = ?',
                [$validatedData['name'], $validatedData['country_id'], $id]
            );

            // Fetch the updated state with the country name
            $state = DB::selectOne(
                'SELECT states.id, states.name AS name, countries.name AS country_name
             FROM states
             JOIN countries ON states.country_id = countries.id
             WHERE states.id = ?', [$id]
            );

            return response()->json([
                'message' => 'State updated successfully!',
                'state' => $state
            ], 200);

        } catch (\PDOException $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Failed to update the state: ' . (config('app.debug') ? $e->getMessage() : 'Server error.')
            ], 500);
        }

    }

    // Destroy method to delete country data
    public function destroy($id)
    {
        try {
            DB::delete('DELETE FROM states WHERE id = ?', [$id]);

            return response()->json(['message' => 'State deleted successfully!'], 200);

        } catch (\PDOException $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Failed to delete the state: ' . (config('app.debug') ? $e->getMessage() : 'Server error.')
            ], 500);
        }

    }
}
