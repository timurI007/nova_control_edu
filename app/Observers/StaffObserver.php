<?php

namespace App\Observers;

use App\Classes\GlobalVariable;
use App\Models\Staff;
use App\Models\Teacher;

class StaffObserver
{
    /**
     * Handle the Staff "created" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function created(Staff $staff)
    {
        if($staff->position_id == GlobalVariable::$positions['teacher']){
            $this->createTeacher($staff->id);
        }
    }

    /**
     * Handle the Staff "updated" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function updated(Staff $staff)
    {
        //
    }

    /**
     * Handle the Staff "updated" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function updating(Staff $staff)
    {
        if($staff->getOriginal('position_id') == GlobalVariable::$positions['teacher']
        && $staff->position_id != GlobalVariable::$positions['teacher']){
            $this->deleteTeacher($staff->id);
        } else if($staff->getOriginal('position_id') != GlobalVariable::$positions['teacher']
        && $staff->position_id == GlobalVariable::$positions['teacher']){
            $this->createTeacher($staff->id);
        }
    }

    /**
     * Handle the Staff "deleted" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function deleted(Staff $staff)
    {
        if($staff->position_id == GlobalVariable::$positions['teacher']){
            $this->deleteTeacher($staff->id);
        }
    }

    /**
     * Handle the Staff "restored" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function restored(Staff $staff)
    {
        //
    }

    /**
     * Handle the Staff "force deleted" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function forceDeleted(Staff $staff)
    {
        //
    }

    private function deleteTeacher($staff_id)
    {
        $teacher = Teacher::where('staff_id', $staff_id)->first();
        if(!empty($teacher)){
            $teacher->delete();
        }
    }
    
    private function createTeacher($staff_id){
        $new_teacher = new Teacher();
        $new_teacher->staff_id = $staff_id;
        $new_teacher->save();
    }
}
