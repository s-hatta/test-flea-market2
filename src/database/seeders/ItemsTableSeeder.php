<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $this->copyImages();
        $params = [
            [
                'name' => '腕時計',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 15000,
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => '/item_001.jpg',
            ],
            [
                'name' => 'HDD',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 5000,
                'detail' => '高速で信頼性の高いハードディスク',
                'img_url' => '/item_002.jpg',
            ],
            [
                'name' => '玉ねぎ3束',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '3',
                'price' => 300,
                'detail' => '新鮮な玉ねぎ3束のセット',
                'img_url' => '/item_003.jpg',
            ],
            [
                'name' => '革靴',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '4',
                'price' => 4000,
                'detail' => 'クラシックなデザインの革靴',
                'img_url' => '/item_004.jpg',
            ],
            [
                'name' => 'ノートPC',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 45000,
                'detail' => '高性能なノートパソコン',
                'img_url' => '/item_005.jpg',
            ],
            [
                'name' => 'マイク',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 8000,
                'detail' => '高音質のレコーディング用マイク',
                'img_url' => '/item_006.jpg',
            ],
            [
                'name' => 'ショルダーバッグ',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '3',
                'price' => 3500,
                'detail' => 'おしゃれなショルダーバッグ',
                'img_url' => '/item_007.jpg',
            ],
            [
                'name' => 'タンブラー',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '4',
                'price' => 500,
                'detail' => '使いやすいタンブラー',
                'img_url' => '/item_008.jpg',
            ],
            [
                'name' => 'コーヒーミル',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 4000,
                'detail' => '手動のコーヒーミル',
                'img_url' => '/item_009.jpg',
            ],
            [
                'name' => 'メイクセット',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 2500,
                'detail' => '便利なメイクアップセット',
                'img_url' => '/item_010.jpg',
            ],
        ];
        foreach ($params as $param) {
            Item::factory()->create($param);
        }
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
