<?php

namespace App\Nova;

use App\Classes\GlobalVariable;
use App\Models\GroupStudent;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
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
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->withCount('students');
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->name . ' | ' . $this->course->name;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'teacher.staff.user.name'
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
    public static $with = ['teacher', 'students', 'course', 'teacher.staff.user'];

    /**
	 * Apply any applicable orderings to the query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  array<string, string>  $orderings
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected static function applyOrderings($query, array $orderings)
	{
        $orderings['status'] = 'asc';
		return parent::applyOrderings($query, $orderings);
	}

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
                ->searchable()
                ->withSubtitles()
                ->filterable(),
            
            BelongsTo::make('Course')
                ->rules('required')
                ->filterable(),
            
            Badge::make('Status')
                ->map(GlobalVariable::get_group_styles_optional())
                ->label(function ($value) {
                    return GlobalVariable::$groups_labels[$value];
                })
                ->filterable()
                ->withIcons(),
            
            Select::make('Status')
                ->options(GlobalVariable::get_group_status_optional())
                ->default(GlobalVariable::$groups_status[0])
                ->onlyOnForms()
                ->rules('required'),
            
            Number::make('Number Of Students', 'students_count')
                ->sortable()
                ->onlyOnIndex()
                ->textAlign('left'),
            
            Number::make('Number Of Students', function () {
                return $this->students->count();
            })->onlyOnDetail(),
            
            BelongsToMany::make('Students', 'students', Student::class),

            HasMany::make('Lessons', 'lessons', Lesson::class)
        ];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return $this->teacher->staff->user->name;
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
