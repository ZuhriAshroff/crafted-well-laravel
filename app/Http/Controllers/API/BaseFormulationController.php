<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BaseFormulation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BaseFormulationController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('active.user');
        $this->middleware('admin')->except(['index', 'show', 'getCompatibleFormulations', 'getRecommendations']);
    }

    /**
     * Display a listing of base formulations.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 20), 50);
            $category = $request->get('category');
            $skinType = $request->get('skin_type');
            $activeOnly = $request->get('active_only', true);

            $query = BaseFormulation::with(['creator:user_id,first_name,last_name']);

            if ($activeOnly) {
                $query->active();
            }

            if ($category) {
                $query->byCategory($category);
            }

            if ($skinType) {
                $query->compatibleWithSkinType($skinType);
            }

            $formulations = $query->orderBy('base_name')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedFormulations = $formulations->getCollection()->map(function ($formulation) {
                return $formulation->getFormulationSummary();
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedFormulations,
                'pagination' => [
                    'current_page' => $formulations->currentPage(),
                    'per_page' => $formulations->perPage(),
                    'total' => $formulations->total(),
                    'last_page' => $formulations->lastPage(),
                    'has_more' => $formulations->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching base formulations: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch base formulations'
            ], 500);
        }
    }

    /**
     * Store a newly created base formulation (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), BaseFormulation::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['created_by'] = $request->user()->user_id;

            // Create base formulation with validation
            $baseFormulation = BaseFormulation::createFormulation($data);

            // Log formulation creation for analytics
            $this->logFormulationAnalytics($request->user(), 'base_formulation_created', [
                'base_formulation_id' => $baseFormulation->base_formulation_id,
                'category' => $baseFormulation->formulation_category,
                'ingredients_count' => count($baseFormulation->universal_ingredients),
                'skin_types' => $baseFormulation->skin_type_compatibility,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Base formulation created successfully',
                'data' => $baseFormulation->getFormulationSummary()
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error creating base formulation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create base formulation'
            ], 500);
        }
    }

    /**
     * Display the specified base formulation.
     */
    public function show($baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::with(['creator'])->findOrFail($baseFormulationId);

            // Log formulation view for analytics
            $this->logFormulationAnalytics(request()->user(), 'base_formulation_viewed', [
                'base_formulation_id' => $baseFormulation->base_formulation_id,
            ]);

            $summary = $baseFormulation->getFormulationSummary();
            $summary['concentration_analysis'] = $baseFormulation->calculateTotalConcentration();
            $summary['usage_count'] = $baseFormulation->customProducts()->count();

            return response()->json([
                'status' => 'success',
                'data' => $summary
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching base formulation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch base formulation'
            ], 500);
        }
    }

    /**
     * Update the specified base formulation (Admin only).
     */
    public function update(Request $request, $baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            // Validate update data
            $validator = Validator::make($request->all(), BaseFormulation::validationRules(true));

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Update formulation with validation
            $baseFormulation->updateFormulation($data);

            // Log formulation update
            $this->logFormulationAnalytics($request->user(), 'base_formulation_updated', [
                'base_formulation_id' => $baseFormulation->base_formulation_id,
                'updated_fields' => array_keys($data),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Base formulation updated successfully',
                'data' => $baseFormulation->fresh()->getFormulationSummary()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error updating base formulation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update base formulation'
            ], 500);
        }
    }

    /**
     * Remove the specified base formulation (Admin only).
     */
    public function destroy($baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            // Check if formulation is used in custom products
            $usageCount = $baseFormulation->customProducts()->count();
            if ($usageCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Cannot delete base formulation. It is used in {$usageCount} custom products."
                ], 400);
            }

            // Log deletion before actual deletion
            $this->logFormulationAnalytics(request()->user(), 'base_formulation_deleted', [
                'base_formulation_id' => $baseFormulation->base_formulation_id,
                'base_name' => $baseFormulation->base_name,
            ]);

            $baseFormulation->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Base formulation deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error deleting base formulation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete base formulation'
            ], 500);
        }
    }

    /**
     * Deactivate base formulation instead of deleting (Admin only).
     */
    public function deactivate($baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            $baseFormulation->deactivate();

            // Log deactivation
            $this->logFormulationAnalytics(request()->user(), 'base_formulation_deactivated', [
                'base_formulation_id' => $baseFormulation->base_formulation_id,
                'base_name' => $baseFormulation->base_name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Base formulation deactivated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error deactivating base formulation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate base formulation'
            ], 500);
        }
    }

    /**
     * Clone base formulation (Admin only).
     */
    public function clone(Request $request, $baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            $validator = Validator::make($request->all(), [
                'new_name' => 'required|string|max:100|unique:BaseFormulation,base_name',
                'description' => 'sometimes|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clonedFormulation = $baseFormulation->cloneFormulation(
                $request->input('new_name'),
                $request->input('description')
            );

            // Log cloning
            $this->logFormulationAnalytics(request()->user(), 'base_formulation_cloned', [
                'original_id' => $baseFormulation->base_formulation_id,
                'cloned_id' => $clonedFormulation->base_formulation_id,
                'new_name' => $clonedFormulation->base_name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Base formulation cloned successfully',
                'data' => $clonedFormulation->getFormulationSummary()
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error cloning base formulation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clone base formulation'
            ], 500);
        }
    }

    /**
     * Get formulations compatible with specific skin types.
     */
    public function getCompatibleFormulations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'skin_types' => 'required|array|min:1',
                'skin_types.*' => 'string|in:dry,oily,combination,sensitive,normal',
                'category' => 'sometimes|string|in:' . implode(',', array_keys(BaseFormulation::FORMULATION_CATEGORIES)),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skinTypes = $request->input('skin_types');
            $category = $request->input('category');

            $query = BaseFormulation::active();

            if ($category) {
                $query->byCategory($category);
            }

            $compatibleFormulations = [];
            
            foreach ($skinTypes as $skinType) {
                $formulations = $query->compatibleWithSkinType($skinType)
                    ->get()
                    ->map(fn($formulation) => $formulation->getFormulationSummary());
                
                $compatibleFormulations[$skinType] = $formulations;
            }

            return response()->json([
                'status' => 'success',
                'data' => $compatibleFormulations,
                'summary' => [
                    'total_skin_types_checked' => count($skinTypes),
                    'formulations_per_skin_type' => array_map(fn($f) => count($f), $compatibleFormulations),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching compatible formulations: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch compatible formulations'
            ], 500);
        }
    }

    /**
     * Get formulation recommendations based on profile.
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'skin_type' => 'required|string|in:dry,oily,combination,sensitive,normal',
                'concerns' => 'sometimes|array',
                'concerns.*' => 'string|in:anti_aging,hydrating,brightening,acne_treatment,sensitive_care',
                'limit' => 'sometimes|integer|min:1|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skinType = $request->input('skin_type');
            $concerns = $request->input('concerns', []);
            $limit = $request->input('limit', 10);

            // Get base recommendations for skin type
            $recommendations = BaseFormulation::getRecommendationsForSkinType($skinType);

            // Filter by concerns if provided
            if (!empty($concerns)) {
                $recommendations = array_filter($recommendations, function($formulation) use ($concerns) {
                    return in_array($formulation['category'], $concerns);
                });
            }

            // Limit results
            $recommendations = array_slice($recommendations, 0, $limit);

            // Log recommendation request
            $this->logFormulationAnalytics($request->user(), 'formulation_recommendations_requested', [
                'skin_type' => $skinType,
                'concerns' => $concerns,
                'recommendations_count' => count($recommendations),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => array_values($recommendations),
                'query_info' => [
                    'skin_type' => $skinType,
                    'concerns' => $concerns,
                    'total_recommendations' => count($recommendations),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching formulation recommendations: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch recommendations'
            ], 500);
        }
    }

    /**
     * Validate ingredient compatibility.
     */
    public function validateCompatibility(Request $request, $baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            $validator = Validator::make($request->all(), [
                'ingredients' => 'required|array|min:1',
                'ingredients.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ingredients = $request->input('ingredients');
            $incompatible = $baseFormulation->checkIngredientCompatibility($ingredients);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'is_compatible' => empty($incompatible),
                    'incompatible_ingredients' => $incompatible,
                    'compatible_ingredients' => array_diff($ingredients, $incompatible),
                    'formulation_name' => $baseFormulation->base_name,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error validating ingredient compatibility: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate compatibility'
            ], 500);
        }
    }

    /**
     * Validate concentration for ingredients.
     */
    public function validateConcentration(Request $request, $baseFormulationId): JsonResponse
    {
        try {
            $baseFormulation = BaseFormulation::findOrFail($baseFormulationId);

            $validator = Validator::make($request->all(), [
                'concentrations' => 'required|array|min:1',
                'concentrations.*.ingredient' => 'required|string',
                'concentrations.*.concentration' => 'required|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $concentrations = $request->input('concentrations');
            $validationResults = [];

            foreach ($concentrations as $item) {
                $isValid = $baseFormulation->validateConcentration(
                    $item['ingredient'], 
                    $item['concentration']
                );

                $validationResults[] = [
                    'ingredient' => $item['ingredient'],
                    'concentration' => $item['concentration'],
                    'is_valid' => $isValid,
                    'allowed_range' => $baseFormulation->standard_concentration_ranges[$item['ingredient']] ?? null,
                ];
            }

            $allValid = collect($validationResults)->every('is_valid');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'all_concentrations_valid' => $allValid,
                    'results' => $validationResults,
                    'formulation_name' => $baseFormulation->base_name,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Base formulation not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error validating concentrations: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate concentrations'
            ], 500);
        }
    }

    /**
     * Get formulation analytics (Admin only).
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $analytics = BaseFormulation::getFormulationStats();

            // Add additional analytics
            $analytics['recent_activity'] = BaseFormulation::with('creator')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(fn($f) => $f->getFormulationSummary());

            $analytics['usage_statistics'] = $this->getUsageStatistics();

            return response()->json([
                'status' => 'success',
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching formulation analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * Helper methods for analytics
     */
    private function getUsageStatistics(): array
    {
        $formulations = BaseFormulation::withCount('customProducts')->get();
        
        return [
            'most_used_formulations' => $formulations->sortByDesc('custom_products_count')
                ->take(5)
                ->map(function($f) {
                    return [
                        'name' => $f->base_name,
                        'usage_count' => $f->custom_products_count,
                        'category' => $f->formulation_category,
                    ];
                })->values(),
            'unused_formulations' => $formulations->where('custom_products_count', 0)->count(),
            'average_usage_per_formulation' => $formulations->avg('custom_products_count'),
        ];
    }

    /**
     * Log analytics to MongoDB
     */
    private function logFormulationAnalytics($user, $action, $data = [])
    {
        try {
            $analyticsData = [
                'user_id' => $user->user_id,
                'action' => $action,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'additional_data' => $data
            ];

            // MongoDB Service integration
            // MongoService::logFormulationActivity($analyticsData);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log formulation analytics: ' . $e->getMessage());
        }
    }
}
