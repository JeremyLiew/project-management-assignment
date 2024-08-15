<?php

namespace App\Http\Controllers;

use App\Decorators\BudgetLogDecorator;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::all(); // Removed the 'project' relation
        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        return view('budgets.create'); // Removed the 'projects' variable
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
        ]);

        $budget = Budget::create([
            'total_amount' => $request->total_amount,
        ]);

        $budgetLogger = new BudgetLogDecorator($budget);
        $budgetLogger->logAction('Created', [
            'total_amount' => $budget->total_amount,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget)
    {
        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
        ]);

        $budget->total_amount = $request->total_amount;
        $budget->save();

        $budgetLogger = new BudgetLogDecorator($budget);
        $budgetLogger->logAction('Updated', [
            'total_amount' => $budget->total_amount,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        if ($budget->projects()->exists()) {
            return redirect()->route('budgets.index')->with('error', 'Cannot delete budget; it is currently assigned to projects.');
        }

        $budget->delete();

        $budgetLogger = new BudgetLogDecorator($budget);
        $budgetLogger->logAction('Deleted', [
            'total_amount' => $budget->total_amount,
        ]);
        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }

}
