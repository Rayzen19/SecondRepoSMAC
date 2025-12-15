<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->get();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string',
                'settings.*.value' => 'required',
            ]);

            // Update each setting
            foreach ($validated['settings'] as $setting) {
                $existingSetting = SystemSetting::where('key', $setting['key'])->first();
                
                if ($existingSetting) {
                    // Validate based on type
                    $value = $setting['value'];
                    
                    if ($existingSetting->type === 'integer') {
                        $value = (int) $value;
                        
                        // Additional validation for specific settings
                        if (in_array($setting['key'], ['max_teacher_sections'])) {
                            if ($value < 1 || $value > 100) {
                                return redirect()->back()
                                    ->with('error', 'Value for ' . $setting['key'] . ' must be between 1 and 100')
                                    ->withInput();
                            }
                        }
                    } elseif ($existingSetting->type === 'boolean') {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                    }
                    
                    $existingSetting->update(['value' => $value]);
                }
            }

            Log::info('System settings updated', ['user' => auth()->user()->email]);

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Error updating settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating settings.')
                ->withInput();
        }
    }
}
