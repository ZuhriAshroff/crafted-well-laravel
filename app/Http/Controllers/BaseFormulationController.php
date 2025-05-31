<?php

namespace App\Http\Controllers;

use App\Models\BaseFormulation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BaseFormulationController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {
        $this->middleware(['auth', 'active.user']);
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of base formulations
     */
    public function index(Request $request): View
    {
        $query = BaseFormulation::with(['creator']);

        // Apply filters
        if ($request->category) {
            $query->byCategory($request->category);
        }

        if ($request->skin_type) {
            $query->compatibleWithSkinType($request->skin_type);
        }

        if ($request->active_only !== 'false') {
            $query->active();
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('base_name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $formulations = $query->orderBy('base_name')->paginate(15);

        return view('base-formulations.index', [
            'formulations' => $formulations,
            'categories' => BaseFormulation::getCategoryOptions(),
            'skinTypes' => BaseFormulation::getSkinTypeOptions(),
            'currentFilters' => [
                'category' => $request->category,
                'skin_type' => $request->skin_type,
                'search' => $request->search,
                'active_only' => $request->active_only !== 'false',
            ]
        ]);
    }

    /**
     * Display the specified base formulation
     */
    public function show($baseFormulationId): View
    {
        $baseFormulation = BaseFormulation::with(['creator', 'customProducts'])
            ->findOrFail($baseFormulationId);

        $summary = $baseFormulation->getFormulationSummary();
        $summary['concentration_analysis'] = $baseFormulation->calculateTotalConcentration();
        $summary['usage_count'] = $baseFormulation->customProducts()->count();

        return view('base-formulations.show', [
            'baseFormulation' => $baseFormulation,
            'formulationSummary' => $summary,
            'recentUsage' => $baseFormulation->customProducts()
                ->with('user')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * Admin Routes
     */

    /**
     * Show the form for creating a new base formulation (Admin only)
     */
    public function create(): View
    {
        return view('admin.base-formulations.create', [
            'categories' => BaseFormulation::getCategoryOptions(),
            'skinTypes' => BaseFormulation::getSkinTypeOptions(),
            'universalIngredients' => BaseFormulation::getUniversalIngredientOptions(),
        ]);
    }

    /**
     * Store a newly created base formulation (Admin only)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(BaseFormulation::validationRules());

        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();

            $baseFormulation = BaseFormulation::createFormulation($data);

            return redirect()->route('admin.base-formulations.show', $baseFormulation)
                ->with('success', 'Base formulation created successfully.');

        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create base formulation. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified base formulation (Admin only)
     */
    public function edit($baseFormulationId): View
    {
        $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

        return view('admin.base-formulations.edit', [
            'baseFormulation' => $baseFormulation,
            'categories' => BaseFormulation::getCategoryOptions(),
            'skinTypes' => BaseFormulation::getSkinTypeOptions(),
            'universalIngredients' => BaseFormulation::getUniversalIngredientOptions(),
        ]);
    }

    /**
     * Update the specified base formulation (Admin only)
     */
    public function update(Request $request, $baseFormulationId): RedirectResponse
    {
        $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

        $request->validate(BaseFormulation::validationRules(true));

        try {
            $data = $request->validated();
            $baseFormulation->updateFormulation($data);

            return redirect()->route('admin.base-formulations.show', $baseFormulation)
                ->with('success', 'Base formulation updated successfully.');

        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update base formulation. Please try again.');
        }
    }

    /**
     * Remove the specified base formulation (Admin only)
     */
    public function destroy($baseFormulationId): RedirectResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            // Check if formulation is used in custom products
            $usageCount = $baseFormulation->customProducts()->count();
            if ($usageCount > 0) {
                return back()->with('error', "Cannot delete base formulation. It is used in {$usageCount} custom products.");
            }

            $baseFormulation->delete();

            return redirect()->route('admin.base-formulations.index')
                ->with('success', 'Base formulation deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete base formulation. Please try again.');
        }
    }

    /**
     * Deactivate base formulation (Admin only)
     */
    public function deactivate($baseFormulationId): RedirectResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);
            $baseFormulation->deactivate();

            return back()->with('success', 'Base formulation deactivated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate base formulation. Please try again.');
        }
    }

    /**
     * Show clone form (Admin only)
     */
    public function showCloneForm($baseFormulationId): View
    {
        $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

        return view('admin.base-formulations.clone', [
            'baseFormulation' => $baseFormulation,
        ]);
    }

    /**
     * Clone base formulation (Admin only)
     */
    public function clone(Request $request, $baseFormulationId): RedirectResponse
    {
        $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

        $request->validate([
            'new_name' => 'required|string|max:100|unique:BaseFormulation,base_name',
            'description' => 'sometimes|string|max:500'
        ]);

        try {
            $clonedFormulation = $baseFormulation->cloneFormulation(
                $request->input('new_name'),
                $request->input('description')
            );

            return redirect()->route('admin.base-formulations.show', $clonedFormulation)
                ->with('success', 'Base formulation cloned successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to clone base formulation. Please try again.');
        }
    }

    /**
     * Base formulation analytics dashboard (Admin only)
     */
    public function analytics(): View
    {
        $analytics = BaseFormulation::getFormulationStats();

        // Add additional analytics for the view
        $analytics['recent_activity'] = BaseFormulation::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $analytics['usage_statistics'] = $this->getUsageStatistics();

        return view('admin.base-formulations.analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Helper method for usage statistics
     */
    private function getUsageStatistics(): array
    {
        $formulations = BaseFormulation::withCount('customProducts')->get();
        
        return [
            'most_used_formulations' => $formulations->sortByDesc('custom_products_count')->take(5),
            'unused_formulations' => $formulations->where('custom_products_count', 0)->count(),
            'average_usage_per_formulation' => $formulations->avg('custom_products_count'),
        ];
    }
}
