<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Category;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $this->copyImages();
        $item = Item::factory()->createOne([
                'name' => '腕時計',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 15000,
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => '/item_001.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'ファッション')->first());
        $item->categories()->attach(Category::where('content', 'アクセサリ')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'HDD',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 5000,
                'detail' => '高速で信頼性の高いハードディスク',
                'img_url' => '/item_002.jpg',
        ]);
        $item->categories()->attach(Category::where('content', '家電')->first());
        
        $item = Item::factory()->createOne([
                'name' => '玉ねぎ3束',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '3',
                'price' => 300,
                'detail' => '新鮮な玉ねぎ3束のセット',
                'img_url' => '/item_003.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'キッチン')->first());
        
        $item = Item::factory()->createOne([
                'name' => '革靴',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '4',
                'price' => 4000,
                'detail' => 'クラシックなデザインの革靴',
                'img_url' => '/item_004.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'ファッション')->first());
        $item->categories()->attach(Category::where('content', 'メンズ')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'ノートPC',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 45000,
                'detail' => '高性能なノートパソコン',
                'img_url' => '/item_005.jpg',
        ]);
        $item->categories()->attach(Category::where('content', '家電')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'マイク',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 8000,
                'detail' => '高音質のレコーディング用マイク',
                'img_url' => '/item_006.jpg',
        ]);
        $item->categories()->attach(Category::where('content', '家電')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'ショルダーバッグ',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '3',
                'price' => 3500,
                'detail' => 'おしゃれなショルダーバッグ',
                'img_url' => '/item_007.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'ファッション')->first());
        $item->categories()->attach(Category::where('content', 'レディース')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'タンブラー',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '4',
                'price' => 500,
                'detail' => '使いやすいタンブラー',
                'img_url' => '/item_008.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'キッチン')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'コーヒーミル',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '1',
                'price' => 4000,
                'detail' => '手動のコーヒーミル',
                'img_url' => '/item_009.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'キッチン')->first());
        
        $item = Item::factory()->createOne([
                'name' => 'メイクセット',
                'owner_id' => 1,
                'brand_name' => 'ノーブランド',
                'condition_id' => '2',
                'price' => 2500,
                'detail' => '便利なメイクアップセット',
                'img_url' => '/item_010.jpg',
        ]);
        $item->categories()->attach(Category::where('content', 'ファッション')->first());
        $item->categories()->attach(Category::where('content', 'レディース')->first());
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
