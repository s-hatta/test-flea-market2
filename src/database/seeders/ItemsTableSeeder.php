<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DateTime;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = new DateTime();
        $this->copyImages();
        $params = [
            [
                'name' => '腕時計',
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 15000,
                'stock' => 1,
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => '/item_001.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'HDD',
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 5000,
                'stock' => 1,
                'detail' => '高速で信頼性の高いハードディスク',
                'img_url' => '/item_002.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '玉ねぎ3束',
                'brand_name' => 'ノーブランド',
                'condition_id' => '3',
                'price' => 300,
                'stock' => 1,
                'detail' => '新鮮な玉ねぎ3束のセット',
                'img_url' => '/item_003.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '革靴',
                'brand_name' => 'ノーブランド',
                'condition_id' => '4',
                'price' => 4000,
                'stock' => 1,
                'detail' => 'クラシックなデザインの革靴',
                'img_url' => '/item_004.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ノートPC',
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 45000,
                'stock' => 1,
                'detail' => '高性能なノートパソコン',
                'img_url' => '/item_005.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'マイク',
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 8000,
                'stock' => 1,
                'detail' => '高音質のレコーディング用マイク',
                'img_url' => '/item_006.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ショルダーバッグ',
                'brand_name' => 'ノーブランド',
                'condition_id' => '3',
                'price' => 3500,
                'stock' => 1,
                'detail' => 'おしゃれなショルダーバッグ',
                'img_url' => '/item_007.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'タンブラー',
                'brand_name' => 'ノーブランド',
                'condition_id' => '4',
                'price' => 500,
                'stock' => 1,
                'detail' => '使いやすいタンブラー',
                'img_url' => '/item_008.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'コーヒーミル',
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 4000,
                'stock' => 1,
                'detail' => '手動のコーヒーミル',
                'img_url' => '/item_009.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'メイクセット',
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 2500,
                'stock' => 1,
                'detail' => '便利なメイクアップセット',
                'img_url' => '/item_010.jpg',
                'user_id' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('items')->insert($params);
    }
    
    private function copyImages()
    {
        $destinationPath = storage_path('app/public/images/items');
        if (!File::exists($destinationPath))
        {
            File::makeDirectory($destinationPath, 777, true);
        }
        
        $sourcePath = public_path('images/items');
        $files = File::files($sourcePath);
        foreach ($files as $file)
        {
            $destinationFile = $destinationPath . '/' . $file->getFilename();
            File::copy($file->getPathname(), $destinationFile);
        }
    }
}
