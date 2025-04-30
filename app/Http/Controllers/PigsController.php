<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Pigs;


class PigsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all pigs from the database, ordered by newest first
        $pigs = Pigs::orderBy('created_at', 'desc')->get();

        // Pass the pigs data to the Vue page through Inertia
        return inertia('Admin/Pigs', [
            'pageTitleTextForBreedcrumbs' => 'All Pigs', //This sends a prop called pageTitleTextForBreedcrumbs to the Vue page (Pigs.vue) and also AdminAuthenticatedLayout.vue will be able to access it since Pigs is under it.
            'pigs' => $pigs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate incoming request
            $validated = $request->validate([
                'penNumber' => 'required|integer',
                'dateBought' => 'required|date',
                'cost' => 'required|numeric',
                'breed' => 'nullable|string',
                'expectedSellDate' => 'required|date',
                'startingWeight' => 'required|string', // Change to startingWeight for consistency
            ]);

            // Save to database
            $createdPigs = Pigs::create([
                'pen_number' => $validated['penNumber'],
                'date_bought' => $validated['dateBought'],
                'cost' => $validated['cost'],
                'breed' => $validated['breed'],
                'expected_sell_date' => $validated['expectedSellDate'],
                'starting_weight' => $validated['startingWeight'], // Ensure it matches here too
            ]);

            // Generate pig_id based on the auto-increment ID
            $createdPigs->pig_id = 'PIG-' . str_pad($createdPigs->id, 2, '0', STR_PAD_LEFT);

            //put also the starting wieght sa baboy sa current kay bag-o paman
            $createdPigs->current_weight = $createdPigs->starting_weight;
            $createdPigs->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pig added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        // try {
        //     DB::beginTransaction(); // Start the DB transaction

        //     // Validate incoming request
        // $validated = $request->validate([
        //     'penNumber' => 'required|integer',
        //     'dateBought' => 'required|date',
        //     'cost' => 'required|numeric',
        //     'breed' => 'nullable|string',
        //     'expectedSellDate' => 'required|date',
        //     'startingWeight' => 'required|string', // Change to startingWeight for consistency
        // ]);

        // // Save to database
        // $createdPigs = Pigs::create([
        //     'pen_number' => $validated['penNumber'],
        //     'date_bought' => $validated['dateBought'],
        //     'cost' => $validated['cost'],
        //     'breed' => $validated['breed'],
        //     'expected_sell_date' => $validated['expectedSellDate'],
        //     'starting_weight' => $validated['startingWeight'], // Ensure it matches here too
        // ]);

        //     DB::commit(); // Commit the transaction

        //     return redirect()->back()->with('message', 'Pig created successfully!');
        // } catch (\Exception $e) {
        //     DB::rollBack(); // Roll back on error

        //     return redirect()->back()->withErrors([
        //         'error' => $e->getMessage(),
        //     ]);
        // }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pig = Pigs::findOrFail($id);
        $pig->delete();

        return redirect()->route('Admin-Pigs.index')->with('success', 'Pig deleted successfully!');
    }
}
