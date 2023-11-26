<?php

namespace App\Nova;

use App\Nova\DefaultFields\UserFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\URL;

class Teacher extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Teacher>
     */
    public static $model = \App\Models\Teacher::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'staff.user.name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'staff.user_id', 'staff.user.name', 'staff.user.email', 'staff.user.phone'
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['staff', 'groups'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $nova_path = config('nova.path');
        return [
            URL::make('System ID', function () use ($nova_path){
                return $nova_path . '/resources/users/' . $this->staff->user_id;
            })
                ->displayUsing(fn () => $this->staff->user_id)
                ->textAlign('left')
                ->showWhenPeeking(),
            // UserFields::avtProfilePhoto('staff.user.profile_photo')->textAlign('left'),
            UserFields::avtProfilePhoto('staff.user.name')->textAlign('left'),
            UserFields::imgProfilePhoto('staff.user.profile_photo')->textAlign('left'),

            URL::make('Staff', function () use ($nova_path){
                return $nova_path . '/resources/staff/' . $this->staff->id;
            })
                ->displayUsing(fn () => $this->staff->user->name)
                ->textAlign('left')
                ->showWhenPeeking(),
            
            Text::make('Contacts', function () {
                return view('user.contact_info', [
                    'phone' => $this->staff->user->phone,
                    'email' => $this->staff->user->email,
                ])->render();
            })->asHtml()->showWhenPeeking(),
            
            Text::make('Courses', function () use ($nova_path) {
                $result = '';
                $added_courses = array();
                foreach($this->groups as $group)
                {
                    if(in_array($group->course->id, $added_courses)){
                        continue;
                    }
                    $result .= '<a class="link-default" href="' . $nova_path. '/resources/courses/' . $group->course->id . '">';
                    $result .= $group->course->name;
                    $result .= '</a><br>';
                    $added_courses[] = $group->course->id;
                }
                return $result;
            })->asHtml(),
            
            Badge::make('Status', function () {
                return $this->staff->status;
            })
                ->map([
                    'Working' => 'success',
                    'Sickleave' => 'warning',
                    'Absent' => 'warning',
                    'Vacation' => 'warning',
                    'Dismissed' => 'danger',
                ])
                ->filterable()
                ->withIcons()
                ->showWhenPeeking(),
            
            HasMany::make('Groups', 'groups', Group::class)
        ];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return $this->staff->status;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [
            Lenses\TeacherStudents::make(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
