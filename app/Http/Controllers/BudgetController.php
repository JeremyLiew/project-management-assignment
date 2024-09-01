<?php

namespace App\Http\Controllers;

use App\Decorators\BudgetLogDecorator;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgetLogger = new BudgetLogDecorator(null);
        try {
            $budgets = Budget::all();
            $budgetLogger->logAction('Fetched Budgets Data', ['status' => '200']);
            return view('budgets.index', compact('budgets'));
        } catch (\Exception $e) {
            $budgetLogger->logAction('Failed to Fetch Budgets', ['error' => $e->getMessage()]);
            return redirect()->route('budgets.index')->with('error', 'Failed to fetch budgets.');
        }
    }

    public function create()
    {
        return view('budgets.create');
    }

    public function store(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            $budgetLogger = new BudgetLogDecorator(null);
            $budgetLogger->logAction('Failed to Create Budget', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create budget.');
        }
    }

    public function edit(Budget $budget)
    {
        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        try {
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
        } catch (\Exception $e) {
            $budgetLogger = new BudgetLogDecorator(null);
            $budgetLogger->logAction('Failed to Update Budget', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update budget.');
        }
    }

    public function destroy(Budget $budget)
    {
        try {
            if ($budget->projects()->exists()) {
                $budgetLogger = new BudgetLogDecorator(null);
                $budgetLogger->logAction('Failed to Delete Budget', ['error' => 'Attempts to delete budget while it is currently assigned to projects']);
                return redirect()->route('budgets.index')->with('error', 'Cannot delete budget; it is currently assigned to projects.');
            }

            $budget->delete();

            $budgetLogger = new BudgetLogDecorator($budget);
            $budgetLogger->logAction('Deleted', [
                'total_amount' => $budget->total_amount,
            ]);

            return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
        } catch (\Exception $e) {
            $budgetLogger = new BudgetLogDecorator(null);
            $budgetLogger->logAction('Failed to Delete Budget', ['error' => $e->getMessage()]);
            return redirect()->route('budgets.index')->with('error', 'Failed to delete budget.');
        }
    }

}
