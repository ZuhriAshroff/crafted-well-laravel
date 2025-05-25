<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display user profile dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        $profile = UserProfile::getLatestForUser($user->user_id);
        
        return view('profile.skin-profile', [
            'profile' => $profile,
            'completion' => $profile ? $profile->getCompletionPercentage() : 0,
            'skinTypes' => UserProfile::getSkinTypeOptions(),
            'skinConcerns' => UserProfile::getSkinConcernsOptions(),
            'environmentalFactors' => UserProfile::getEnvironmentalFactorsOptions(),
        ]);
    }

    /**
     * Display profile creation wizard
     */
    public function create(): View
    {
        return view('profile.create-wizard', [
            'skinTypes' => UserProfile::getSkinTypeOptions(),
            'skinConcerns' => UserProfile::getSkinConcernsOptions(),
            'environmentalFactors' => UserProfile::getEnvironmentalFactorsOptions(),
        ]);
    }

    /**
     * Store a new profile (from web form)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(UserProfile::validationRules());
        
        try {
            $user = auth()->user();
            $data = $request->validated();
            
            $profile = UserProfile::createOrUpdateForUser($user->user_id, $data);
            
            return redirect()->route('profile.index')
                ->with('success', 'Profile created successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create profile. Please try again.');
        }
    }

    /**
     * Display profile edit form
     */
    public function edit($profileId = null): View|RedirectResponse
    {
        $user = auth()->user();
        
        if ($profileId) {
            $profile = UserProfile::forUser($user->user_id)->findOrFail($profileId);
        } else {
            $profile = UserProfile::getLatestForUser($user->user_id);
        }

        if (!$profile) {
            return redirect()->route('profile.create')
                ->with('info', 'Please create a profile first.');
        }

        return view('profile.edit', [
            'profile' => $profile,
            'skinTypes' => UserProfile::getSkinTypeOptions(),
            'skinConcerns' => UserProfile::getSkinConcernsOptions(),
            'environmentalFactors' => UserProfile::getEnvironmentalFactorsOptions(),
        ]);
    }

    /**
     * Update a profile (from web form)
     */
    public function update(Request $request, $profileId): RedirectResponse
    {
        $request->validate(UserProfile::validationRules(true));
        
        try {
            $user = auth()->user();
            $profile = UserProfile::forUser($user->user_id)->findOrFail($profileId);
            
            $profile->update($request->validated());
            
            return redirect()->route('profile.index')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Delete a profile
     */
    public function destroy($profileId): RedirectResponse
    {
        try {
            $user = auth()->user();
            $profile = UserProfile::forUser($user->user_id)->findOrFail($profileId);
            
            $profile->delete();
            
            return redirect()->route('profile.index')
                ->with('success', 'Profile deleted successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete profile. Please try again.');
        }
    }

    /**
     * Display product recommendations based on profile
     */
    public function recommendations(): View|RedirectResponse
    {
        $user = auth()->user();
        $profile = UserProfile::getLatestForUser($user->user_id);
        
        if (!$profile) {
            return redirect()->route('profile.create')
                ->with('info', 'Please create a profile first to get recommendations.');
        }

        $recommendations = $profile->getRecommendedProducts();
        
        return view('profile.recommendations', [
            'profile' => $profile,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Display profile analytics
     */
    public function analytics(): View
    {
        $user = auth()->user();
        $profiles = UserProfile::forUser($user->user_id)->get();
        
        $analytics = [
            'total_profiles' => $profiles->count(),
            'completion_average' => $profiles->avg(fn($p) => $p->getCompletionPercentage()),
            'skin_types' => $profiles->groupBy('skin_type')->map->count(),
            'concerns' => $profiles->pluck('primary_skin_concerns')->groupBy(fn($item) => $item)->map->count(),
        ];
        
        return view('profile.analytics', [
            'profiles' => $profiles,
            'analytics' => $analytics,
        ]);
    }
}
