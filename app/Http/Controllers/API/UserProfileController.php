<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserProfileController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('active.user'); // Custom middleware to check if user is active
    }

    /**
     * Display a listing of user's profiles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profiles = UserProfile::forUser($user->user_id)
                ->latest()
                ->get()
                ->map(fn($profile) => $profile->getFormattedData());

            return response()->json([
                'status' => 'success',
                'data' => $profiles,
                'count' => $profiles->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching user profiles: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch profiles'
            ], 500);
        }
    }

    /**
     * Store a newly created profile.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate the request data
            $validator = Validator::make($request->all(), UserProfile::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = $user->user_id;

            // Create the profile
            $profile = UserProfile::create($data);

            // Validate the created profile data
            $profile->validateProfileData();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile created successfully',
                'data' => $profile->getFormattedData()
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
            \Log::error('Error creating user profile: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create profile'
            ], 500);
        }
    }

    /**
     * Display the latest profile for the authenticated user.
     */
    public function show(Request $request, ?int $profileId = null): JsonResponse
    {
        try {
            $user = $request->user();

            if ($profileId === null || $profileId === 'latest') {
                // Get latest profile
                $profile = UserProfile::getLatestForUser($user->user_id);
            } else {
                // Get specific profile
                $profile = UserProfile::forUser($user->user_id)->find($profileId);
            }

            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $profile->getFormattedData()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching user profile: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch profile'
            ], 500);
        }
    }

    /**
     * Update the specified profile.
     */
    public function update(Request $request, int $profileId): JsonResponse
    {
        try {
            $user = $request->user();

            // Find the profile
            $profile = UserProfile::forUser($user->user_id)->find($profileId);

            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile not found'
                ], 404);
            }

            // Validate the request data
            $validator = Validator::make($request->all(), UserProfile::validationRules(true));

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the profile
            $profile->update($validator->validated());

            // Validate the updated profile data
            $profile->validateProfileData();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => $profile->getFormattedData()
            ]);

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
            \Log::error('Error updating user profile: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    /**
     * Remove the specified profile.
     */
    public function destroy(Request $request, int $profileId): JsonResponse
    {
        try {
            $user = $request->user();

            // Find the profile
            $profile = UserProfile::forUser($user->user_id)->find($profileId);

            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile not found'
                ], 404);
            }

            $profile->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting user profile: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete profile'
            ], 500);
        }
    }

    /**
     * Validate skin type (step-by-step validation)
     */
    public function validateSkinType(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), UserProfile::skinTypeValidationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'valid' => true,
                'skin_type' => $request->skin_type
            ]);

        } catch (\Exception $e) {
            \Log::error('Error validating skin type: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed'
            ], 400);
        }
    }

    /**
     * Validate skin concerns (step-by-step validation)
     */
    public function validateSkinConcerns(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), UserProfile::skinConcernsValidationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'valid' => true,
                'skin_concerns' => [
                    'primary' => $request->primary_skin_concerns,
                    'secondary' => $request->secondary_skin_concerns
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error validating skin concerns: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed'
            ], 400);
        }
    }

    /**
     * Validate environmental factors (step-by-step validation)
     */
    public function validateEnvironmentalFactors(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), UserProfile::environmentalFactorsValidationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'valid' => true,
                'environmental_factors' => $request->environmental_factors
            ]);

        } catch (\Exception $e) {
            \Log::error('Error validating environmental factors: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed'
            ], 400);
        }
    }

    /**
     * Create or update profile (upsert functionality)
     */
    public function createOrUpdate(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate the request data
            $validator = Validator::make($request->all(), UserProfile::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create or update profile
            $profile = UserProfile::createOrUpdateForUser($user->user_id, $validator->validated());

            // Validate the profile data
            $profile->validateProfileData();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile saved successfully',
                'data' => $profile->getFormattedData()
            ]);

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
            \Log::error('Error saving user profile: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save profile'
            ], 500);
        }
    }

    /**
     * Get profile options for forms
     */
    public function getOptions(): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'skin_types' => UserProfile::getSkinTypeOptions(),
                    'skin_concerns' => UserProfile::getSkinConcernsOptions(),
                    'environmental_factors' => UserProfile::getEnvironmentalFactorsOptions(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching profile options: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch options'
            ], 500);
        }
    }

    /**
     * Get recommended products based on user profile
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profile = UserProfile::getLatestForUser($user->user_id);

            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No profile found. Please create a profile first.'
                ], 404);
            }

            $recommendations = $profile->getRecommendedProducts();

            return response()->json([
                'status' => 'success',
                'data' => $recommendations,
                'count' => $recommendations->count(),
                'based_on_profile' => $profile->getFormattedData()
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
     * Get analytics data for user profiles (for MongoDB integration)
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Log this request to MongoDB for analytics
            $this->logProfileAnalytics($user, 'profile_analytics_viewed');
            
            $profiles = UserProfile::forUser($user->user_id)->get();
            
            $analytics = [
                'total_profiles' => $profiles->count(),
                'completion_rates' => $profiles->map(fn($p) => $p->getCompletionPercentage())->avg(),
                'skin_type_distribution' => $profiles->groupBy('skin_type')->map->count(),
                'most_common_concerns' => $profiles->pluck('primary_skin_concerns')->groupBy(fn($item) => $item)->map->count()->sortDesc(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching profile analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * Log analytics to MongoDB
     */
    private function logProfileAnalytics($user, $action, $data = [])
    {
        try {
            // MongoDB logging for analytics
            $analyticsData = [
                'user_id' => $user->user_id,
                'action' => $action,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'additional_data' => $data
            ];

            // You can implement MongoDB Service here
            // MongoService::logUserActivity($analyticsData);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log analytics: ' . $e->getMessage());
            // Don't throw exception, just log the error
        }
    }
}