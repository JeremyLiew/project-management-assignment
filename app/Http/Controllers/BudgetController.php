<?php

namespace App\Http\Controllers;

use App\Decorators\BudgetLogDecorator;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $budgetLogger = new BudgetLogDecorator(null, $request);
        try {
            $budgets = Budget::with('expenses')->get(); // Eager load expenses
            // Calculate extra costs for each budget
            foreach ($budgets as $budget) {
                $totalExpenses = $budget->expenses->sum('amount');
                $budget->extra_cost = $budget->total_amount - $totalExpenses;
            }
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
                'expenses.*.amount' => 'required|numeric|min:0',
                'expenses.*.description' => 'required|string|max:255',
            ]);
    
            // Create the budget
            $budget = Budget::create([
                'total_amount' => $request->total_amount,
            ]);
    
        // Add expenses to the budget
        if ($request->has('expenses')) {
            foreach ($request->expenses as $expense) {
                $budget->expenses()->create([
                    'amount' => $expense['amount'],
                    'description' => $expense['description'],
                    'expense_category_id' => 1, // Assuming a default or pre-selected category
                ]);
            }
        }
    
            $budgetLogger = new BudgetLogDecorator($budget, $request);
            $budgetLogger->logAction('Created with expenses', [
                'total_amount' => $budget->total_amount,
            ]);
    
            return redirect()->route('budgets.index')->with('success', 'Budget and expenses created successfully.');
        } catch (\Exception $e) {
            $budgetLogger = new BudgetLogDecorator(null, $request);
            $budgetLogger->logAction('Failed to Create Budget with expenses', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create budget and expenses.');
        }
    }

    public function edit(Budget $budget)
    {
        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        try {
            // Validate the request
            $request->validate([
                'total_amount' => 'required|numeric|min:0',
                'expenses.*.amount' => 'required|numeric|min:0',
                'expenses.*.description' => 'required|string|max:255',
            ]);
    
            // Update the budget
            $budget->update([
                'total_amount' => $request->total_amount,
            ]);
    
            // Get existing expense IDs
            $existingExpenseIds = $budget->expenses->pluck('id')->toArray();
    
            // Loop through the expenses from the request
            $updatedExpenseIds = [];
            if ($request->has('expenses')) {
                foreach ($request->expenses as $expenseData) {
                    // If the expense ID is present, update it
                    if (isset($expenseData['id'])) {
                        $expense = Expense::find($expenseData['id']);
                        if ($expense && $expense->budget_id == $budget->id) {
                            $expense->update([
                                'amount' => $expenseData['amount'],
                                'description' => $expenseData['description'],
                            ]);
                            $updatedExpenseIds[] = $expense->id;
                        }
                    } else {
                        // If it's a new expense, create it
                        $budget->expenses()->create([
                            'amount' => $expenseData['amount'],
                            'description' => $expenseData['description'],
                            'expense_category_id' => 1, // Default or selected category
                        ]);
                    }
                }
            }
    
            // Delete expenses that are not in the updated list
            $expensesToDelete = array_diff($existingExpenseIds, $updatedExpenseIds);
            if (!empty($expensesToDelete)) {
                Expense::whereIn('id', $expensesToDelete)->delete();
            }
    
            return redirect()->route('budgets.index')->with('success', 'Budget and expenses updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update budget and expenses.');
        }
    }
    
    

    public function destroy(Budget $budget, Request $request)
    {
        try {
            // Check if the budget is associated with projects
            if ($budget->projects()->exists()) {
                $budgetLogger = new BudgetLogDecorator(null, $request);
                $budgetLogger->logAction('Failed to Delete Budget', ['error' => 'Attempts to delete budget while it is currently assigned to projects']);
                return redirect()->route('budgets.index')->with('error', 'Cannot delete budget; it is currently assigned to projects.');
            }
    
            // Delete all associated expenses
            $budget->expenses()->delete();
    
            // Delete the budget
            $budget->delete();
    
            $budgetLogger = new BudgetLogDecorator(null, $request);
            $budgetLogger->logAction('Deleted', [
                'total_amount' => $budget->total_amount,
            ]);
    
            return redirect()->route('budgets.index')->with('success', 'Budget and associated expenses deleted successfully.');
        } catch (\Exception $e) {
            $budgetLogger = new BudgetLogDecorator(null, $request);
            $budgetLogger->logAction('Failed to Delete Budget', ['error' => $e->getMessage()]);
            return redirect()->route('budgets.index')->with('error', 'Failed to delete budget.');
        }
    }
    

}
