<?php

namespace App\Services;

class TradeInCalculatorService
{
    /**
     * Get the complete catalog of supported iPhone models for Trade-In
     * along with their base maximum trade-in valuation per storage capacity.
     *
     * @return array<string, array{series: string, max_price: int, accent: string, capacities: array<string, int>}>
     */
    public function getModelCatalog(): array
    {
        return [
            // iPhone 16 Series
            'iPhone 16 Pro Max' => [
                'series' => 'iPhone 16 Series',
                'max_price' => 19500000,
                'accent' => '#C5B49F',
                'capacities' => [
                    '256GB' => 16500000,
                    '512GB' => 18000000,
                    '1TB' => 19500000,
                ],
            ],
            'iPhone 16 Pro' => [
                'series' => 'iPhone 16 Series',
                'max_price' => 17500000,
                'accent' => '#9A958E',
                'capacities' => [
                    '128GB' => 14000000,
                    '256GB' => 15200000,
                    '512GB' => 16500000,
                    '1TB' => 17500000,
                ],
            ],
            'iPhone 16' => [
                'series' => 'iPhone 16 Series',
                'max_price' => 13800000,
                'accent' => '#496495',
                'capacities' => [
                    '128GB' => 11500000,
                    '256GB' => 12600000,
                    '512GB' => 13800000,
                ],
            ],

            // iPhone 15 Series
            'iPhone 15 Pro Max' => [
                'series' => 'iPhone 15 Series',
                'max_price' => 17200000,
                'accent' => '#9A958E',
                'capacities' => [
                    '256GB' => 14500000,
                    '512GB' => 15800000,
                    '1TB' => 17200000,
                ],
            ],
            'iPhone 15 Pro' => [
                'series' => 'iPhone 15 Series',
                'max_price' => 15200000,
                'accent' => '#2D2F33',
                'capacities' => [
                    '128GB' => 12200000,
                    '256GB' => 13400000,
                    '512GB' => 14300000,
                    '1TB' => 15200000,
                ],
            ],
            'iPhone 15 Plus' => [
                'series' => 'iPhone 15 Series',
                'max_price' => 12500000,
                'accent' => '#799896',
                'capacities' => [
                    '128GB' => 10200000,
                    '256GB' => 11300000,
                    '512GB' => 12500000,
                ],
            ],
            'iPhone 15' => [
                'series' => 'iPhone 15 Series',
                'max_price' => 11200000,
                'accent' => '#E8C4C6',
                'capacities' => [
                    '128GB' => 9200000,
                    '256GB' => 10200000,
                    '512GB' => 11200000,
                ],
            ],

            // iPhone 14 Series
            'iPhone 14 Pro Max' => [
                'series' => 'iPhone 14 Series',
                'max_price' => 13800000,
                'accent' => '#AF52DE',
                'capacities' => [
                    '128GB' => 11200000,
                    '256GB' => 12200000,
                    '512GB' => 13000000,
                    '1TB' => 13800000,
                ],
            ],
            'iPhone 14 Pro' => [
                'series' => 'iPhone 14 Series',
                'max_price' => 12800000,
                'accent' => '#AF52DE',
                'capacities' => [
                    '128GB' => 10000000,
                    '256GB' => 11000000,
                    '512GB' => 11900000,
                    '1TB' => 12800000,
                ],
            ],
            'iPhone 14' => [
                'series' => 'iPhone 14 Series',
                'max_price' => 8800000,
                'accent' => '#5AC8FA',
                'capacities' => [
                    '128GB' => 7200000,
                    '256GB' => 8000000,
                    '512GB' => 8800000,
                ],
            ],

            // iPhone 13 Series
            'iPhone 13 Pro Max' => [
                'series' => 'iPhone 13 Series',
                'max_price' => 10800000,
                'accent' => '#5AC8FA',
                'capacities' => [
                    '128GB' => 8800000,
                    '256GB' => 9600000,
                    '512GB' => 10200000,
                    '1TB' => 10800000,
                ],
            ],
            'iPhone 13 Pro' => [
                'series' => 'iPhone 13 Series',
                'max_price' => 9500000,
                'accent' => '#34C759',
                'capacities' => [
                    '128GB' => 7800000,
                    '256GB' => 8500000,
                    '512GB' => 9000000,
                    '1TB' => 9500000,
                ],
            ],
            'iPhone 13' => [
                'series' => 'iPhone 13 Series',
                'max_price' => 7500000,
                'accent' => '#E5DCC5',
                'capacities' => [
                    '128GB' => 6200000,
                    '256GB' => 6900000,
                    '512GB' => 7500000,
                ],
            ],

            // iPhone 12 & 11 Series
            'iPhone 12 Pro Max' => [
                'series' => 'iPhone 12 & 11 Series',
                'max_price' => 7800000,
                'accent' => '#215E7C',
                'capacities' => [
                    '128GB' => 6400000,
                    '256GB' => 7100000,
                    '512GB' => 7800000,
                ],
            ],
            'iPhone 12' => [
                'series' => 'iPhone 12 & 11 Series',
                'max_price' => 5400000,
                'accent' => '#191F26',
                'capacities' => [
                    '64GB' => 4200000,
                    '128GB' => 4800000,
                    '256GB' => 5400000,
                ],
            ],
            'iPhone 11' => [
                'series' => 'iPhone 12 & 11 Series',
                'max_price' => 4200000,
                'accent' => '#F2F1EC',
                'capacities' => [
                    '64GB' => 3200000,
                    '128GB' => 3700000,
                    '256GB' => 4200000,
                ],
            ],
        ];
    }

