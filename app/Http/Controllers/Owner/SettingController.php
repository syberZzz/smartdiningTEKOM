<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $taxRate = Setting::getTaxRate();

        return view('owner.settings.index', compact('taxRate'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'tax_rate' => 'required|numeric|min:0|max:100',
        ], [
            'tax_rate.required' => 'Persentase PPN wajib diisi.',
            'tax_rate.numeric'  => 'Persentase PPN harus berupa angka.',
            'tax_rate.min'      => 'Persentase PPN tidak boleh kurang dari 0%.',
            'tax_rate.max'      => 'Persentase PPN tidak boleh lebih dari 100%.',
        ]);

        Setting::set('tax_rate', $validated['tax_rate']);

        return redirect()
            ->route('owner.settings.index')
            ->with('success', 'Pengaturan PPN berhasil diperbarui menjadi ' . $validated['tax_rate'] . '%.');
    }
}
