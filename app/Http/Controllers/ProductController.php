<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BaseFormulation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // ... (keep all your existing methods exactly as they are)

    /**
     * Display a listing of products
     */
    public function index(Request $request): View
    {
        $products = Product::with('baseFormulation')
            ->when($request->category, fn($query) => $query->byCategory($request->category))
            ->when($request->search, function ($query) use ($request) {
                $query->where('product_name', 'like', "%{$request->search}%");
            })
            ->paginate(12);

        return view('products.index', [
            'products' => $products,
            'categories' => Product::getCategoryOptions(),
            'currentCategory' => $request->category,
            'currentSearch' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new product (Admin only)
     */
    public function create(): View
    {
        return view('products.create', [
            'categories' => Product::getCategoryOptions(),
            'types' => Product::getTypeOptions(),
            'baseFormulations' => BaseFormulation::all(['base_formulation_id', 'base_name']),
        ]);
    }

    /**
     * Store a newly created product (Admin only)
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        // Handle JSON requests (from admin panel)
        if ($request->expectsJson() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255|unique:products,product_name',
                'base_category' => 'required|string|max:100',
                'product_type' => 'required|string|max:100',
                'standard_price' => 'required|numeric|min:0',
                'customization_price_modifier' => 'required|numeric|min:0',
                'base_formulation_id' => 'required|exists:base_formulations,base_formulation_id',
                'description' => 'nullable|string',
                'image_url' => 'nullable|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                DB::beginTransaction();

                $product = Product::create([
                    'product_name' => trim($request->product_name),
                    'base_category' => trim($request->base_category),
                    'product_type' => trim($request->product_type),
                    'standard_price' => $request->standard_price,
                    'customization_price_modifier' => $request->customization_price_modifier,
                    'base_formulation_id' => $request->base_formulation_id,
                    'description' => $request->description,
                    'image_url' => $request->image_url,
                    'is_active' => true,
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Product created successfully',
                    'data' => $product->load('baseFormulation:base_formulation_id,base_name')
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error creating product: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create product'
                ], 500);
            }
        }

        // Handle regular form requests
        $request->validate(Product::validationRules());
        
        try {
            $product = Product::create($request->validated());
            
            return redirect()->route('products.show', $product)
                ->with('success', 'Product created successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product
     */
    public function show($productId): View
    {
        $product = Product::with(['baseFormulation', 'customProducts'])
            ->findOrFail($productId);
            
        $relatedProducts = $product->getFrequentlyBoughtTogether();

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Show the form for editing the specified product (Admin only)
     */
    public function edit($productId): View
    {
        $product = Product::findOrFail($productId);

        return view('products.edit', [
            'product' => $product,
            'categories' => Product::getCategoryOptions(),
            'types' => Product::getTypeOptions(),
            'baseFormulations' => BaseFormulation::all(['base_formulation_id', 'base_name']),
        ]);
    }

    /**
     * Update the specified product (Admin only)
     */
    public function update(Request $request, $productId): RedirectResponse|JsonResponse
    {
        $product = Product::findOrFail($productId);

        // Handle JSON requests (from admin panel)
        if ($request->expectsJson() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255|unique:products,product_name,' . $productId . ',product_id',
                'base_category' => 'required|string|max:100',
                'product_type' => 'required|string|max:100',
                'standard_price' => 'required|numeric|min:0',
                'customization_price_modifier' => 'required|numeric|min:0',
                'base_formulation_id' => 'required|exists:base_formulations,base_formulation_id',
                'description' => 'nullable|string',
                'image_url' => 'nullable|url',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                DB::beginTransaction();

                $product->update([
                    'product_name' => trim($request->product_name),
                    'base_category' => trim($request->base_category),
                    'product_type' => trim($request->product_type),
                    'standard_price' => $request->standard_price,
                    'customization_price_modifier' => $request->customization_price_modifier,
                    'base_formulation_id' => $request->base_formulation_id,
                    'description' => $request->description,
                    'image_url' => $request->image_url,
                    'is_active' => $request->get('is_active', true),
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Product updated successfully',
                    'data' => $product->load('baseFormulation:base_formulation_id,base_name')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating product: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update product'
                ], 500);
            }
        }

        // Handle regular form requests
        $request->validate(Product::validationRules(true));
        
        try {
            $product->update($request->validated());
            
            return redirect()->route('products.show', $product)
                ->with('success', 'Product updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product (Admin only)
     */
    public function destroy($productId): RedirectResponse|JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Check if product is used in any orders or custom products
            $isUsed = DB::table('order_items')
                ->where('product_id', $productId)
                ->exists() ||
                DB::table('custom_products')
                ->where('base_product_id', $productId)
                ->exists();

            $message = '';
            if ($isUsed) {
                // Soft delete - just deactivate
                $product->update(['is_active' => false]);
                $message = 'Product deactivated (has existing orders/custom products)';
            } else {
                // Hard delete
                $product->delete();
                $message = 'Product deleted successfully';
            }

            // Handle JSON requests (from admin panel)
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message
                ]);
            }

            // Handle regular form requests
            return redirect()->route('products.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error('Error deleting product: ' . $e->getMessage());

            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete product'
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }

    /**
     * Display product recommendations based on user profile
     */
    public function recommendations(): View|RedirectResponse
    {
        $user = auth()->user();
        $userProfile = \App\Models\UserProfile::getLatestForUser($user->user_id);
        
        if (!$userProfile) {
            return redirect()->route('profile.create')
                ->with('info', 'Please create a profile first to get recommendations.');
        }

        $recommendations = Product::getRecommendationsForProfile($userProfile);
        
        return view('products.recommendations', [
            'recommendations' => $recommendations,
            'profile' => $userProfile,
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request): View
    {
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
        
        return view('products.search', [
            'products' => $products,
            'query' => $query,
            'category' => $category,
            'priceRange' => $priceRange,
            'categories' => Product::getCategoryOptions(),
        ]);
    }

    // ========================================
    // ADMIN-SPECIFIC METHODS
    // ========================================

    /**
     * Admin products index page
     */
    public function adminIndex(): View
    {
        return view('admin.products.index');
    }

    /**
     * Get products for admin dashboard (API endpoint)
     */
    public function getAdminProducts(Request $request): JsonResponse
    {
        try {
            $query = Product::with('baseFormulation:base_formulation_id,base_name');

            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('product_name', 'like', "%{$request->search}%")
                      ->orWhere('base_category', 'like', "%{$request->search}%")
                      ->orWhere('product_type', 'like', "%{$request->search}%");
                });
            }

            if ($request->category) {
                $query->where('base_category', $request->category);
            }

            if ($request->type) {
                $query->where('product_type', $request->type);
            }

            // Sorting
            $sortField = $request->get('sort', 'product_id');
            $sortDirection = $request->get('direction', 'desc');
            
            $allowedSorts = ['product_id', 'product_name', 'base_category', 'product_type', 'standard_price', 'created_at'];
            if (in_array($sortField, $allowedSorts)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Get all products (no pagination for admin table)
            $products = $query->get();

            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching admin products: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }

    /**
     * Get product options for admin forms
     */
    public function getProductOptions(): JsonResponse
    {
        try {
            $categories = Product::distinct()->pluck('base_category')->filter()->sort()->values();
            $types = Product::distinct()->pluck('product_type')->filter()->sort()->values();
            
            // Get base formulations with explicit column selection to avoid duplicates
            $baseFormulations = DB::table('base_formulations')
                ->select(
                    DB::raw('base_formulation_id as id'), // Use alias to avoid duplicate issue
                    'base_name',
                    'description'
                )
                ->where('is_active', 1)
                ->orderBy('base_name')
                ->get()
                ->map(function($formulation) {
                    return [
                        'base_formulation_id' => $formulation->id,
                        'base_name' => $formulation->base_name,
                        'description' => $formulation->description
                    ];
                });

            // If no active formulations found, try without the is_active filter
            if ($baseFormulations->isEmpty()) {
                $baseFormulations = DB::table('base_formulations')
                    ->select(
                        DB::raw('base_formulation_id as id'),
                        'base_name', 
                        'description'
                    )
                    ->orderBy('base_name')
                    ->get()
                    ->map(function($formulation) {
                        return [
                            'base_formulation_id' => $formulation->id,
                            'base_name' => $formulation->base_name,
                            'description' => $formulation->description
                        ];
                    });
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'categories' => $categories,
                    'types' => $types,
                    'base_formulations' => $baseFormulations
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching product options: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch options',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product analytics for admin
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30days');
            $days = match($period) {
                '7days' => 7,
                '30days' => 30,
                '90days' => 90,
                '1year' => 365,
                default => 30
            };

            $startDate = now()->subDays($days);

            $analytics = [
                'overview' => [
                    'total_products' => Product::count(),
                    'active_products' => Product::where('is_active', true)->count(),
                    'categories_count' => Product::distinct('base_category')->count(),
                    'types_count' => Product::distinct('product_type')->count(),
                ],
                'popular_categories' => Product::selectRaw('base_category, COUNT(*) as count')
                    ->groupBy('base_category')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'price_distribution' => [
                    'min_price' => Product::min('standard_price'),
                    'max_price' => Product::max('standard_price'),
                    'avg_price' => Product::avg('standard_price'),
                    'price_ranges' => $this->getPriceDistribution(),
                ],
                'recent_products' => Product::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['product_id', 'product_name', 'base_category', 'standard_price', 'created_at'])
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics,
                'period' => $period
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
     * Get price distribution for analytics
     */
    private function getPriceDistribution(): array
    {
        $ranges = [
            '0-1000' => [0, 1000],
            '1001-5000' => [1001, 5000],
            '5001-10000' => [5001, 10000],
            '10001-20000' => [10001, 20000],
            '20000+' => [20001, PHP_INT_MAX]
        ];

        $distribution = [];
        foreach ($ranges as $label => $range) {
            $count = Product::whereBetween('standard_price', $range)->count();
            $distribution[$label] = $count;
        }

        return $distribution;
    }
}