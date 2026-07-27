{{-- Alpine.js (load only once via layout, this is a safety fallback) --}}
@once
    @push('styles')
    <style>[x-cloak] { display: none !important; }</style>
    @endpush
@endonce

@php
$iconGroups = [
    'Makanan & Minuman' => [
        'restaurant'          => 'Restoran',
        'restaurant_menu'     => 'Menu',
        'local_dining'        => 'Dining',
        'fastfood'            => 'Fast Food',
        'lunch_dining'        => 'Makan Siang',
        'dinner_dining'       => 'Makan Malam',
        'breakfast_dining'    => 'Sarapan',
        'brunch_dining'       => 'Brunch',
        'ramen_dining'        => 'Mie/Ramen',
        'rice_bowl'           => 'Nasi',
        'bakery_dining'       => 'Bakeri',
        'kebab_dining'        => 'Kebab',
        'tapas'               => 'Tapas',
        'set_meal'            => 'Paket Makan',
        'cake'                => 'Kue',
        'cookie'              => 'Kue Kering',
        'icecream'            => 'Es Krim',
        'local_pizza'         => 'Pizza',
        'local_cafe'          => 'Kafe',
        'coffee'              => 'Kopi',
        'emoji_food_beverage' => 'Minuman',
        'local_bar'           => 'Bar',
        'sports_bar'          => 'Sports Bar',
        'liquor'              => 'Minuman Keras',
        'wine_bar'            => 'Wine',
        'nightlife'           => 'Nightlife',
    ],
    'Peralatan Masak' => [
        'kitchen'             => 'Dapur',
        'blender'             => 'Blender',
        'microwave'           => 'Microwave',
        'soup_kitchen'        => 'Sup',
        'outdoor_grill'       => 'Grill/BBQ',
        'skillet'             => 'Wajan',
    ],
    'Kategori Umum' => [
        'category'            => 'Kategori',
        'star'                => 'Favorit',
        'favorite'            => 'Suka',
        'thumb_up'            => 'Populer',
        'new_releases'        => 'Baru',
        'local_offer'         => 'Promo',
        'whatshot'            => 'Terlaris',
        'grade'               => 'Pilihan',
        'verified'            => 'Unggulan',
        'recommend'           => 'Rekomendasi',
        'eco'                 => 'Sehat/Vegan',
        'grass'               => 'Vegetarian',
        'spa'                 => 'Organik',
        'local_florist'       => 'Segar',
        'water_drop'          => 'Minuman',
        'grain'               => 'Biji-bijian',
        'agriculture'         => 'Pertanian',
    ],
    'Gaya Hidup' => [
        'child_care'          => 'Anak',
        'family_restroom'     => 'Keluarga',
        'groups'              => 'Grup',
        'celebration'         => 'Pesta',
        'emoji_events'        => 'Event',
        'park'                => 'Outdoor',
        'beach_access'        => 'Pantai',
        'takeout_dining'      => 'Takeout',
        'delivery_dining'     => 'Delivery',
    ],
];

$allIcons = [];
foreach ($iconGroups as $icons) {
    $allIcons = array_merge($allIcons, $icons);
}
@endphp

<div x-data="{
    selected: '{{ $selectedIcon ?? 'category' }}',
    search: '',
    open: false,
    groups: {{ json_encode($iconGroups) }},
    get filtered() {
        if (!this.search) return this.groups;
        const q = this.search.toLowerCase();
        let result = {};
        for (const [group, icons] of Object.entries(this.groups)) {
            const matches = Object.fromEntries(
                Object.entries(icons).filter(([k, v]) =>
                    k.includes(q) || v.toLowerCase().includes(q)
                )
            );
            if (Object.keys(matches).length) result[group] = matches;
        }
        return result;
    },
    get totalFiltered() {
        return Object.values(this.filtered).reduce((sum, g) => sum + Object.keys(g).length, 0);
    }
}" class="space-y-2">
    <label class="block text-label-md text-on-surface mb-1.5">
        Icon Kategori
        <span class="text-label-sm text-on-surface-variant font-normal ml-1">(pilih dari daftar di bawah)</span>
    </label>

    {{-- Hidden input --}}
    <input type="hidden" name="icon" :value="selected">

    {{-- Preview + Toggle Button --}}
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-3 flex-1 px-4 py-2.5 border border-outline-variant rounded-xl bg-surface cursor-pointer hover:border-primary transition-colors"
             @click="open = !open">
            <span class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-primary text-[22px]" x-text="selected"></span>
            </span>
            <span class="text-body-md text-on-surface flex-1" x-text="selected.replace(/_/g, ' ')"></span>
            <span class="material-symbols-outlined text-on-surface-variant text-[20px] transition-transform duration-200"
                  :class="open ? 'rotate-180' : ''" style="transform-origin: center;">expand_more</span>
        </div>
    </div>

    {{-- Picker Panel --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="border border-outline-variant rounded-2xl bg-surface-container-lowest shadow-lg overflow-hidden">

        {{-- Search in Picker --}}
        <div class="p-3 border-b border-outline-variant bg-surface-container-low">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input type="text" x-model="search" placeholder="Cari icon..."
                       class="w-full pl-9 pr-4 py-2 border border-outline-variant rounded-xl bg-surface text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors text-[14px]"
                       @click.stop />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-label-xs text-on-surface-variant text-[11px]"
                      x-show="search" x-text="totalFiltered + ' icon'"></span>
            </div>
        </div>

        {{-- Icons Grid --}}
        <div class="overflow-y-auto max-h-72 p-3 space-y-4">
            <template x-for="[groupName, icons] in Object.entries(filtered)" :key="groupName">
                <div>
                    <p class="text-label-xs text-on-surface-variant uppercase tracking-wider mb-2 text-[10px] font-semibold px-1"
                       x-text="groupName"></p>
                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="[iconKey, iconLabel] in Object.entries(icons)" :key="iconKey">
                            <button type="button"
                                    :title="iconLabel + ' (' + iconKey + ')'"
                                    @click="selected = iconKey; open = false; search = ''"
                                    :class="selected === iconKey
                                        ? 'bg-primary text-on-primary ring-2 ring-primary ring-offset-1'
                                        : 'bg-surface-container text-on-surface-variant hover:bg-primary/10 hover:text-primary'"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-150 group relative">
                                <span class="material-symbols-outlined text-[20px]" x-text="iconKey"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Empty state --}}
            <div x-show="totalFiltered === 0" class="py-8 text-center">
                <span class="material-symbols-outlined text-[40px] text-outline-variant block mb-2">search_off</span>
                <p class="text-label-sm text-on-surface-variant">Tidak ada icon ditemukan</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-outline-variant bg-surface-container-low flex items-center justify-between">
            <span class="text-label-xs text-on-surface-variant text-[11px]">
                Dipilih: <span class="font-semibold text-primary" x-text="selected.replace(/_/g, ' ')"></span>
            </span>
            <button type="button" @click="open = false"
                    class="text-label-xs text-primary hover:underline text-[12px]">Tutup</button>
        </div>
    </div>

    @error('icon') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
</div>
