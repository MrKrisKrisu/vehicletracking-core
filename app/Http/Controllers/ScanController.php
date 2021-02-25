<?php

namespace App\Http\Controllers;

use App\Scan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanController extends Controller {
    public function update(Request $request): JsonResponse {
        $validated = $request->validate([
                                            'id'     => ['required', 'exists:scans,id'],
                                            'hidden' => ['nullable', 'boolean']
                                        ]);

        $scan = Scan::find($validated['id']);
        $scan->update($validated);

        return response()->json(['success' => true]);
    }
}
