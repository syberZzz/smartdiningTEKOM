<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index(Request $request)
    {
        $query = RestaurantTable::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where('table_number', 'like', "%{$search}%");
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $tables = $query->orderBy('table_number')->paginate(12)->withQueryString();

        return view('owner.tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create()
    {
        return view('owner.tables.create');
    }

    /**
     * Store a newly created table.
     */
    public function store(StoreTableRequest $request)
    {
        RestaurantTable::create($request->validated());

        return redirect()->route('owner.tables.index')
            ->with('success', 'Meja berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(RestaurantTable $table)
    {
        return view('owner.tables.edit', compact('table'));
    }

    /**
     * Update the specified table.
     */
    public function update(UpdateTableRequest $request, RestaurantTable $table)
    {
        $table->update($request->validated());

        return redirect()->route('owner.tables.index')
            ->with('success', 'Meja berhasil diperbarui.');
    }

    /**
     * Remove the specified table.
     */
    public function destroy(RestaurantTable $table)
    {
        $table->delete();

        return redirect()->route('owner.tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Show QR Code preview for a table.
     */
    public function showQr(RestaurantTable $table)
    {
        $qrUrl = url("/order/{$table->id}");
        $qrSvg = QrCode::size(300)->generate($qrUrl);

        return view('owner.tables.qr', compact('table', 'qrUrl', 'qrSvg'));
    }

    /**
     * Download QR Code as PNG or SVG.
     */
    public function downloadQr(RestaurantTable $table, Request $request)
    {
        $qrUrl     = url("/order/{$table->id}");
        // Gunakan SVG — tidak membutuhkan ekstensi Imagick
        $qrContent = QrCode::size(400)->generate($qrUrl);
        $fileName  = "table-{$table->table_number}-qr.svg";

        return response($qrContent, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Print-friendly QR Code page.
     */
    public function printQr(RestaurantTable $table)
    {
        $qrUrl = url("/order/{$table->id}");
        $qrSvg = QrCode::size(400)->generate($qrUrl);

        return view('owner.tables.print-qr', compact('table', 'qrUrl', 'qrSvg'));
    }


    /**
     * Download all active tables' QR codes as a single PDF file.
     */
    public function downloadAllQrPdf(Request $request)
    {
        $statusFilter = $request->input('status_filter', 'active');

        $query = RestaurantTable::query();

        if ($statusFilter === 'available') {
            $query->where('status', 'available');
        } else {
            $query->where('status', '!=', 'disabled');
        }

        $tables = $query->orderBy('table_number')->get();

        if ($tables->isEmpty()) {
            return redirect()->route('owner.tables.index')
                ->with('error', 'Tidak ada meja aktif yang ditemukan untuk diunduh.');
        }

        // Generate QR codes as base64 data URIs (DomPDF can't render inline SVG properly)
        $qrImages = [];
        $qrUrls   = [];

        foreach ($tables as $table) {
            $qrUrl = url("/order/{$table->id}");
            $qrUrls[$table->id]  = $qrUrl;
            
            $svgString = QrCode::size(300)->margin(2)->generate($qrUrl);
            $qrImages[$table->id] = 'data:image/svg+xml;base64,' . base64_encode($svgString);
        }

        $pdf = Pdf::loadView('owner.tables.qr-all-pdf', compact('tables', 'qrImages', 'qrUrls', 'statusFilter'))
            ->setPaper('a4', 'portrait');

        $fileName = 'qr-codes-all-tables-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($fileName);
    }
}
