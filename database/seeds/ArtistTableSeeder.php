<?php

use Illuminate\Database\Seeder;

class ArtistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('artists')->delete();
        $artists=[
            ['id'=>1, 'name'=>'Madonna', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
            ['id'=>2, 'name'=>'ATB', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
            ['id'=>3, 'name'=>'ACDC', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
            ['id'=>4, 'name'=>'Movetown', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
            ['id'=>5, 'name'=>'Pitbull', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
            ['id'=>6, 'name'=>'Eminem', 'created_at'=>new DateTime, 'updated_at'=>new DateTime],
        ];
        DB::table('artists')->insert($artists);
    }
}
