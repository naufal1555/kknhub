<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\OprecCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $location = $request->query('location');

        $query = OprecCatalog::with(['team.pic', 'divisions'])
            ->where('status', 'published')
            ->where('deadline', '>', now());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('team', function ($teamQuery) use ($search) {
                      $teamQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('target_location', 'like', "%{$search}%");
                  });
            });
        }

        if ($location) {
            $query->whereHas('team', function ($q) use ($location) {
                $q->where('target_location', 'like', "%{$location}%");
            });
        }

        $catalogs = $query->orderBy('deadline', 'asc')->paginate(12);

        return view('catalog.index', [
            'catalogs' => $catalogs,
            'search' => $search,
            'location' => $location,
        ]);
    }

    public function show(int $id): View
    {
        $catalog = OprecCatalog::with(['team.pic', 'team.contactPersons', 'divisions'])
            ->where('status', 'published')
            ->findOrFail($id);

        $user = Auth::user();

        // Cek apakah mahasiswa ini sudah mendaftar di tim ini
        $existingApplication = null;
        if ($user) {
            $existingApplication = Application::with('division')
                ->where('user_id', $user->id)
                ->where('team_id', $catalog->team_id)
                ->first();
        }

        // Hitung kuota tersisa per divisi
        $divisionStats = [];
        foreach ($catalog->divisions as $division) {
            $acceptedCount = Application::where('division_id', $division->id)
                ->where('status', 'accepted')
                ->count();
            $divisionStats[$division->id] = [
                'accepted' => $acceptedCount,
                'remaining' => max(0, $division->quota - $acceptedCount),
            ];
        }

        return view('catalog.show', [
            'catalog' => $catalog,
            'existingApplication' => $existingApplication,
            'divisionStats' => $divisionStats,
        ]);
    }
}
