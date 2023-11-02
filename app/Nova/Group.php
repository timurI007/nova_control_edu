<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;

class Group extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Group>
     */
    public static $model = \App\Models\Group::class;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->name . '|' . $this->course->name;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'course.name'
    ];

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = true;

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['teacher', 'students', 'course'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Name')
                ->rules('required', 'string', 'max:20')
                ->creationRules('unique:groups,name')
                ->updateRules('unique:groups,name,{{resourceId}}')
                ->sortable(),

            BelongsTo::make('Teacher')
                ->rules('required')
                ->filterable(),
            
            Number::make('Number Of Students', function () {
                return $this->students->count();
            })->sortable()->exceptOnForms()->textAlign('left'),
            
            BelongsTo::make('Course')
                ->rules('required')
                ->filterable(),
            
            Badge::make('Status')
                ->map([
                    'Recruitment' => 'info',
                    'Studying' => 'success',
                    'Suspended' => 'warning',
                    'Finished' => 'info',
                ])
                ->filterable()
                ->withIcons(),
            
            Select::make('Status')->options([
                    'Recruitment' => 'Recruitment',
                    'Studying' => 'Studying',
                    'Suspended' => 'Suspended',
                    'Finished' => 'Finished',
                ])->default('Recruitment')
                ->onlyOnForms()
                ->rules('required'),
            
            BelongsToMany::make('Students', 'students', Student::class)
        ];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return 'Teacher: ' . $this->teacher->staff->user->name;
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
