<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of products with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10), 100); // Max 100 per page
            $category = $request->get('category');
            $type = $request->get('type');
            $search = $request->get('search');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            $query = Product::with('baseFormulation');

            // Apply filters
            if ($category) {
                $query->byCategory($category);
            }

            if ($type) {
                $query->byType($type);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                      ->orWhere('base_category', 'like', "%{$search}%")
                      ->orWhere('product_type', 'like', "%{$search}%");
                });
            }

            if ($minPrice || $maxPrice) {
                $query->byPriceRange($minPrice, $maxPrice);
            }

            $products = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'status' => 'success',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'has_more' => $products->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }

    /**
     * Store a newly created product (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), Product::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the product
            $product = Product::create($validator->validated());

            // Load the relationship
            $product->load('baseFormulation');

            // Log product creation for analytics
            $this->logProductAnalytics($request->user(), 'product_created', [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'category' => $product->base_category,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product->getFormattedData()
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product'
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::with(['baseFormulation', 'customProducts'])
                ->findOrFail($productId);

            // Log product view for analytics
            if ($request->user()) {
                $this->logProductAnalytics($request->user(), 'product_viewed', [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $product->getFormattedData()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch product'
            ], 500);
        }
    }

    /**
     * Update the specified product (Admin only).
     */
    public function update(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);

            // Validate the request data
            $validator = Validator::make($request->all(), Product::validationRules(true));

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the product
            $product->update($validator->validated());
            $product->load('baseFormulation');

            // Log product update for analytics
            $this->logProductAnalytics($request->user(), 'product_updated', [
                'product_id' => $product->product_id,
                'updated_fields' => array_keys($validator->validated()),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product->getFormattedData()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error updating product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product'
            ], 500);
        }
    }

    /**
     * Remove the specified product (Admin only).
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);

            // Check for existing custom products (handled in model boot method)
            $productName = $product->product_name;
            $product->delete();

            // Log product deletion for analytics
            $this->logProductAnalytics($request->user(), 'product_deleted', [
                'product_id' => $productId,
                'product_name' => $productName,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {
            // Check if it's the custom products constraint error
            if (str_contains($e->getMessage(), 'Cannot delete product with existing custom products')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete product with existing custom products'
                ], 400);
            }

            \Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete product'
            ], 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            $category = $request->get('category');
            $priceRange = null;

            if ($request->has('min_price') || $request->has('max_price')) {
                $priceRange = [
                    'min' => $request->get('min_price'),
                    'max' => $request->get('max_price'),
                ];
            }

            $products = Product::search($query, $category, $priceRange);

            // Log search for analytics
            if ($request->user()) {
                $this->logProductAnalytics($request->user(), 'product_search', [
                    'query' => $query,
                    'category' => $category,
                    'results_count' => $products->count(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $products->map(fn($product) => $product->getFormattedData()),
                'count' => $products->count(),
                'search_params' => [
                    'query' => $query,
                    'category' => $category,
                    'price_range' => $priceRange,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error searching products: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search products'
            ], 500);
        }
    }

    /**
     * Get product recommendations based on user profile
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Try to get user profile - update this based on your UserProfile model
            $userProfile = null;
            if (class_exists('\App\Models\UserProfile')) {
                $userProfile = \App\Models\UserProfile::where('user_id', $user->id)->latest()->first();
            }

            if (!$userProfile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user profile found. Please create a profile first.'
                ], 404);
            }

            $recommendations = Product::getRecommendationsForProfile($userProfile);

            // Log recommendations view for analytics
            $this->logProductAnalytics($user, 'recommendations_viewed', [
                'profile_id' => $userProfile->profile_id ?? null,
                'recommendations_count' => $recommendations->count(),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $recommendations->map(fn($product) => $product->getFormattedData()),
                'count' => $recommendations->count(),
                'based_on_profile' => $userProfile->toArray() ?? []
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching recommendations: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch recommendations'
            ], 500);
        }
    }

    /**
     * Get products frequently bought together
     */
    public function getFrequentlyBoughtTogether(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            $frequentlyBought = $product->getFrequentlyBoughtTogether();

            return response()->json([
                'status' => 'success',
                'data' => $frequentlyBought->map(fn($p) => $p->getFormattedData())
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching frequently bought products: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch frequently bought products'
            ], 500);
        }
    }

    /**
     * Get product categories and types for filters
     */
    public function getOptions(): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'categories' => Product::getCategoryOptions(),
                    'types' => Product::getTypeOptions(),
                    'price_range' => Product::getPriceStatistics(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching product options: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch options'
            ], 500);
        }
    }

    /**
     * Get product analytics (Admin only)
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $analytics = [
                'total_products' => Product::getTotalCount(),
                'products_by_category' => Product::getProductsByCategory(),
                'price_statistics' => Product::getPriceStatistics(),
                'recent_products' => Product::getRecentProducts(10)->map(fn($p) => $p->getFormattedData()),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching product analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * Get product sales statistics (Admin only)
     */
    public function getSalesStats(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            $stats = $product->getSalesStats();

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching product sales stats: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sales statistics'
            ], 500);
        }
    }

    /**
     * Log analytics to MongoDB or database
     */
    private function logProductAnalytics($user, $action, $data = [])
    {
        try {
            // Basic analytics logging - you can expand this later
            $analyticsData = [
                'user_id' => $user->id, // Fixed: use id instead of user_id
                'action' => $action,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'additional_data' => $data
            ];

            // You can implement MongoDB Service here or log to database
            \Log::info('Product Analytics', $analyticsData);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log product analytics: ' . $e->getMessage());
            // Don't throw exception, just log the error
        }
    }
}