    /**
     * Get physical condition options with deduction percentage.
     *
     * @return array<string, array{label: string, description: string, deduction_rate: float, badge: string}>
     */
    public function getPhysicalConditions(): array
    {
        return [
            'Mulus (Grade A+)' => [
                'label' => 'Mulus Sempurna (Grade A+)',
                'description' => 'Fisik 99% seperti baru, tanpa goresan atau lecet sama sekali.',
                'deduction_rate' => 0.00,
                'badge' => 'Nilai Maksimal (0%)',
            ],
            'Mulus (Grade A)' => [
                'label' => 'Mulus Pemakaian Wajar (Grade A)',
                'description' => 'Fisik 95-98% terawat, goresan sangat halus hampir tak terlihat.',
                'deduction_rate' => 0.05,
                'badge' => 'Potongan -5%',
            ],
            'Lecet Pemakaian (Grade B)' => [
                'label' => 'Lecet Pemakaian (Grade B)',
                'description' => 'Terdapat lecet halus atau bekas casing pada bezel/bodi belakang.',
                'deduction_rate' => 0.15,
                'badge' => 'Potongan -15%',
            ],
            'Dent / Penyok (Grade C)' => [
                'label' => 'Dent / Penyok Sudut (Grade C)',
                'description' => 'Terdapat penyok di sudut bezel atau retak halus pada backglass.',
                'deduction_rate' => 0.28,
                'badge' => 'Potongan -28%',
            ],
        ];
    }

    /**
     * Get screen and functional condition options with deduction percentage.
     *
     * @return array<string, array{label: string, description: string, deduction_rate: float, badge: string}>
     */
    public function getScreenConditions(): array
    {
        return [
            'Normal' => [
                'label' => 'Normal 100% (TrueTone & Face ID Aktif)',
                'description' => 'Layar LCD original bening, sentuhan responsif, TrueTone & Face ID berfungsi normal.',
                'deduction_rate' => 0.00,
                'badge' => 'Tanpa Potongan (0%)',
            ],
            'Ada Jamur/TrueTone Off' => [
                'label' => 'Ada Whitespot / Jamur / TrueTone Off',
                'description' => 'Layar terdapat whitespot tipis, baret terlihat, atau fitur TrueTone tidak aktif.',
                'deduction_rate' => 0.18,
                'badge' => 'Potongan -18%',
            ],
            'Retak' => [
                'label' => 'Kaca Retak / Shadow / Face ID Off',
                'description' => 'Kaca layar retak, terdapat garis/dead pixel, atau sensor Face ID bermasalah.',
                'deduction_rate' => 0.35,
                'badge' => 'Potongan -35%',
            ],
        ];
    }

    /**
     * Get accessories completeness options with flat deduction amount in IDR.
     *
     * @return array<string, array{label: string, description: string, deduction_amount: int, badge: string}>
     */
    public function getAccessoriesOptions(): array
    {
        return [
            'Fullset Box Original' => [
                'label' => 'Fullset Box & Kabel Original',
                'description' => 'Kotak dus asli sesuai IMEI, buku manual, dan kabel data original lengkap.',
                'deduction_amount' => 0,
                'badge' => '+ Nilai Penuh',
            ],
            'Box + Kabel OEM' => [
                'label' => 'Box Original Tanpa Kabel / OEM',
                'description' => 'Kotak dus ada namun aksesoris kabel sudah tidak ada atau non-original.',
                'deduction_amount' => 250000,
                'badge' => '- Rp 250.000',
            ],
            'Unit Only' => [
                'label' => 'Unit iPhone Saja (Batangan)',
                'description' => 'Hanya unit iPhone tanpa kotak dus maupun kabel pengisi daya.',
                'deduction_amount' => 500000,
                'badge' => '- Rp 500.000',
            ],
        ];
    }

