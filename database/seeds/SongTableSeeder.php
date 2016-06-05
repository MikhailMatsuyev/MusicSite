<?php

use Illuminate\Database\Seeder;

class SongTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('songs')->delete();
        //$faker = Faker::create();
        $songs = [];
        
        for ($i = 1; $i < 100; $i++) {
            DB::table('songs')->insert([ //,
                'id'=> $i,
                'name' => 'Song #'.$i,
                'album_id' => rand(1, 11),
                'artist_id' => rand(1, 6),
                'created_at'=>new DateTime, 
                'updated_at'=>new DateTime,
            ]);
        }
        
        
        
//        foreach (range(1,100) as $index)
//        {
//            $songs[]=[
//                'id' => rand(10, 16),
//                'email' => $faker->email,
//                'phone' => $faker->phoneNumber,
//                'adress' => "{$faker->streetName} {$faker->postcode} {$faker->city}",
//                'created_at' => new DateTime,
//                'updated_at' => new DateTime,
//                'group_id' => rand(1, 3)        
//            ];
//        }
//
//        DB::table('contacts')->insert($contacts);
    }
}
