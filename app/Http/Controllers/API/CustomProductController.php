<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CustomProductController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('active.user');
    }

    /**
     * Display a listing of user's custom products.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10), 50);

            $customProducts = CustomProduct::forUser($user->user_id)
                ->with(['baseProduct:product_id,product_name,base_category'])
                ->orderBy('formulation_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedProducts = $customProducts->getCollection()->map(function ($product) {
                return $product->getFormattedDetails();
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedProducts,
                'count' => $customProducts->count(),
                'pagination' => [
                    'current_page' => $customProducts->currentPage(),
                    'per_page' => $customProducts->perPage(),
                    'total' => $customProducts->total(),
                    'last_page' => $customProducts->lastPage(),
                    'has_more' => $customProducts->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching user custom products: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch custom products'
            ], 500);
        }
    }

    /**
     * Store a newly created custom product.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate the request data
            $validator = Validator::make($request->all(), CustomProduct::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = $user->user_id;

            // Verify base product exists
            $baseProduct = Product::find($data['base_product_id']);
            if (!$baseProduct) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Base product not found'
                ], 404);
            }

            // Create custom product with intelligent formulation
            $customProduct = CustomProduct::createWithFormulation($data);

            // Log custom product creation for analytics
            $this->logCustomProductAnalytics($user, 'custom_product_created', [
                'custom_product_id' => $customProduct->custom_product_id,
                'base_product_id' => $customProduct->base_product_id,
                'total_price' => $customProduct->total_price,
                'skin_type' => $customProduct->profile_data['skin_type'],
                'concerns' => $customProduct->profile_data['skin_concerns'],
                'ingredients_count' => count($customProduct->selected_ingredients),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Custom product created successfully',
                'data' => $customProduct->getFormattedDetails()
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error creating custom product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create custom product'
            ], 500);
        }
    }

    /**
     * Display the specified custom product.
     */
    public function show(Request $request, $customProductId): JsonResponse
    {
        try {
            $user = $request->user();

            $customProduct = CustomProduct::forUser($user->user_id)
                ->with(['baseProduct:product_id,product_name,base_category'])
                ->findOrFail($customProductId);

            // Log custom product view for analytics
            $this->logCustomProductAnalytics($user, 'custom_product_viewed', [
                'custom_product_id' => $customProduct->custom_product_id,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $customProduct->getFormattedDetails()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Custom product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching custom product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch custom product'
            ], 500);
        }
    }

    /**
     * Update the specified custom product.
     */
    public function update(Request $request, $customProductId): JsonResponse
    {
        try {
            $user = $request->user();

            $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

            // Validate update data
            $validator = Validator::make($request->all(), CustomProduct::validationRules(true));

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // If profile data is being updated, regenerate the entire formulation
            if (isset($data['profile_data'])) {
                $customProduct->updateWithNewProfile($data['profile_data']);
                
                // Log profile update
                $this->logCustomProductAnalytics($user, 'custom_product_reformulated', [
                    'custom_product_id' => $customProduct->custom_product_id,
                    'old_price' => $customProduct->getOriginal('total_price'),
                    'new_price' => $customProduct->total_price,
                    'new_skin_type' => $data['profile_data']['skin_type'],
                    'new_concerns' => $data['profile_data']['skin_concerns'],
                ]);
            } else {
                // Regular update for other fields
                $customProduct->update($data);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Custom product updated successfully',
                'data' => $customProduct->fresh()->getFormattedDetails()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Custom product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error updating custom product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update custom product'
            ], 500);
        }
    }

    /**
     * Remove the specified custom product.
     */
    public function destroy(Request $request, $customProductId): JsonResponse
    {
        try {
            $user = $request->user();

            $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

            // Check if product is in any active orders
            $activeOrders = $customProduct->orders()
                ->whereIn('payment_status', ['pending', 'paid'])
                ->whereIn('shipping_status', ['processing', 'shipped'])
                ->count();

            if ($activeOrders > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete custom product with active orders'
                ], 400);
            }

            // Log deletion before actual deletion
            $this->logCustomProductAnalytics($user, 'custom_product_deleted', [
                'custom_product_id' => $customProduct->custom_product_id,
                'product_name' => $customProduct->product_name,
            ]);

            $customProduct->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Custom product deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Custom product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error deleting custom product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete custom product'
            ], 500);
        }
    }

    /**
     * Get allergy alternatives and information.
     */
    public function allergyAlternatives(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $alternatives = CustomProduct::getAllergyAlternatives();

            // Log allergy lookup for analytics
            $this->logCustomProductAnalytics($user, 'allergy_alternatives_viewed', []);

            return response()->json([
                'status' => 'success',
                'data' => $alternatives
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching allergy alternatives: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch allergy alternatives'
            ], 500);
        }
    }

    /**
     * Get custom product statistics for user.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $stats = [
                'total_custom_products' => CustomProduct::getUserProductsCount($user->user_id),
                'recent_products' => CustomProduct::getRecentForUser($user->user_id, 3),
                'favorite_skin_type' => $this->getUserFavoriteSkinType($user->user_id),
                'most_common_concerns' => $this->getUserCommonConcerns($user->user_id),
                'average_price' => $this->getUserAveragePrice($user->user_id),
                'ingredient_usage' => $this->getUserIngredientStats($user->user_id),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching custom product statistics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    /**
     * Reformulate existing custom product with new profile.
     */
    public function reformulate(Request $request, $customProductId): JsonResponse
    {
        try {
            $user = $request->user();

            $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

            // Validate new profile data
            $validator = Validator::make($request->all(), [
                'profile_data' => 'required|array',
                'profile_data.skin_type' => 'required|string|in:dry,oily,combination,sensitive',
                'profile_data.skin_concerns' => 'required|array|min:1',
                'profile_data.skin_concerns.*' => 'string|in:blemish,wrinkle,spots,soothe',
                'profile_data.environmental_factors' => 'required|string|in:urban,tropical,moderate',
                'profile_data.allergies' => 'sometimes|array',
                'profile_data.allergies.*' => 'string|in:' . implode(',', array_keys(CustomProduct::ALLERGY_CATEGORIES)),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldPrice = $customProduct->total_price;
            $oldIngredients = $customProduct->selected_ingredients;

            // Update with new profile
            $customProduct->updateWithNewProfile($validator->validated()['profile_data']);

            // Log reformulation
            $this->logCustomProductAnalytics($user, 'custom_product_reformulated', [
                'custom_product_id' => $customProduct->custom_product_id,
                'old_price' => $oldPrice,
                'new_price' => $customProduct->total_price,
                'old_ingredients' => $oldIngredients,
                'new_ingredients' => $customProduct->selected_ingredients,
                'price_change' => $customProduct->total_price - $oldPrice,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Custom product reformulated successfully',
                'data' => $customProduct->fresh()->getFormattedDetails(),
                'changes' => [
                    'price_change' => $customProduct->total_price - $oldPrice,
                    'ingredients_changed' => $oldIngredients !== $customProduct->selected_ingredients,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Custom product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error reformulating custom product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reformulate custom product'
            ], 500);
        }
    }

    /**
     * Admin-only endpoints
     */

    /**
     * Get all custom products (Admin only).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 20), 100);
            $skinType = $request->get('skin_type');
            $search = $request->get('search');

            $query = CustomProduct::with(['user:user_id,first_name,last_name,email', 'baseProduct']);

            if ($skinType) {
                $query->whereJsonContains('profile_data->skin_type', $skinType);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('email', 'like', "%{$search}%")
                                   ->orWhere('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }

            $customProducts = $query->orderBy('formulation_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedProducts = $customProducts->getCollection()->map(function ($product) {
                $details = $product->getFormattedDetails();
                $details['user'] = [
                    'user_id' => $product->user->user_id,
                    'name' => $product->user->name,
                    'email' => $product->user->email,
                ];
                return $details;
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedProducts,
                'pagination' => [
                    'current_page' => $customProducts->currentPage(),
                    'per_page' => $customProducts->perPage(),
                    'total' => $customProducts->total(),
                    'last_page' => $customProducts->lastPage(),
                    'has_more' => $customProducts->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching custom products for admin: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch custom products'
            ], 500);
        }
    }

    /**
     * Get custom product analytics (Admin only).
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $analytics = [
                'total_custom_products' => CustomProduct::count(),
                'products_by_skin_type' => $this->getProductsBySkinType(),
                'popular_ingredients' => $this->getPopularIngredients(),
                'average_price' => CustomProduct::avg('total_price'),
                'price_distribution' => $this->getPriceDistribution(),
                'recent_activity' => CustomProduct::recent(10)->get()->map(fn($p) => $p->getFormattedDetails()),
                'allergy_stats' => $this->getAllergyStats(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching custom product analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * Helper methods for analytics
     */
    private function getUserFavoriteSkinType($userId): ?string
    {
        return CustomProduct::forUser($userId)
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(profile_data, '$.skin_type')) as skin_type, COUNT(*) as count")
            ->groupBy('skin_type')
            ->orderBy('count', 'desc')
            ->first()?->skin_type;
    }

    private function getUserCommonConcerns($userId): array
    {
        $products = CustomProduct::forUser($userId)->get();
        $concerns = [];
        
        foreach ($products as $product) {
            if (isset($product->profile_data['skin_concerns'])) {
                foreach ($product->profile_data['skin_concerns'] as $concern) {
                    $concerns[$concern] = ($concerns[$concern] ?? 0) + 1;
                }
            }
        }
        
        arsort($concerns);
        return array_slice($concerns, 0, 3, true);
    }

    private function getUserAveragePrice($userId): float
    {
        return CustomProduct::forUser($userId)->avg('total_price') ?? 0;
    }

    private function getUserIngredientStats($userId): array
    {
        $products = CustomProduct::forUser($userId)->get();
        $ingredients = [];
        
        foreach ($products as $product) {
            foreach ($product->selected_ingredients as $ingredient) {
                $ingredients[$ingredient] = ($ingredients[$ingredient] ?? 0) + 1;
            }
        }
        
        arsort($ingredients);
        return array_slice($ingredients, 0, 5, true);
    }

    private function getProductsBySkinType(): array
    {
        return CustomProduct::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(profile_data, '$.skin_type')) as skin_type, COUNT(*) as count")
            ->groupBy('skin_type')
            ->pluck('count', 'skin_type')
            ->toArray();
    }

    private function getPopularIngredients(): array
    {
        $products = CustomProduct::all();
        $ingredients = [];
        
        foreach ($products as $product) {
            foreach ($product->selected_ingredients as $ingredient) {
                $ingredients[$ingredient] = ($ingredients[$ingredient] ?? 0) + 1;
            }
        }
        
        arsort($ingredients);
        return array_slice($ingredients, 0, 10, true);
    }

    private function getPriceDistribution(): array
    {
        return [
            'under_80' => CustomProduct::where('total_price', '<', 80)->count(),
            '80_to_120' => CustomProduct::whereBetween('total_price', [80, 120])->count(),
            '120_to_160' => CustomProduct::whereBetween('total_price', [120, 160])->count(),
            'over_160' => CustomProduct::where('total_price', '>', 160)->count(),
        ];
    }

    private function getAllergyStats(): array
    {
        $products = CustomProduct::all();
        $allergies = [];
        
        foreach ($products as $product) {
            if (isset($product->profile_data['allergies'])) {
                foreach ($product->profile_data['allergies'] as $allergy) {
                    $allergies[$allergy] = ($allergies[$allergy] ?? 0) + 1;
                }
            }
        }
        
        arsort($allergies);
        return $allergies;
    }

    /**
     * Log analytics to MongoDB
     */
    private function logCustomProductAnalytics($user, $action, $data = [])
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
            // MongoService::logCustomProductActivity($analyticsData);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log custom product analytics: ' . $e->getMessage());
        }
    }
}
