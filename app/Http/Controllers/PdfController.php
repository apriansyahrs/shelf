<?php

namespace App\Http\Controllers;

use App\Models\AssetTransfer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function downloadAssetTransfer($id)
    {
        $assetTransfer = AssetTransfer::with('fromUser.jobTitle', 'toUser.jobTitle', 'businessEntity', 'details.asset.category', 'details.asset.brand')->findOrFail($id);

        // Pemetaan status ke singkatan
        $statusMap = [
            'BERITA ACARA SERAH TERIMA' => 'BA',
            'BERITA ACARA PENGALIHAN BARANG' => 'BAPAB',
            'BERITA ACARA PENGEMBALIAN BARANG' => 'BAPEB',
        ];

        // Pemetaan entitas bisnis ke gambar header
        $headerImageMap = [
            'MAJU' => 'images/maju_kop.png',
            'MKLI' => 'images/mkli_kop.png',
            'TOP' => 'images/top_kop.png',
            'RISM' => 'images/rism_kop.png',
            'CV.CS' => 'images/cvcs_kop.png',
        ];

        $status = $statusMap[$assetTransfer->status] ?? 'UNKNOWN';
        $headerImage = $headerImageMap[$assetTransfer->businessEntity->name] ?? 'images/default_kop.png';
        $letterNumber = $assetTransfer->letter_number;
        $toUserName = strtolower(str_replace(' ', '_', $assetTransfer->toUser->name));
        $toUserJobTitle = $assetTransfer->toUser->jobTitle ? strtolower(str_replace(' ', '_', $assetTransfer->toUser->jobTitle->title)) : 'no_title';

        $fileName = "{$status}_{$letterNumber}_{$toUserName}_{$toUserJobTitle}.pdf";

        $pdf = Pdf::loadView('pdf.asset-transfer', compact('assetTransfer', 'headerImage'));

        return $pdf->download($fileName);
    }
}
