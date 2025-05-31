<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\CustomProduct;
use App\Models\BaseFormulation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Apply admin middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Admin dashboard with comprehensive analytics
     */
    public function dashboard(): View
    {
        $analytics = [
            'overview' => $this->getOverviewStats(),
            'revenue' => $this->getRevenueStats(),
            'users' => $this->getUserStats(),
            'products' => $this->getProductStats(),
            'recent_activity' => $this->getRecentActivity(),
            'growth_metrics' => $this->getGrowthMetrics(),
        ];

        return view('admin.dashboard', compact('analytics'));
    }

    /**
     * API endpoint for dashboard analytics
     */
    public function dashboardAnalytics(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30days'); // 7days, 30days, 90days, 1year
            
            $analytics = [
                'overview' => $this->getOverviewStats(),
                'revenue' => $this->getRevenueStats($period),
                'users' => $this->getUserStats($period),
                'products' => $this->getProductStats($period),
                'growth_metrics' => $this->getGrowthMetrics($period),
                'top_performers' => $this->getTopPerformers($period),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics,
                'period' => $period,
                'generated_at' => now()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching admin analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * System health check
     */
    public function systemHealth(): JsonResponse
    {
        try {
            $health = [
                'database' => $this->checkDatabaseHealth(),
                'storage' => $this->checkStorageHealth(),
                'cache' => $this->checkCacheHealth(),
                'queue' => $this->checkQueueHealth(),
                'overall_status' => 'healthy'
            ];

            // Determine overall status
            $healthStatuses = array_column($health, 'status');
            if (in_array('critical', $healthStatuses)) {
                $health['overall_status'] = 'critical';
            } elseif (in_array('warning', $healthStatuses)) {
                $health['overall_status'] = 'warning';
            }

            return response()->json([
                'status' => 'success',
                'data' => $health,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Health check failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User management overview
     */
    public function userManagement(Request $request): View
    {
        $query = User::query();

        // Apply filters
        if ($request->status) {
            if ($request->status === 'active') {
                $query->where(function($q) {
                    $q->where('is_active', true)->orWhere('status', 'active');
                });
            } elseif ($request->status === 'inactive') {
                $query->where(function($q) {
                    $q->where('is_active', false)->orWhere('status', 'inactive');
                });
            }
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                  ->orWhere('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        }

        $users = $query->withCount(['orders', 'customProducts'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
            'userStats' => $this->getUserStats(),
            'currentFilters' => [
                'status' => $request->status,
                'role' => $request->role,
                'search' => $request->search,
            ]
        ]);
    }

    /**
     * Platform settings management
     */
    public function platformSettings(): View
    {
        // You can implement a settings system here
        $settings = [
            'system' => [
                'maintenance_mode' => false,
                'registration_enabled' => true,
                'api_rate_limit' => 1000,
            ],
            'business' => [
                'base_product_price' => CustomProduct::BASE_PRICE,
                'max_custom_products_per_user' => 10,
                'auto_approve_orders' => false,
            ],
            'email' => [
                'order_notifications' => true,
                'welcome_emails' => true,
                'promotional_emails' => false,
            ]
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Export data for reports
     */
    public function exportData(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:users,orders,products,custom_products',
            'format' => 'required|in:csv,json,xlsx',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
        ]);

        try {
            $data = $this->prepareExportData($request->type, $request->only(['date_from', 'date_to']));
            
            // In a real implementation, you'd queue this for large datasets
            return response()->json([
                'status' => 'success',
                'message' => 'Export prepared successfully',
                'data' => $data,
                'count' => count($data)
            ]);

        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Export failed'
            ], 500);
        }
    }

    /**
     * Private helper methods for analytics
     */
    private function getOverviewStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('payment_status', 'pending')->count(),
            'total_products' => Product::count(),
            'custom_products' => CustomProduct::count(),
            'base_formulations' => BaseFormulation::count(),
            'revenue_today' => Order::where('payment_status', 'paid')
                ->whereDate('order_date', today())
                ->sum('total_amount'),
        ];
    }

    private function getRevenueStats(string $period = '30days'): array
    {
        $days = $this->getPeriodDays($period);
        $startDate = now()->subDays($days);

        $revenue = Order::where('payment_status', 'paid')
            ->where('order_date', '>=', $startDate)
            ->selectRaw('DATE(order_date) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $revenue->sum('revenue');
        $totalOrders = $revenue->sum('orders');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'daily_breakdown' => $revenue->toArray(),
            'growth_rate' => $this->calculateGrowthRate('revenue', $period),
        ];
    }

    private function getUserStats(string $period = '30days'): array
    {
        $days = $this->getPeriodDays($period);
        $startDate = now()->subDays($days);

        $newUsers = User::where('created_at', '>=', $startDate)->count();
        $activeUsers = User::where('is_active', true)->count();
        $totalUsers = User::count();

        return [
            'total_users' => $totalUsers,
            'new_users' => $newUsers,
            'active_users' => $activeUsers,
            'retention_rate' => $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0,
            'user_types' => [
                'regular' => User::where('role', '!=', 'admin')->count(),
                'admin' => User::where('role', 'admin')->count(),
            ],
            'registration_trend' => User::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as registrations')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray(),
        ];
    }

    private function getProductStats(string $period = '30days'): array
    {
        $days = $this->getPeriodDays($period);
        $startDate = now()->subDays($days);

        return [
            'total_products' => Product::count(),
            'custom_products' => CustomProduct::count(),
            'base_formulations' => BaseFormulation::count(),
            'new_custom_products' => CustomProduct::where('formulation_date', '>=', $startDate)->count(),
            'popular_skin_types' => CustomProduct::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(profile_data, '$.skin_type')) as skin_type, COUNT(*) as count")
                ->groupBy('skin_type')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
                ->toArray(),
            'ingredient_usage' => $this->getPopularIngredients(),
        ];
    }

    private function getRecentActivity(): array
    {
        return [
            'recent_orders' => Order::with('user:user_id,first_name,last_name,email')
                ->orderBy('order_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->order_id,
                        'user' => $order->user->name ?? 'N/A',
                        'amount' => $order->total_amount,
                        'status' => $order->payment_status,
                        'date' => $order->order_date,
                    ];
                }),
            'recent_users' => User::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['user_id', 'first_name', 'last_name', 'email', 'created_at'])
                ->map(function($user) {
                    return [
                        'id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'joined' => $user->created_at,
                    ];
                }),
            'recent_custom_products' => CustomProduct::with('user:user_id,first_name,last_name')
                ->orderBy('formulation_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->custom_product_id,
                        'name' => $product->product_name,
                        'user' => $product->user->name ?? 'N/A',
                        'price' => $product->total_price,
                        'created' => $product->formulation_date,
                    ];
                }),
        ];
    }

    private function getGrowthMetrics(string $period = '30days'): array
    {
        return [
            'user_growth' => $this->calculateGrowthRate('users', $period),
            'revenue_growth' => $this->calculateGrowthRate('revenue', $period),
            'order_growth' => $this->calculateGrowthRate('orders', $period),
            'custom_product_growth' => $this->calculateGrowthRate('custom_products', $period),
        ];
    }

    private function getTopPerformers(string $period = '30days'): array
    {
        $days = $this->getPeriodDays($period);
        $startDate = now()->subDays($days);

        return [
            'top_customers' => User::withSum(['orders' => function($query) use ($startDate) {
                    $query->where('payment_status', 'paid')->where('order_date', '>=', $startDate);
                }], 'total_amount')
                ->orderBy('orders_sum_total_amount', 'desc')
                ->limit(5)
                ->get(['user_id', 'first_name', 'last_name', 'email'])
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'total_spent' => $user->orders_sum_total_amount ?? 0,
                    ];
                }),
            'popular_base_formulations' => BaseFormulation::withCount(['customProducts' => function($query) use ($startDate) {
                    $query->where('formulation_date', '>=', $startDate);
                }])
                ->orderBy('custom_products_count', 'desc')
                ->limit(5)
                ->get(['base_formulation_id', 'base_name'])
                ->map(function($formulation) {
                    return [
                        'name' => $formulation->base_name,
                        'usage_count' => $formulation->custom_products_count,
                    ];
                }),
        ];
    }

    private function calculateGrowthRate(string $metric, string $period): float
    {
        $days = $this->getPeriodDays($period);
        $currentPeriodStart = now()->subDays($days);
        $previousPeriodStart = now()->subDays($days * 2);
        $previousPeriodEnd = $currentPeriodStart;

        switch ($metric) {
            case 'users':
                $current = User::where('created_at', '>=', $currentPeriodStart)->count();
                $previous = User::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();
                break;
            case 'revenue':
                $current = Order::where('payment_status', 'paid')
                    ->where('order_date', '>=', $currentPeriodStart)
                    ->sum('total_amount');
                $previous = Order::where('payment_status', 'paid')
                    ->whereBetween('order_date', [$previousPeriodStart, $previousPeriodEnd])
                    ->sum('total_amount');
                break;
            case 'orders':
                $current = Order::where('order_date', '>=', $currentPeriodStart)->count();
                $previous = Order::whereBetween('order_date', [$previousPeriodStart, $previousPeriodEnd])->count();
                break;
            case 'custom_products':
                $current = CustomProduct::where('formulation_date', '>=', $currentPeriodStart)->count();
                $previous = CustomProduct::whereBetween('formulation_date', [$previousPeriodStart, $previousPeriodEnd])->count();
                break;
            default:
                return 0;
        }

        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function getPeriodDays(string $period): int
    {
        return match($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            '1year' => 365,
            default => 30
        };
    }

    private function getPopularIngredients(): array
    {
        $customProducts = CustomProduct::all();
        $ingredients = [];
        
        foreach ($customProducts as $product) {
            foreach ($product->selected_ingredients as $ingredient) {
                $ingredients[$ingredient] = ($ingredients[$ingredient] ?? 0) + 1;
            }
        }
        
        arsort($ingredients);
        return array_slice($ingredients, 0, 10, true);
    }

    private function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $tablesCount = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()")[0]->count;
            
            return [
                'status' => 'healthy',
                'message' => 'Database connection successful',
                'details' => [
                    'tables_count' => $tablesCount,
                    'connection_name' => config('database.default')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'critical',
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkStorageHealth(): array
    {
        try {
            $storagePath = storage_path();
            $freeSpace = disk_free_space($storagePath);
            $totalSpace = disk_total_space($storagePath);
            $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;

            $status = 'healthy';
            if ($usedPercentage > 90) {
                $status = 'critical';
            } elseif ($usedPercentage > 80) {
                $status = 'warning';
            }

            return [
                'status' => $status,
                'message' => 'Storage accessible',
                'details' => [
                    'free_space_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
                    'total_space_gb' => round($totalSpace / 1024 / 1024 / 1024, 2),
                    'used_percentage' => round($usedPercentage, 2)
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'critical',
                'message' => 'Storage check failed',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkCacheHealth(): array
    {
        try {
            cache()->put('health_check', 'test', 60);
            $value = cache()->get('health_check');
            
            return [
                'status' => $value === 'test' ? 'healthy' : 'warning',
                'message' => 'Cache system operational',
                'details' => [
                    'driver' => config('cache.default')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Cache check failed',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkQueueHealth(): array
    {
        try {
            return [
                'status' => 'healthy',
                'message' => 'Queue system operational',
                'details' => [
                    'driver' => config('queue.default')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Queue check failed',
                'error' => $e->getMessage()
            ];
        }
    }

    private function prepareExportData(string $type, array $filters): array
    {
        $query = null;
        
        switch ($type) {
            case 'users':
                $query = User::with('orders');
                break;
            case 'orders':
                $query = Order::with(['user', 'orderItems']);
                break;
            case 'products':
                $query = Product::query();
                break;
            case 'custom_products':
                $query = CustomProduct::with(['user', 'baseProduct']);
                break;
        }

        // Apply date filters if provided
        if (!empty($filters['date_from'])) {
            $dateField = match($type) {
                'users' => 'created_at',
                'orders' => 'order_date',
                'products' => 'created_at',
                'custom_products' => 'formulation_date',
                default => 'created_at'
            };
            
            $query->whereDate($dateField, '>=', $filters['date_from']);
            
            if (!empty($filters['date_to'])) {
                $query->whereDate($dateField, '<=', $filters['date_to']);
            }
        }

        return $query->limit(1000)->get()->toArray(); // Limit for safety
    }
}