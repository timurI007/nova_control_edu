<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Classes\GlobalVariable;
use Illuminate\Database\Seeder;

class ProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
