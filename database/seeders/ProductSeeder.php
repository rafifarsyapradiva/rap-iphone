<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cat16 = Category::where('slug', 'iphone-16-series')->first();
        $cat15 = Category::where('slug', 'iphone-15-series')->first();
        $cat14 = Category::where('slug', 'iphone-14-series')->first();
        $cat13 = Category::where('slug', 'iphone-13-series')->first();
        $catSecond = Category::where('slug', 'pre-owned-second-berkualitas')->first();

        // 1. iPhone 16 Pro Max (BNIB Resmi iBox)
        $p1 = Product::updateOrCreate(
            ['slug' => 'iphone-16-pro-max'],
            [
                'category_id' => $cat16->id,
                'name' => 'iPhone 16 Pro Max',
                'series' => 'iPhone 16 Series',
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'base_price' => 24999000,
                'description' => "iPhone 16 Pro Max. Ditenagai chip A18 Pro terobosan dengan grafis tingkat lanjut untuk gaming kelas konsol. Dilengkapi Camera Control inovatif, layar Super Retina XDR 6,9 inci dengan ProMotion, dan rangka titanium kelas dirgantara yang sangat kuat dan ringan. Garansi Resmi Apple Indonesia (TAM / iBox / Digimap).",
                'is_active' => true,
            ]
        );

        $colorsP1 = [
            ['name' => 'Desert Titanium', 'hex' => '#C5B49F'],
            ['name' => 'Natural Titanium', 'hex' => '#9A958E'],
            ['name' => 'White Titanium', 'hex' => '#F2F1EC'],
            ['name' => 'Black Titanium', 'hex' => '#2D2F33'],
        ];

        $storagesP1 = [
            ['cap' => '256GB', 'cost' => 23200000, 'sell' => 24999000],
            ['cap' => '512GB', 'cost' => 25800000, 'sell' => 27999000],
            ['cap' => '1TB',   'cost' => 30000000, 'sell' => 32499000],
        ];

        foreach ($colorsP1 as $color) {
            foreach ($storagesP1 as $storage) {
                $sku = 'IP16PM-' . strtoupper(Str::slug($color['name'])) . '-' . $storage['cap'];
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'product_id' => $p1->id,
                        'storage_capacity' => $storage['cap'],
                        'color_name' => $color['name'],
                        'color_hex' => $color['hex'],
                        'cost_price' => $storage['cost'],
                        'selling_price' => $storage['sell'],
                        'stock' => 15,
                        'battery_health' => null,
                        'imei' => null,
                    ]
                );

                ProductImage::updateOrCreate(
                    ['product_id' => $p1->id, 'variant_id' => $variant->id],
                    [
                        'image_path' => 'images/products/iphone-16-pro-max.png',
                        'is_primary' => ($color['name'] === 'Desert Titanium' && $storage['cap'] === '256GB'),
                        'order_index' => 1,
                    ]
                );
            }
        }

        // 2. iPhone 16 Pro (BNIB Resmi iBox)
        $p2 = Product::updateOrCreate(
            ['slug' => 'iphone-16-pro'],
            [
                'category_id' => $cat16->id,
                'name' => 'iPhone 16 Pro',
                'series' => 'iPhone 16 Series',
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'base_price' => 20999000,
                'description' => "iPhone 16 Pro. Memadukan kekuatan chip A18 Pro, Camera Control intuitif, kamera Fusion 48 MP generasi berikutnya, dan layar Super Retina XDR 6,3 inci dengan ProMotion. Rangka titanium kuat dengan batas layar tertipis dari lini iPhone mana pun.",
                'is_active' => true,
            ]
        );

        $storagesP2 = [
            ['cap' => '128GB', 'cost' => 19300000, 'sell' => 20999000],
            ['cap' => '256GB', 'cost' => 21800000, 'sell' => 23499000],
            ['cap' => '512GB', 'cost' => 24500000, 'sell' => 26499000],
        ];

        foreach ($colorsP1 as $color) {
            foreach ($storagesP2 as $storage) {
                $sku = 'IP16P-' . strtoupper(Str::slug($color['name'])) . '-' . $storage['cap'];
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'product_id' => $p2->id,
                        'storage_capacity' => $storage['cap'],
                        'color_name' => $color['name'],
                        'color_hex' => $color['hex'],
                        'cost_price' => $storage['cost'],
                        'selling_price' => $storage['sell'],
                        'stock' => 12,
                        'battery_health' => null,
                        'imei' => null,
                    ]
                );

                ProductImage::updateOrCreate(
                    ['product_id' => $p2->id, 'variant_id' => $variant->id],
                    [
                        'image_path' => 'images/products/iphone-16-pro.png',
                        'is_primary' => ($color['name'] === 'Natural Titanium' && $storage['cap'] === '128GB'),
                        'order_index' => 1,
                    ]
                );
            }
        }

        // 3. iPhone 16 (BNIB Resmi iBox)
        $p3 = Product::updateOrCreate(
            ['slug' => 'iphone-16'],
            [
                'category_id' => $cat16->id,
                'name' => 'iPhone 16',
                'series' => 'iPhone 16 Series',
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'base_price' => 16499000,
                'description' => "iPhone 16. Sambut Camera Control, tombol Tindakan, kamera Fusion 48 MP, serta chip A18 baru bertenaga tinggi dengan efisiensi baterai luar biasa. Pilihan warna segar dengan ketahanan kaca belakang Color-infused glass.",
                'is_active' => true,
            ]
        );

        $colorsP3 = [
            ['name' => 'Ultramarine', 'hex' => '#496495'],
            ['name' => 'Teal', 'hex' => '#799896'],
            ['name' => 'Pink', 'hex' => '#E8C4C6'],
            ['name' => 'White', 'hex' => '#F2F1EC'],
            ['name' => 'Black', 'hex' => '#2D2F33'],
        ];

        $storagesP3 = [
            ['cap' => '128GB', 'cost' => 15100000, 'sell' => 16499000],
            ['cap' => '256GB', 'cost' => 17400000, 'sell' => 18999000],
        ];

        foreach ($colorsP3 as $color) {
            foreach ($storagesP3 as $storage) {
                $sku = 'IP16-' . strtoupper(Str::slug($color['name'])) . '-' . $storage['cap'];
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'product_id' => $p3->id,
                        'storage_capacity' => $storage['cap'],
                        'color_name' => $color['name'],
                        'color_hex' => $color['hex'],
                        'cost_price' => $storage['cost'],
                        'selling_price' => $storage['sell'],
                        'stock' => 20,
                        'battery_health' => null,
                        'imei' => null,
                    ]
                );

                ProductImage::updateOrCreate(
                    ['product_id' => $p3->id, 'variant_id' => $variant->id],
                    [
                        'image_path' => 'images/products/iphone-16.png',
                        'is_primary' => ($color['name'] === 'Ultramarine' && $storage['cap'] === '128GB'),
                        'order_index' => 1,
                    ]
                );
            }
        }

        // 4. iPhone 15 Pro (Second Like New - Grade A+)
        $p4 = Product::updateOrCreate(
            ['slug' => 'iphone-15-pro-second-like-new'],
            [
                'category_id' => $catSecond->id,
                'name' => 'iPhone 15 Pro (Second Like New)',
                'series' => 'iPhone 15 Series',
                'condition' => 'second',
                'grade' => 'Grade A+',
                'warranty_type' => 'toko',
                'base_price' => 15800000,
                'description' => "Unit second pilihan dengan kondisi fisik 98% mulus tanpa dent. Battery Health tinggi (>95%), fungsi 3uTools hijau normal semua, Face ID dan TrueTone aktif. IMEI terdaftar resmi Bea Cukai / Kemenperin (Anti Blokir). Bergaransi toko 30 hari ganti unit.",
                'is_active' => true,
            ]
        );

        $variantsP4 = [
            [
                'color_name' => 'Natural Titanium',
                'color_hex' => '#9A958E',
                'storage' => '128GB',
                'cost' => 14100000,
                'sell' => 15800000,
                'stock' => 4,
                'bh' => 96,
                'imei' => '358792110293812',
                'sku' => 'SEC-IP15P-NT-128GB',
            ],
            [
                'color_name' => 'Blue Titanium',
                'color_hex' => '#3B4454',
                'storage' => '256GB',
                'cost' => 15300000,
                'sell' => 17200000,
                'stock' => 3,
                'bh' => 98,
                'imei' => '358792110482914',
                'sku' => 'SEC-IP15P-BT-256GB',
            ],
        ];

        foreach ($variantsP4 as $varData) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $varData['sku']],
                [
                    'product_id' => $p4->id,
                    'storage_capacity' => $varData['storage'],
                    'color_name' => $varData['color_name'],
                    'color_hex' => $varData['color_hex'],
                    'cost_price' => $varData['cost'],
                    'selling_price' => $varData['sell'],
                    'stock' => $varData['stock'],
                    'battery_health' => $varData['bh'],
                    'imei' => $varData['imei'],
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $p4->id, 'variant_id' => $variant->id],
                [
                    'image_path' => 'images/products/iphone-15-pro.png',
                    'is_primary' => ($varData['color_name'] === 'Natural Titanium'),
                    'order_index' => 1,
                ]
            );
        }

        // 5. iPhone 14 (BNIB Resmi iBox)
        $p5 = Product::updateOrCreate(
            ['slug' => 'iphone-14'],
            [
                'category_id' => $cat14->id,
                'name' => 'iPhone 14',
                'series' => 'iPhone 14 Series',
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'base_price' => 12249000,
                'description' => "iPhone 14. Sistem kamera ganda paling mengesankan di iPhone. Mengambil foto memukau dalam pencahayaan rendah maupun terang. Dilengkapi Deteksi Tabrakan untuk keselamatan Anda. Garansi Resmi iBox 1 Tahun.",
                'is_active' => true,
            ]
        );

        $colorsP5 = [
            ['name' => 'Midnight', 'hex' => '#2D2F33'],
            ['name' => 'Starlight', 'hex' => '#F2F1EC'],
            ['name' => 'Blue', 'hex' => '#A7C1D9'],
        ];

        foreach ($colorsP5 as $color) {
            $sku = 'IP14-' . strtoupper(Str::slug($color['name'])) . '-128GB';
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $sku],
                [
                    'product_id' => $p5->id,
                    'storage_capacity' => '128GB',
                    'color_name' => $color['name'],
                    'color_hex' => $color['hex'],
                    'cost_price' => 11200000,
                    'selling_price' => 12249000,
                    'stock' => 10,
                    'battery_health' => null,
                    'imei' => null,
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $p5->id, 'variant_id' => $variant->id],
                [
                    'image_path' => 'images/products/iphone-14.png',
                    'is_primary' => ($color['name'] === 'Midnight'),
                    'order_index' => 1,
                ]
            );
        }

        // 6. iPhone 13 (Second Like New - Grade A)
        $p6 = Product::updateOrCreate(
            ['slug' => 'iphone-13-second-like-new'],
            [
                'category_id' => $catSecond->id,
                'name' => 'iPhone 13 (Second Like New)',
                'series' => 'iPhone 13 Series',
                'condition' => 'second',
                'grade' => 'Grade A',
                'warranty_type' => 'toko',
                'base_price' => 8900000,
                'description' => "Pilihan terfavorit dengan nilai terbaik. Chip A15 Bionic cepat, baterai tahan lama seharian, dan kamera ganda diagonal dengan Cinematic Mode. Unit mulus 95%, Battery Health 89%-91%, All Operator aman tanpa blokir.",
                'is_active' => true,
            ]
        );

        $variantsP6 = [
            [
                'color_name' => 'Midnight',
                'color_hex' => '#2D2F33',
                'storage' => '128GB',
                'cost' => 7700000,
                'sell' => 8900000,
                'stock' => 5,
                'bh' => 89,
                'imei' => '358792098231291',
                'sku' => 'SEC-IP13-MD-128GB',
            ],
            [
                'color_name' => 'Pink',
                'color_hex' => '#FAD7BD',
                'storage' => '128GB',
                'cost' => 7800000,
                'sell' => 9100000,
                'stock' => 3,
                'bh' => 91,
                'imei' => '358792098231455',
                'sku' => 'SEC-IP13-PK-128GB',
            ],
        ];

        foreach ($variantsP6 as $varData) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $varData['sku']],
                [
                    'product_id' => $p6->id,
                    'storage_capacity' => $varData['storage'],
                    'color_name' => $varData['color_name'],
                    'color_hex' => $varData['color_hex'],
                    'cost_price' => $varData['cost'],
                    'selling_price' => $varData['sell'],
                    'stock' => $varData['stock'],
                    'battery_health' => $varData['bh'],
                    'imei' => $varData['imei'],
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $p6->id, 'variant_id' => $variant->id],
                [
                    'image_path' => 'images/products/iphone-13.png',
                    'is_primary' => ($varData['color_name'] === 'Midnight'),
                    'order_index' => 1,
                ]
            );
        }
    }
}
