<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Category;

class MenuItemSeeder extends Seeder
{
    /**
     * Seed the menu items table.
     */
    public function run(): void
    {
        // Build a lookup map: category string => category_id
        $categoryMap = Category::pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtolower($name) => $id];
        })->toArray();

        $items = [
            [
                'name'        => 'Classic Burger',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAD_OOgSF8Kzf1rJ_S6xbYN0DCw7l5fPq93VlUbD37Sr6O8OAxCjFVcr3-zbj44YCDHcvaKzSllB2ejcBzf6sSMEMS2oKEtb-Lw3oItsjaiu7cXnMUuc-LoK8wo6VPSJOVAP0_VMr2FDqyYCoxjeEz5HCr3B7TtE5NP1MXLRLyce7YhQ4RY2SqJydgl2eNf6Y9p-cnCX-H4XTKKJtyYnFnlG81WKViiUu28xHI9UQho9id8EjBDUvc9',
                'image_alt'   => 'A juicy classic beef burger on a wooden board with toasted brioche bun, melted cheese, crisp lettuce, and thick beef patty.',
                'rating'      => 4.8,
                'time'        => '15m',
                'description' => 'Beef patty, cheddar, lettuce, tomato, house sauce.',
                'price'       => 16.00,
                'category'    => 'makanan',
                'popular'     => true,
            ],
            [
                'name'        => 'Margarita Pizza',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuChnYOli78Q3kG36sne6QpONw3cBSz0uHOKxs59uCQAzgNEUDBIRXL95eMMArPHjPfY6p-GG00lT6bZzD-7Ih9xQdIXfe7kPM-WsBT34GJxC-ug54Cz3-vg1h0kYFFoJdLaZFEr1hoI7H6U8R1MsyoTyxonXByEUVhr66A_m6uugisQs8BD3ba5yFTnf-g7C6IytIa4m-IpKHnmUE0YDYyTkfhqEunmTj_ZEIaCvD5hZQrMP4M7o5s6',
                'image_alt'   => 'A classic Margarita pizza with thin crust, rich tomato sauce, fresh mozzarella, and basil leaves.',
                'rating'      => 4.9,
                'time'        => '20m',
                'description' => 'Fresh mozzarella, basil, san marzano tomatoes.',
                'price'       => 18.00,
                'category'    => 'makanan',
                'popular'     => true,
            ],
            [
                'name'        => 'Salmon Fillet',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBU82kMPir-2cZWqzAoSNzhVGZcKX_hbk5Yw3ErOmkgYdiMjPOOdE9LKDRpMb5EEI7iBd9QYznwZHU0bYtqZ67SjnPd2epmQ-QHvbVMt5HwTL6ritW0O9uOudik_YxQpjEDKOctraQttm0-7DDJ_xLB0h-0bORvLNiKIyw-__wNpsSr6IMv0fwxm_yOQX7eGgf5LuFw2xa9W7s-yUC7Fb1rnampeahqEKPEYgG75q90ME3PdbuUrvje',
                'image_alt'   => 'A perfectly seared Salmon fillet on a bed of quinoa and asparagus.',
                'rating'      => 4.7,
                'time'        => '25m',
                'description' => 'Pan-seared Atlantic salmon with quinoa and asparagus.',
                'price'       => 28.00,
                'category'    => 'makanan',
                'popular'     => false,
            ],
            [
                'name'        => 'Garden Salad',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBQvXBm23vtefmPU0EPhmvEeCN-axFVsTYvuHAxgXkDNb4YEQJk-SlALv7t4uWIazJ42rCaErBpXjVqpYGTNlS4NNjDqqDOswnYTqjf1dYWBWf8u64RvU7a3LYvvAm0Q-WObaJsLHLJEY0VinEBoawQfxgiPoQcRvHqQdoJsAeSFOMEOH7HHSRwZaSwAK4omuYXCMe0kyrIg8wTwS2YQfGjtAmSyBGq1MsbLmht4qG5t9CCdg_xcLXZ',
                'image_alt'   => 'A fresh garden salad in a shallow white bowl with mixed greens, cherry tomatoes, and cucumber.',
                'rating'      => 4.6,
                'time'        => '10m',
                'description' => 'Mixed greens, cherry tomatoes, cucumber, vinaigrette.',
                'price'       => 12.00,
                'category'    => 'makanan',
                'popular'     => false,
            ],
            [
                'name'        => 'Truffle Mushroom Pasta',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCvA-dtCyQg7ZEtRx_eVIoSaSMtQdbXSjRdCyhKxEazV1mFZ2PDPQmvryE4u9dNMTKpwH11sWSCb6r9knKSECmQQHH_ctImEcLKmqaQXC3v3C4-PdSdUKZq-YUId54LxzTQw423xptUTBGptBo5X0wmhLef0Qm7fmAKFv81qytYRjhmANSITiVhj4L_ct-2x7vC8mH3e3Se4mei__nBolABN_UbR3k3X9COEjyMhWXe1ty1_eMNCDbi',
                'image_alt'   => 'Fresh tagliatelle with wild mushrooms and creamy truffle sauce.',
                'rating'      => 4.8,
                'time'        => '20m',
                'description' => 'Fresh tagliatelle, wild mushrooms, creamy truffle sauce.',
                'price'       => 22.00,
                'category'    => 'makanan',
                'popular'     => true,
            ],
            [
                'name'        => 'Ribeye Steak',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBPmN6PMfZ4VPKL8GlzzH4-xfjWMtZOl4wyE9PCw6kipKnGzl0iiN8dFEXHP-nEcO-mrgIAB6zCfj4Ay9aWtOH4b9clFDuFz9c7F906Q0gK71MtbdqvhTDZcCJgqB5WFu1r7tb9EwFh7nBHNRGyAx_zasSz2Rcuxu6pph8FJpn3ksJR_Qt4f-e6OQ8BnZu6r7YBmsbvlJCnAJmFuetkVNgXVS80X2g9DlD9w1V2SMXhLnKn7hOqVQSI',
                'image_alt'   => '12oz prime ribeye steak cooked medium-rare with garlic herb butter.',
                'rating'      => 4.9,
                'time'        => '30m',
                'description' => '12oz prime ribeye, medium-rare, garlic herb butter.',
                'price'       => 45.00,
                'category'    => 'makanan',
                'popular'     => true,
            ],
            [
                'name'        => 'Korean Fried Chicken',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDOmG7esf_Wd09ds4rg2jG9StlGgeRCNy9ogXYmT1cF6_2RBW4eki3qf3-_XhROy6FbnedZt4MUjgTJucLv0A3rsvcLIrVXFT-OU55whybEk_OC3zao26GgESHzAf8a5NZSYQoamZ0CT0gdufFx6wNQqJsR018qXAgR5jLN_0T8c_O59JgL-1nAkcjQGWBhYGWceF-EOAE3hd5uK8EesjkDZSdKwE7PPSxp1f76AQBgUqGsu6oty6_m',
                'image_alt'   => 'Double-fried crispy Korean fried chicken wings with sweet and spicy glaze.',
                'rating'      => 4.7,
                'time'        => '25m',
                'description' => 'Double-fried crispy wings tossed in sweet and spicy glaze.',
                'price'       => 16.00,
                'category'    => 'makanan',
                'popular'     => false,
            ],
            [
                'name'        => 'Teriyaki Salmon Bowl',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBLYxO3NgS2p0gfNKgT9QscVAqK_7GszcesT4lIUZHAA7PzSdUpMmFGSiogwSieUfKg7cHtuAN9xaenQRDGDMDqRNxFofvgo69w8lGRekpBmyGj6uFT6UCSZ-0qAm-rDygwE2sPJvVZKUQXZZl6y5uuctFQGdfeAHBiHIbjIVcBZvuwu_hk21IA2Sh42zp3T7tPJFIulTFV2R2ijI-Me6FfzBfiHRcsjJWXHqk0YXwbiq98YOVq-G-F',
                'image_alt'   => 'Grilled salmon with teriyaki glaze, jasmine rice, and avocado in a bowl.',
                'rating'      => 4.6,
                'time'        => '20m',
                'description' => 'Grilled salmon with teriyaki glaze, jasmine rice, avocado.',
                'price'       => 19.00,
                'category'    => 'makanan',
                'popular'     => false,
            ],
            [
                'name'        => 'Chocolate Lava Cake',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNYWq7WsHD9aBGh6xv3RSDda3ZFsSs_k6RqAaFhNUjVkseoy5G2CFX76p7haJx15ecPELn-4rBBzCNwh2qx0nnBrxDgd7rPpFp5Im7SBlH1W-J6F_nj_gRGtHvF1J-TbWtQlQWDrqDSfVsKlGlUsVGmzU0l3lps5k2eOcaUYGJv0TCD0uQAYZ692pmYzJ-e4rS41oBHZE7Y1TWVB1RZNxscdLOfhc6DMfZvVuPNREjakG6xN7uT97J',
                'image_alt'   => 'Warm chocolate lava cake with a molten center served with vanilla bean ice cream.',
                'rating'      => 4.9,
                'time'        => '15m',
                'description' => 'Warm chocolate cake with a molten center, served with vanilla bean ice cream.',
                'price'       => 9.00,
                'category'    => 'makanan',
                'popular'     => true,
            ],
            [
                'name'        => 'Matcha Latte',
                'image'       => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBhPBgiCtmw5tfy5KTfdA3guckM-yQ_qWzb9Rwmxb5i89QeusZD3gbImEz8LeuNyoa8rMOusptE6e1IKmIaB3dJZtPIToapCpwpMM8er7UU9Jys-2psjo5zLiwEcsGgGNfYgM1cxJT3iVFjRkK1UbXLzPka4jiGYdm8fX1G6JBntwkUOlzQfNXdXNSc2_3LGT-YEWYb_JChOI5I1YyU8uTlKuRzcZRygxVRiLfGFI-lRfY53Pra6xRd',
                'image_alt'   => 'Premium matcha green tea latte, steamed milk, lightly sweetened.',
                'rating'      => 4.7,
                'time'        => '5m',
                'description' => 'Premium matcha green tea, steamed milk, lightly sweetened.',
                'price'       => 5.00,
                'category'    => 'minuman',
                'popular'     => false,
            ],
        ];

        foreach ($items as $item) {
            $item['category_id'] = $categoryMap[$item['category']] ?? null;
            $item['status'] = 'available';
            MenuItem::create($item);
        }
    }
}
