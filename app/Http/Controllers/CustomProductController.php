<?php

namespace App\Http\Controllers;

use App\Models\CustomProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomProductController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of user's custom products
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $customProducts = CustomProduct::forUser($user->user_id)
            ->with(['baseProduct'])
            ->orderBy('formulation_date', 'desc')
            ->paginate(12);

        return view('custom-products.index', [
            'customProducts' => $customProducts,
            'userStats' => [
                'total_products' => CustomProduct::getUserProductsCount($user->user_id),
                'recent_products' => CustomProduct::getRecentForUser($user->user_id, 3),
            ]
        ]);
    }

    /**
     * Show the form for creating a new custom product
     */
    public function create(): View
    {
        $baseProducts = Product::where('base_category', 'serum')
            ->orWhere('base_category', 'essence')
            ->select('product_id', 'product_name', 'base_category', 'product_price')
            ->get();

        return view('custom-products.create', [
            'baseProducts' => $baseProducts,
            'allergyCategories' => CustomProduct::getAllergyAlternatives(),
        ]);
    }

  /**
 * Store a newly created custom product
 */
public function store(Request $request): RedirectResponse
{
    $request->validate(CustomProduct::validationRules());

    try {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id; // ✅ Use auth()->user()->id

        $customProduct = CustomProduct::createWithFormulation($data);

        return redirect()->route('custom-products.show', $customProduct)
            ->with('success', 'Custom product created successfully! Your personalized formulation is ready.');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Failed to create custom product. Please try again.');
    }
}

    /**
     * Display the specified custom product
     */
    public function show($customProductId): View
    {
        try {
            $user = auth()->user();
            
            // Fix: Use $user->id (users table PK) to match custom_products.user_id (FK)
            $customProduct = CustomProduct::where('custom_product_id', $customProductId)
                ->where('user_id', $user->id) // ✅ users.id matches custom_products.user_id
                ->with(['baseProduct'])
                ->firstOrFail();
    
            $productDetails = $customProduct->getFormattedDetails();
            
            return view('custom-products.show', [
                'customProduct' => $customProduct,
                'productDetails' => $productDetails,
                'canOrder' => true,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('survey.index')
                ->with('error', 'Product not found. Please complete the survey to create a new custom product.');
        } catch (\Exception $e) {
            \Log::error('Error displaying custom product: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Unable to display product details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified custom product
     */
    public function edit($customProductId): View
    {
        $user = auth()->user();
        
        $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

        $baseProducts = Product::where('base_category', 'serum')
            ->orWhere('base_category', 'essence')
            ->select('product_id', 'product_name', 'base_category', 'product_price')
            ->get();

        return view('custom-products.edit', [
            'customProduct' => $customProduct,
            'baseProducts' => $baseProducts,
            'allergyCategories' => CustomProduct::getAllergyAlternatives(),
        ]);
    }

    /**
     * Update the specified custom product
     */
    public function update(Request $request, $customProductId): RedirectResponse
    {
        $user = auth()->user();
        $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

        $request->validate(CustomProduct::validationRules(true));

        try {
            $data = $request->validated();

            // If profile data is being updated, regenerate the entire formulation
            if (isset($data['profile_data'])) {
                $customProduct->updateWithNewProfile($data['profile_data']);
                $message = 'Custom product reformulated successfully with your new preferences!';
            } else {
                $customProduct->update($data);
                $message = 'Custom product updated successfully!';
            }

            return redirect()->route('custom-products.show', $customProduct)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update custom product. Please try again.');
        }
    }

    /**
     * Remove the specified custom product
     */
    public function destroy($customProductId): RedirectResponse
    {
        try {
            $user = auth()->user();
            $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

            // Check if product is in any active orders
            $activeOrders = $customProduct->orders()
                ->whereIn('payment_status', ['pending', 'paid'])
                ->whereIn('shipping_status', ['processing', 'shipped'])
                ->count();

            if ($activeOrders > 0) {
                return back()->with('error', 'Cannot delete custom product with active orders.');
            }

            $customProduct->delete();

            return redirect()->route('custom-products.index')
                ->with('success', 'Custom product deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete custom product. Please try again.');
        }
    }

    /**
     * Show reformulation form
     */
    public function reformulate($customProductId): View
    {
        $user = auth()->user();
        $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

        return view('custom-products.reformulate', [
            'customProduct' => $customProduct,
            'allergyCategories' => CustomProduct::getAllergyAlternatives(),
            'currentProfile' => $customProduct->profile_data,
        ]);
    }

    /**
     * Process reformulation
     */
    public function processReformulation(Request $request, $customProductId): RedirectResponse
    {
        $user = auth()->user();
        $customProduct = CustomProduct::forUser($user->user_id)->findOrFail($customProductId);

        $request->validate([
            'profile_data' => 'required|array',
            'profile_data.skin_type' => 'required|string|in:dry,oily,combination,sensitive',
            'profile_data.skin_concerns' => 'required|array|min:1',
            'profile_data.skin_concerns.*' => 'string|in:blemish,wrinkle,spots,soothe',
            'profile_data.environmental_factors' => 'required|string|in:urban,tropical,moderate',
            'profile_data.allergies' => 'sometimes|array',
            'profile_data.allergies.*' => 'string|in:' . implode(',', array_keys(CustomProduct::ALLERGY_CATEGORIES)),
        ]);

        try {
            $oldPrice = $customProduct->total_price;
            $customProduct->updateWithNewProfile($request->input('profile_data'));
            
            $priceChange = $customProduct->total_price - $oldPrice;
            $message = 'Your custom product has been reformulated successfully!';
            
            if ($priceChange != 0) {
                $message .= ' Price ' . ($priceChange > 0 ? 'increased' : 'decreased') . ' by $' . abs($priceChange);
            }

            return redirect()->route('custom-products.show', $customProduct)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to reformulate custom product. Please try again.');
        }
    }

    /**
     * Show allergy alternatives page
     */
    public function allergyAlternatives(): View
    {
        return view('custom-products.allergy-alternatives', [
            'allergyCategories' => CustomProduct::getAllergyAlternatives(),
        ]);
    }

    /**
     * Admin Routes
     */

    /**
     * Display all custom products for admin
     */
    public function adminIndex(Request $request): View
    {
        $query = CustomProduct::with(['user:user_id,first_name,last_name,email', 'baseProduct']);

        // Apply filters
        if ($request->skin_type) {
            $query->whereJsonContains('profile_data->skin_type', $request->skin_type);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('product_name', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('email', 'like', "%{$request->search}%")
                               ->orWhere('first_name', 'like', "%{$request->search}%")
                               ->orWhere('last_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->price_range) {
            switch ($request->price_range) {
                case 'under_80':
                    $query->where('total_price', '<', 80);
                    break;
                case '80_to_120':
                    $query->whereBetween('total_price', [80, 120]);
                    break;
                case '120_to_160':
                    $query->whereBetween('total_price', [120, 160]);
                    break;
                case 'over_160':
                    $query->where('total_price', '>', 160);
                    break;
            }
        }

        $customProducts = $query->orderBy('formulation_date', 'desc')->paginate(20);

        return view('admin.custom-products.index', [
            'customProducts' => $customProducts,
            'skinTypes' => ['dry', 'oily', 'combination', 'sensitive'],
            'priceRanges' => [
                'under_80' => 'Under $80',
                '80_to_120' => '$80 - $120',
                '120_to_160' => '$120 - $160',
                'over_160' => 'Over $160'
            ],
            'currentFilters' => [
                'skin_type' => $request->skin_type,
                'search' => $request->search,
                'price_range' => $request->price_range,
            ]
        ]);
    }

    /**
     * Display custom product details for admin
     */
    public function adminShow($customProductId): View
    {
        $customProduct = CustomProduct::with(['user', 'baseProduct', 'orders'])
            ->findOrFail($customProductId);

        return view('admin.custom-products.show', [
            'customProduct' => $customProduct,
            'productDetails' => $customProduct->getFormattedDetails(),
            'orderHistory' => $customProduct->orders()->with('orderItems')->get(),
        ]);
    }

    /**
     * Custom product analytics dashboard (Admin)
     */
    public function analytics(): View
    {
        $analytics = [
            'total_custom_products' => CustomProduct::count(),
            'products_by_skin_type' => $this->getProductsBySkinType(),
            'popular_ingredients' => $this->getPopularIngredients(),
            'average_price' => CustomProduct::avg('total_price'),
            'price_distribution' => $this->getPriceDistribution(),
            'recent_activity' => CustomProduct::recent(10)->with('user')->get(),
            'allergy_stats' => $this->getAllergyStats(),
            'monthly_growth' => $this->getMonthlyGrowth(),
        ];

        return view('admin.custom-products.analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Helper methods for analytics
     */
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

    private function getMonthlyGrowth(): array
    {
        return CustomProduct::selectRaw('YEAR(formulation_date) as year, MONTH(formulation_date) as month, COUNT(*) as count')
            ->where('formulation_date', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'count' => $item->count
                ];
            })
            ->toArray();
    }
}
