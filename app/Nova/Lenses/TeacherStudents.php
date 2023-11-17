<?php

namespace App\Nova\Lenses;

use App\Nova\DefaultFields\UserFields;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Nova;

class TeacherStudents extends Lens
{
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'staff.user_id', 'staff.user.name'
    ];

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withoutTableOrderPrefix()->withOrdering($request->withFilters(
            $query->select(self::columns())
                ->leftJoin('groups', 'teachers.id', '=', 'groups.teacher_id')
                ->leftJoin('groups_students', 'groups.id', '=', 'groups_students.group_id')
                ->leftJoin('students', 'groups_students.student_id', '=', 'students.id')
                ->join('staff', 'teachers.staff_id', '=', 'staff.id')
                ->join('users', 'staff.user_id', '=', 'users.id')
                ->groupBy('teachers.id')
        ));
    }

    /**
     * Get the columns that should be selected.
     *
     * @return array
     */
    protected static function columns()
    {
        return [
            'teachers.id',
            'users.id as user_id',
            'staff.id as staff_id',
            'users.name as user_name',
            DB::raw('count(distinct students.id) AS total_students'),
            DB::raw('count(distinct groups.id) AS total_groups'),
        ];
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $nova_path = config('nova.path');
        return [
            URL::make('System ID', function () use ($nova_path){
                return $nova_path . '/resources/users/' . $this->user_id;
            })
                ->displayUsing(fn () => $this->user_id)
                ->textAlign('left'),

            URL::make('Staff', function () use ($nova_path){
                return $nova_path . '/resources/staff/' . $this->staff_id;
            })
                ->displayUsing(fn () => $this->user_name)
                ->textAlign('left'),

            Number::make('Number Of Groups', 'total_groups')->sortable()->textAlign('left'),

            Number::make('Number Of Students', 'total_students')->sortable()->textAlign('left'),
            
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'teacher-students';
    }
}
