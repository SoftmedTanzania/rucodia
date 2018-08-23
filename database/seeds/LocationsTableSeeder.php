<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed using model factories
        // $users = factory(App\Location::class, 30)->create();
        // $this->command->info('Location table seeded!');

        // First sample location
        DB::table('locations')->insert([
            'uuid' => (string) Str::uuid(),
            'latitude' => -4.859657,
            'longitude' => 29.625055,
            'name' => 'Sebastian Agrovets',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Second sample location
        DB::table('locations')->insert([
            'uuid' => (string) Str::uuid(),
            'latitude' => -4.818000,
            'longitude' => 29.625000,
            'name' => 'Kibirizi Traders',
            'created_at' => date('Y-m-d H:i:s'),
            ]);
        
        // Third sample location
        DB::table('locations')->insert([
            'uuid' => (string) Str::uuid(),
            'latitude' => -3.583117,
            'longitude' => 30.724251,
            'name' => 'Kasuku Store',
            'created_at' => date('Y-m-d H:i:s'),
            ]);

        // Fourth sample location
        DB::table('locations')->insert([
            'uuid' => (string) Str::uuid(),
            'latitude' => -4.825949,
            'longitude' => 29.657873,
            'name' => 'Mkulima Shop',
            'created_at' => date('Y-m-d H:i:s'),
            ]);

        // Fifth sample location
        DB::table('locations')->insert([
            'uuid' => (string) Str::uuid(),
            'latitude' => -4.825467,
            'longitude' => 29.651003,
            'name' => 'Mkulima Shop',
            'created_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
