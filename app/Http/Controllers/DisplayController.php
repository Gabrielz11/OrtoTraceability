<?php

namespace App\Http\Controllers;

use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class DisplayController extends Controller
{
    /**
     * Show the real-time surgery display (TV Kiosk).
     *
     * Protected by 'signed' middleware in routes.
     */
    public function show(Surgery $surgery)
    {
        // Load relationships needed for the display
        $surgery->load(['materials' => function ($query) {
            $query->orderBy('pivot_updated_at', 'desc');
        }]);

        return view('display.surgery', [
            'surgery' => $surgery,
        ]);
    }

    /**
     * Generate a signed URL for the TV display.
     * Accessible only by admins/auditors.
     */
    public function generateUrl(Surgery $surgery)
    {
        $url = URL::signedRoute('display.surgery', ['surgery' => $surgery->id]);

        return response()->json([
            'url' => $url
        ]);
    }

    /**
     * API endpoint to get current materials status for a surgery.
     * Used by the Kiosk for real-time list refreshing.
     */
    public function materialsStatus(Surgery $surgery)
    {
        $materials = $surgery->materials()
            ->withPivot('acao')
            ->orderBy('surgery_material.updated_at', 'desc')
            ->get();

        return response()->json([
            'materials' => $materials
        ]);
    }
}
