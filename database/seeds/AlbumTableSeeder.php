<?php

use Illuminate\Database\Seeder;

class AlbumTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('albums')->delete();
        
        for ($i = 1; $i < 12; $i++) {
            DB::table('albums')->insert([ //,
                'id'=> $i,
                'name' => 'Album'.$i,
                'year' => '20'.rand(10, 16),
                'artist_id' => rand(1, 6),
                'created_at'=>new DateTime, 
                'updated_at'=>new DateTime,
            ]);
        }
        
        
//        $artists=[
//            ['id'=>1, 'name'=>'Album 1', 'year' => rand(1, 7), 'artist_id' => rand(1, 7), 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//            ['id'=>2, 'name'=>'Album 2', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//            ['id'=>3, 'name'=>'Album 3', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//            ['id'=>4, 'name'=>'Album 4', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//            ['id'=>5, 'name'=>'Album 5', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//            ['id'=>6, 'name'=>'Album 6', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//            ['id'=>7, 'name'=>'Album 7', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
//        ];
//        DB::table('artists')->insert($artists);
    }
}
