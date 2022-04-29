<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CollectionType;

class CollectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = array(
            array(
                'name' => 'Books',
                'require_thumbnail' => 1
            ),
            array(
                'name' => 'Music',
                'require_thumbnail' => 1
            ),
            array(
                'name' => 'Files',
                'require_thumbnail' => 1
            ),
            array(
                'name' => 'Images',
                'require_thumbnail' => 0
            )
        );
        foreach($datas as $data){
            CollectionType::create([
                'name' => $data['name'],
                'require_thumbnail' => $data['require_thumbnail']
            ]);
        }
    }
}
