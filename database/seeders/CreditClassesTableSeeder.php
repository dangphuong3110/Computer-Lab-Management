<?php

namespace Database\Seeders;

use App\Models\CreditClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class = [
            [
                'name' => 'Khai phá dữ liệu-6-23 (63HTTT1 (- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '81wI9E',
                'lecturer_id' => 1,
            ],
            [
                'name' => 'Khai phá dữ liệu-6-23 (63HTTT1 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '1Zk3Ve',
                'lecturer_id' => 1,
            ],
            [
                'name' => 'Khai phá dữ liệu-6-23 (63HTTT2 (- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'CFjYCM',
                'lecturer_id' => 1,
            ],
            [
                'name' => 'Khai phá dữ liệu-6-23 (63HTTT2 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'cwhAwf',
                'lecturer_id' => 1,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65CNTT (TH1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'gdOgxE',
                'lecturer_id' => 2,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65CNTT (TH2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'LTecmu',
                'lecturer_id' => 2,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65HTTT (TH1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '2LzItK',
                'lecturer_id' => 2,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65HTTT (TH2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '3hSHqw',
                'lecturer_id' => 9,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65KTPM ( TH1 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'UC6Tcf',
                'lecturer_id' => 3,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65KTPM ( TH2 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'KNbUFG',
                'lecturer_id' => 3,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65TTNT ( TH1 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'iULeZk',
                'lecturer_id' => 3,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65TTNT ( TH2 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'kj42U6',
                'lecturer_id' => 3,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65ANM ( TH1 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'GTiK5M',
                'lecturer_id' => 3,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65ANM ( TH2 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'CQQJtO',
                'lecturer_id' => 3,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65KTRB ( TH1 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'ctPpGP',
                'lecturer_id' => 4,
            ],
            [
                'name' => 'Lập trình nâng cao-2-23 (65KTRB ( TH2 ))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'kc0Bnu',
                'lecturer_id' => 4,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT1 (- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '78l75D',
                'lecturer_id' => 5,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT1 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '3RTaoO',
                'lecturer_id' => 5,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT2 (- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'GIgkNO',
                'lecturer_id' => 5,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT2 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'YeqZsn',
                'lecturer_id' => 5,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT3(- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'DcDSiH',
                'lecturer_id' => 6,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT3 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'k5V4mB',
                'lecturer_id' => 6,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT4(- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'ugvmo7',
                'lecturer_id' => 6,
            ],
            [
                'name' => 'Phát triển ứng dụng cho các thiết bị di động-6-23 (63CNTT4(- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'gO8ICe',
                'lecturer_id' => 6,
            ],
            [
                'name' => 'Truy hồi thông tin và tìm kiếm web-6-23 (63CNTT3 (- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => '8PNBSa',
                'lecturer_id' => 7,
            ],
            [
                'name' => 'Truy hồi thông tin và tìm kiếm web-6-23 (63CNTT3 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'HXyvFW',
                'lecturer_id' => 7,
            ],
            [
                'name' => 'Truy hồi thông tin và tìm kiếm web-6-23 (63CNTT4 (- 1))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'fcqF9e',
                'lecturer_id' => 7,
            ],
            [
                'name' => 'Truy hồi thông tin và tìm kiếm web-6-23 (63CNTT4 (- 2))',
                'start_date' => '2024-06-19',
                'end_date' => '2024-07-31',
                'class_code' => 'i5izjZ',
                'lecturer_id' => 7,
            ],
        ];

        DB::table('classes')->insert($class);
    }
}
