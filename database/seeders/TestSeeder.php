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
        $equipment = $this->getEquipment();

        // Saving

        // \App\Models\User::factory()->count(20)->create();
        // saving defined
        foreach ($departments as $department){
            \App\Models\Department::factory()->create($department);
        }
        foreach ($positions as $position){
            \App\Models\Position::factory()->create($position);
        }
        foreach ($courses as $course){
            \App\Models\Course::factory()->create($course);
        }
        $chair = \App\Models\Equipment::factory()->create($equipment[0]);
        $table = \App\Models\Equipment::factory()->create($equipment[1]);
        // rooms equipment
        \App\Models\Room::factory()->count(10)->hasAttached(
            $chair,
            [
                'amount' => 15
            ],
        )->hasAttached(
            $table,
            [
                'amount' => 7
            ]
        )->create();
        // teachers groups students
        \App\Models\Staff::factory()->count(10)->create([
            'position_id' => 1,
            'department_id' => 1
        ])->each(function ($staff) {
            \App\Models\Group::factory()->count(random_int(1, 4))->create([
                'course_id' => random_int(1, 9),
                'teacher_id' => $staff->id,
            ])->each(function ($group) {
                $students = \App\Models\Student::factory()->count(random_int(10, 14))->create()->pluck('id')->toArray();
                $group->students()->attach($students);
            });
        });
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

    function getEquipment() : array {
        return array(
            [
                'id' => 1,
                'name' => 'Chair'
            ],
            [
                'id' => 2,
                'name' => 'Table'
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
