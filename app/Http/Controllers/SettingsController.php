<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'child_max_age' => config('membership.child_max_age', 18),
            'age_reference' => config('membership.age_reference', 'today'),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'child_max_age' => 'required|integer|min:1|max:30',
            'age_reference' => 'required|in:today,end_of_year',
        ]);

        $this->writeEnv([
            'MEMBERSHIP_CHILD_MAX_AGE' => (string) $validated['child_max_age'],
            'MEMBERSHIP_AGE_REFERENCE' => $validated['age_reference'],
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    private function writeEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }
        $content = file_get_contents($envPath);
        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $line = $key.'='.preg_replace('/\n|\r/', '', $value);
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $line, $content);
            } else {
                $content .= "\n".$line;
            }
        }
        file_put_contents($envPath, $content);
    }
}


