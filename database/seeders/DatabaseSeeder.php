<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Classes\GlobalVariable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Defining
        
        $positions = array(
            [
                'id' => GlobalVariable::$positions['teacher'],
                'name' => 'Teacher'
            ],
        );

        // Saving

        \App\Models\User::factory()->create([
            'name' => 'Timur',
            'email' => 'islamovtimur29@gmail.com',
            'phone' => '+998 99 123 45 67',
            'birthdate' => '29.12.2003'
        ]);

        foreach ($positions as $position){
            \App\Models\Position::factory()->create($position);
        }
    }
}
