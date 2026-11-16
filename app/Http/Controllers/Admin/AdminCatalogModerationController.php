<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OprecCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCatalogModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = OprecCatalog::with(['team.pic', 'divisions'])->latest();

        if ($status && in_array($status, ['draft', 'pending_approval', 'published', 'rejected', 'closed'], true)) {
            $query->where('status', $status);
        }

        $catalogs = $query->paginate(15);

        return view('admin.catalogs.index', [
            'catalogs' => $catalogs,
            'currentStatus' => $status,
        ]);
    }

    public function show(int $id): View
    {
        $catalog = OprecCatalog::with(['team.pic', 'team.contactPersons', 'divisions'])->findOrFail($id);

        return view('admin.catalogs.show', [
            'catalog' => $catalog,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $catalog = OprecCatalog::findOrFail($id);

        if ($catalog->status !== 'pending_approval') {
            return back()->with('error', 'Hanya katalog berstatus Pending Approval yang dapat disetujui.');
        }

        $catalog->update([
            'status' => 'published',
            'rejection_note' => null,
        ]);

        return redirect()->route('admin.catalogs.index')->with('success', "Katalog '{$catalog->title}' berhasil disetujui dan berstatus PUBLISHED. Kini langsung tampil di portal publik.");
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_note' => ['required', 'string', 'max:1000'],
        ], [
            'rejection_note.required' => 'Catatan alasan penolakan katalog wajib diisi.',
        ]);

        $catalog = OprecCatalog::findOrFail($id);

        if ($catalog->status !== 'pending_approval') {
            return back()->with('error', 'Hanya katalog berstatus Pending Approval yang dapat ditolak.');
        }

        $catalog->update([
            'status' => 'rejected',
            'rejection_note' => $request->input('rejection_note'),
        ]);

        return redirect()->route('admin.catalogs.index')->with('success', "Katalog '{$catalog->title}' telah ditolak dengan catatan evaluasi.");
    }
}
