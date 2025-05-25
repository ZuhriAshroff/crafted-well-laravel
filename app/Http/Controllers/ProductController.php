<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{


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
            'baseFormulations' => \App\Models\BaseFormulation::all(['base_formulation_id', 'base_name']),
        ]);
    }

    /**
     * Store a newly created product (Admin only)
     */
    public function store(Request $request): RedirectResponse
    {
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
            'baseFormulations' => \App\Models\BaseFormulation::all(['base_formulation_id', 'base_name']),
        ]);
    }

    /**
     * Update the specified product (Admin only)
     */
    public function update(Request $request, $productId): RedirectResponse
    {
        $request->validate(Product::validationRules(true));
        
        try {
            $product = Product::findOrFail($productId);
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
    public function destroy($productId): RedirectResponse
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Check for existing custom products
            if ($product->customProducts()->count() > 0) {
                return back()
                    ->with('error', 'Cannot delete product with existing custom products.');
            }
            
            $product->delete();
            
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully!');
                
        } catch (\Exception $e) {
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
}
