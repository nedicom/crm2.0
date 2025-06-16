<?php

namespace App\Http\Controllers;

use App\Models\Prompt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromptController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);

        Prompt::create([
            'prompt' => $validated['prompt'],
        ]);

        return redirect()->back()->with('success', 'Промпт успешно сохранен');
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        DB::table('gptsettings')
            ->where('id', 1)
            ->update(['global_gpt_active' => $request->is_active]);

        return response()->json(['success' => true]);
    }
}
