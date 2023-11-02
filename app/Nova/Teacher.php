<?php

namespace App\Nova;

use App\Nova\DefaultFields\UserFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Number;
use Illuminate\Support\Facades\Config;
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
        'staff.user.id', 'staff.user.name', 'staff.user.email', 'staff.user.phone'
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
        $nova_path = Config::get('nova.path');
        return [
            URL::make('System ID', function () use ($nova_path){
                return $nova_path. '/resources/users/' . $this->staff->user->id;
            })
                ->displayUsing(fn () => $this->staff->user->id)
                ->textAlign('left')
                ->showWhenPeeking(),
            UserFields::avtProfilePhoto('staff.user.profile_photo')->textAlign('left'),
            UserFields::imgProfilePhoto('staff.user.profile_photo')->textAlign('left'),

            URL::make('Staff', function () use ($nova_path){
                return $nova_path. '/resources/staff/' . $this->staff->id;
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
                foreach($this->groups as $group)
                {
                    $result .= '<a class="link-default" href="' . $nova_path. '/resources/courses/' . $group->course->id . '">';
                    $result .= $group->course->name;
                    $result .= '</a>';
                }
                return $result;
            })->asHtml(),

            Number::make('Number Of Students', function () {
                $counter = 0;
                foreach($this->groups as $group)
                {
                    $counter += $group->students->count();
                }
                return $counter;
            })->sortable()->textAlign('left'),

            Number::make('Number Of Groups', function () {
                return $this->groups->count();
            })->sortable()->textAlign('left'),
            
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
        return [];
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
