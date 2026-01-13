<?php


namespace App\Http\Controllers;

use App\Models\Pme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PmeController extends Controller
{
    public function index()
    {
        $items = Pme::with('user')->latest()->paginate(20);
        return view('admin.pmes.index', compact('items'));
    }

    public function create()
    {
        $users = User::whereJsonContains('roles', 'vendor')
            ->orWhereJsonContains('roles', 'pme')
            ->orderBy('name')
            ->get();

        return view('admin.pmes.create', compact('users'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'operator' => 'nullable|in:MTN,ORANGE',
            'name_entreprise' => 'required|string|max:255',
            'name_responsable' => 'required|string|max:255',
            'poste_responsable' => 'required|string|max:255',
            'amount_bc' => 'required|numeric|min:0',
            'number_souscripteur' => 'required|integer|min:1',
            'number_echeance_paiement' => 'required|integer|min:1',
            'montant_total' => 'required|numeric|min:0',
            'name_gestionnaire' => 'required|string|max:255',
            'name_manager' => 'required|string|max:255',

            // Images
            'image_bc' => 'required|image|max:2048',
            'image_bl' => 'required|image|max:2048',
            'image_facture' => 'required|image|max:2048',
            'image_avi' => 'required|image|max:2048',
            'image_pl' => 'required|image|max:2048',
            'image_contract1' => 'required|image|max:2048',
            'image_contract2' => 'required|image|max:2048',
        ]);

        $validated['referenceId'] = Str::uuid();

        foreach ([
                     'image_bc','image_bl','image_facture','image_avi',
                     'image_pl','image_contract1','image_contract2'
                 ] as $img) {
            $validated[$img] = $request->file($img)->store('pmes', 'public');
        }

        Pme::create($validated);

        return redirect()->route('pmes.index')
            ->with('success', 'PME créée avec succès');
    }

    public function show(Pme $pme)
    {
        return view('admin.pmes.show', compact('pme'));
    }

    public function edit(Pme $pme)
    {
        $users = User::whereJsonContains('roles', 'vendor')
            ->orWhereJsonContains('roles', 'pme')
            ->orderBy('name')
            ->get();

        return view('admin.pmes.edit', [
            'item' => $pme,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Pme $pme)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'operator' => 'nullable|in:MTN,ORANGE',
            'name_entreprise' => 'required|string|max:255',
            'name_responsable' => 'required|string|max:255',
            'poste_responsable' => 'required|string|max:255',
            'amount_bc' => 'required|numeric|min:0',
            'number_souscripteur' => 'required|integer|min:1',
            'number_echeance_paiement' => 'required|integer|min:1',
            'montant_total' => 'required|numeric|min:0',
            'name_gestionnaire' => 'required|string|max:255',
            'name_manager' => 'required|string|max:255',
            'status' => 'required|in:pending,confirmed,failed',

            // Images facultatives
            'image_bc' => 'nullable|image|max:2048',
            'image_bl' => 'nullable|image|max:2048',
            'image_facture' => 'nullable|image|max:2048',
            'image_avi' => 'nullable|image|max:2048',
            'image_pl' => 'nullable|image|max:2048',
            'image_contract1' => 'nullable|image|max:2048',
            'image_contract2' => 'nullable|image|max:2048',
        ]);

        foreach ([
                     'image_bc','image_bl','image_facture','image_avi',
                     'image_pl','image_contract1','image_contract2'
                 ] as $img) {
            if ($request->hasFile($img)) {
                Storage::disk('public')->delete($pme->$img);
                $validated[$img] = $request->file($img)->store('pmes', 'public');
            }
        }

        if ($validated['status'] === 'confirmed' && !$pme->confirmed_at) {
            $validated['confirmed_at'] = now();
        }

        $pme->update($validated);

        return redirect()->route('pmes.index')
            ->with('success', 'PME mise à jour');
    }

    public function destroy(Pme $pme)
    {
        foreach ([
                     'image_bc','image_bl','image_facture','image_avi',
                     'image_pl','image_contract1','image_contract2'
                 ] as $img) {
            Storage::disk('public')->delete($pme->$img);
        }

        $pme->delete();

        return back()->with('success', 'PME supprimée');
    }
}