    /**
     * Supported Indonesian Banks & Digital Wallets for Payout.
     *
     * @return array<int, string>
     */
    public function getSupportedBanks(): array
    {
        return [
            'BCA (Bank Central Asia)',
            'Bank Mandiri',
            'BNI (Bank Negara Indonesia)',
            'BRI (Bank Rakyat Indonesia)',
            'BSI (Bank Syariah Indonesia)',
            'CIMB Niaga',
            'SeaBank Indonesia',
            'Bank Jago',
            'Permata Bank',
        ];
    }

    /**
     * Calculate automatic estimated trade-in value with full breakdown.
     *
     * @param  array{
     *     phone_model: string,
     *     storage_capacity: string,
     *     battery_health: int|numeric-string,
     *     physical_condition: string,
     *     screen_condition: string,
     *     accessories_status: string
     * }  $params
     * @return array{
     *     base_price: float,
     *     physical_deduction: float,
     *     screen_deduction: float,
     *     battery_deduction: float,
     *     accessories_deduction: float,
     *     total_deduction: float,
     *     estimated_price: float
     * }
     */
    public function calculateEstimate(array $params): array
    {
        $catalog = $this->getModelCatalog();
        $modelName = $params['phone_model'] ?? '';
        $capacity = $params['storage_capacity'] ?? '';
        $batteryHealth = max(50, min(100, (int) ($params['battery_health'] ?? 85)));
        $physicalCondition = $params['physical_condition'] ?? 'Mulus (Grade A)';
        $screenCondition = $params['screen_condition'] ?? 'Normal';
        $accessoriesStatus = $params['accessories_status'] ?? 'Fullset Box Original';

        // 1. Base Price Lookup
        $basePrice = 5000000;
        if (isset($catalog[$modelName])) {
            $capacities = $catalog[$modelName]['capacities'];
            $basePrice = $capacities[$capacity] ?? reset($capacities);
        }

        // 2. Physical Condition Deduction Rate
        $physicalOptions = $this->getPhysicalConditions();
        $physicalRate = $physicalOptions[$physicalCondition]['deduction_rate'] ?? 0.05;
        $physicalDeduction = round($basePrice * $physicalRate, -3);

        // 3. Screen Condition Deduction Rate
        $screenOptions = $this->getScreenConditions();
        $screenRate = $screenOptions[$screenCondition]['deduction_rate'] ?? 0.00;
        $screenDeduction = round($basePrice * $screenRate, -3);

        // 4. Battery Health Deduction Rate
        if ($batteryHealth >= 90) {
            $batteryRate = 0.00;
        } elseif ($batteryHealth >= 85) {
            $batteryRate = 0.04;
        } elseif ($batteryHealth >= 80) {
            $batteryRate = 0.08;
        } else {
            $batteryRate = 0.15;
        }
        $batteryDeduction = round($basePrice * $batteryRate, -3);

        // 5. Accessories Flat Deduction
        $accessoriesOptions = $this->getAccessoriesOptions();
        $accessoriesDeduction = (float) ($accessoriesOptions[$accessoriesStatus]['deduction_amount'] ?? 0);

        // Total Deduction & Final Safe Floor (minimum 25% of base price or Rp 1.000.000)
        $totalDeduction = $physicalDeduction + $screenDeduction + $batteryDeduction + $accessoriesDeduction;
        $minimumFloor = max(1000000, round($basePrice * 0.25, -3));
        $estimatedPrice = max($minimumFloor, $basePrice - $totalDeduction);

        return [
            'base_price' => (float) $basePrice,
            'physical_deduction' => (float) $physicalDeduction,
            'screen_deduction' => (float) $screenDeduction,
            'battery_deduction' => (float) $batteryDeduction,
            'accessories_deduction' => (float) $accessoriesDeduction,
            'total_deduction' => (float) $totalDeduction,
            'estimated_price' => (float) $estimatedPrice,
        ];
    }
}
