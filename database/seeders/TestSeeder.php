<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Classes\GlobalVariable;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Defining
        
        $departments = $this->getDepartments();
        $positions = $this->getPositions();
        $courses = $this->getCourses();

        // Saving

        // \App\Models\User::factory()->count(20)->create();
        foreach ($departments as $department){
            \App\Models\Department::factory()->create($department);
        }
        foreach ($positions as $position){
            \App\Models\Position::factory()->create($position);
        }
        foreach ($courses as $course){
            \App\Models\Course::factory()->create($course);
        }
        \App\Models\Staff::factory()->count(10)->create([
            'position_id' => 1,
            'department_id' => 1
        ]);
    }

    function getDepartments() : array {
        return array(
            [
                'name' => 'Education'
            ],
            [
                'name' => 'Management'
            ],
        );
    }
    
    function getPositions() : array {
        return array(
            [
                'id' => GlobalVariable::$positions['teacher'],
                'name' => 'Teacher'
            ],
            [
                'name' => 'Admin'
            ],
        );
    }
    
    function getCourses() : array {
        return array(
            [
                'name' => 'English Beginner'
            ],
            [
                'name' => 'English Elementary'
            ],
            [
                'name' => 'English Pre Intermediate'
            ],
            [
                'name' => 'English Intermediate'
            ],
            [
                'name' => 'English Upper Intermediate'
            ],
            [
                'name' => 'English Advanced'
            ],
            [
                'name' => 'English IELTS'
            ],
            [
                'name' => 'English High Math'
            ],
            [
                'name' => 'Russian High Math'
            ],
        );
    }
}
