<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Expenses;
use App\Models\Batch;
use App\Models\Pigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all pigs from the database, ordered by newest first
        $pigs = Pigs::orderBy('created_at', 'desc')->get();
        $batches = Batch::orderBy('created_at', 'desc')->get();

        return inertia('Admin/Expenses', [
            'pageTitleTextForBreedcrumbs' => 'Expense',
            'pigs' => $pigs,
            'batches' => $batches,
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
            // Validate input
            $validated = $request->validate([
                'pig_id' => 'nullable|exists:pigs,id',
                'batch_id' => 'required|exists:batches,id',
                'category' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'is_recurring' => 'nullable|boolean',
                'recurrence_type' => 'nullable|in:daily,weekly,monthly',
                'receipt' => 'nullable|image|max:2048',
            ]);

            // Handle receipt upload save in public/receipt and to the db
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receipt = $request->file('receipt');
                $fileName = time() . '_' . $receipt->getClientOriginalName();
                $receipt->move(public_path('receipt'), $fileName);
                $receiptPath = 'receipt/' . $fileName;
            }

            
            // Save expense
            $expense = Expenses::create([
                'pig_id' => $validated['pig_id'] ?? null,
                'batch_id' => $validated['batch_id'] ?? null,
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'is_recurring' => $validated['is_recurring'] ?? false,
                'recurrence_type' => $validated['recurrence_type'] ?? null,
                'receipt_path' => $receiptPath, // fixed undefined variable
            ]);

        
            DB::commit();

            return redirect()->back()->with('success', 'Expense created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expenses $expenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expenses $expenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expenses $expenses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expenses $expenses)
    {
        //
    }
}
