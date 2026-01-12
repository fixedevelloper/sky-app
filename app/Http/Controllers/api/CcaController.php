<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CcaEnrollment; // modèle pour enregistrer l'enrôlement


class CcaController extends Controller
{
    public function submit(Request $request)
    {
        // Récupérer les infos Step1
        $step1 = json_decode($request->input('step1'), true);

        // Créer un enregistrement en DB si nécessaire
        $enrollment = CcaEnrollment::create([
            'type' => $step1['type'],
            'name' => $step1['name'],
            'accounts' => $step1['accounts'],
            'niu' => $step1['niu'] ?? null,
            'position' => $step1['position'],
        ]);

        // Stocker les fichiers
        $uploadedUrls = [];
        $filesMap = [
            'cni_recto',
            'cni_verso',
            'niu_image',
            'facture',
            'demi_carte',
            'justificatif',
        ];

        foreach ($filesMap as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store("cca_enrollments/{$enrollment->id}", 'public');
                $uploadedUrls[$key] = asset("storage/$path");
            }
        }

        // Enregistrer les URLs dans l’enrôlement
        $enrollment->documents = json_encode($uploadedUrls);
        $enrollment->save();

        // Générer le message WhatsApp
        $whatsappNumber = '237683806782';
        $message = "Bonjour CCA 👋\n\nJe souhaite m'enrôler :\n\n";
        $message .= "Type : {$enrollment->type}\n";
        $message .= "Nom / PME : {$enrollment->name}\n";
        $message .= "Nombre de comptes : {$enrollment->accounts}\n";
        if ($enrollment->niu) $message .= "NIU : {$enrollment->niu}\n";
        $message .= "Position : {$enrollment->position}\n\nDocuments :\n";

        foreach ($uploadedUrls as $label => $url) {
            $message .= "- $label : $url\n";
        }

        $whatsappLink = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'whatsapp_link' => $whatsappLink
        ]);
    }

}
