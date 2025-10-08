<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SchoolSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display the school settings page
     */
    public function index()
    {
        // Only admins can access settings
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can access system settings.');
        }

        $settings = SchoolSettings::getInstance();
        
        // Add full URL for logo if it exists
        if ($settings->school_logo) {
            $settings->school_logo_url = asset('storage/' . $settings->school_logo);
        }

        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Update school settings
     */
    public function update(Request $request)
    {
        // Only admins can update settings
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can update system settings.');
        }

        $validated = $request->validate([
            'school_name' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:1000',
            'school_phone' => 'nullable|string|max:20',
            'school_email' => 'nullable|email|max:255',
            'principal_name' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            $logoFile = $request->file('school_logo');
            $logoPath = $logoFile->store('logos', 'public');
            $validated['school_logo'] = $logoPath;
        }

        $settings = SchoolSettings::getInstance();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'School settings updated successfully.');
    }
}
