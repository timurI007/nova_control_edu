<?php

namespace App\Nova;

use App\Classes\GlobalVariable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Stack;

class Lesson extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Lesson>
     */
    public static $model = \App\Models\Lesson::class;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'All Lessons';
    }

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return 'Create Lesson';
    }

    /**
     * Get the text for the update resource button.
     *
     * @return string|null
     */
    public static function updateButtonLabel()
    {
        return 'Update Lesson';
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->when(empty($request->get('orderBy')), function(Builder $q) {
            $q->getQuery()->orders = [];
            $q->orderBy('starts_at');
            return $q->orderBy('ends_at');
        });
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'group.course.name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'group.name', 'group.course.name', 'room.name', 'group.teacher.staff.user.name'
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['group', 'room', 'group.course', 'group.teacher.staff.user'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Stack::make('Group', [
                BelongsTo::make('group')
                    ->searchable()
                    ->filterable(),
                fn () => optional($this->group)->teacher->staff->user->name
            ]),
            
            BelongsTo::make('Room', 'room')
                ->filterable(),

            Select::make('Week Day', 'weekday')
                ->options(GlobalVariable::$week_days)
                ->displayUsingLabels()
                ->sortable(),
            
            DateTime::make('Starts at', 'starts_at')->displayUsing(function ($value) {
                return date('H:i', strtotime($value));
            })->sortable(),
            
            DateTime::make('Ends at', 'ends_at')->displayUsing(function ($value) {
                return date('H:i', strtotime($value));
            })->sortable()
        ];
    }

    private function fieldsForForms(NovaRequest $request){
        $day_of_week = date('w');
        return [
            BelongsTo::make('Group', 'group')
                ->rules('required')
                ->searchable()
                ->withSubtitles()
                ->dontReorderAssociatables(),
            
            BelongsTo::make('Room', 'room')
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->where('is_active', 1);
                })
                ->rules('required'),

            Select::make('Week Day', 'weekday')
                ->options(GlobalVariable::$week_days)
                ->default($day_of_week)
                ->displayUsingLabels()
                ->rules('required', 'integer', 'min:1', 'max:7'),
            
            Text::make('Starts at', 'starts_at')->resolveUsing(function ($value) {
                if(empty($value)){
                    return date('H:i');
                }
                return date('H:i', strtotime($value));
            })->rules('required', 'date_format:"H:i"')->withMeta(['extraAttributes' => ['type' => 'time']]),
            
            Text::make('Ends at', 'ends_at')->resolveUsing(function ($value) {                
                if(empty($value)){
                    return date('H:i', strtotime('+1 hours'));
                }
                return date('H:i', strtotime($value));
            })->rules('required', 'date_format:"H:i"', 'after:starts_at')->withMeta(['extraAttributes' => ['type' => 'time']]),
        ];
    }

    /**
     * Get the fields displayed by the resource on update page.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForUpdate(NovaRequest $request)
    {
        return $this->fieldsForForms($request);
    }

    /**
     * Get the fields displayed by the resource on update page.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForCreate(NovaRequest $request)
    {
        return $this->fieldsForForms($request);
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return \Laravel\Nova\URL|string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/' . static::uriKey() . '/';
    }

    /**
     * Handle any post-validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterValidation(NovaRequest $request, $validator)
    {
        $room_id = $request->post('room');
        $weekday = $request->post('weekday');
        $starts_at = $request->post('starts_at');
        $ends_at = $request->post('ends_at');

        $start_time = strtotime($starts_at);
        $end_time = strtotime($ends_at);
        $time_diff = abs($end_time - $start_time);
        // >= 0 hours 30 minutes && <= 3 hours 0 minutes
        if (!($time_diff >= 1800 && $time_diff <= 10800)) {
            $validator->errors()->add(
                'starts_at',
                'The lesson time should be at least 30 minutes and no more than 3 hours.'
            );
            $validator->errors()->add(
                'ends_at',
                'The lesson time should be at least 30 minutes and no more than 3 hours.'
            );
        }

        $interval = [$starts_at, $ends_at];
        // validate
        $model_query = \App\Models\Lesson::where('room_id', $room_id)
            ->where('weekday', $weekday)
            ->where(function ($query) use ($request, $interval) {
                $query->whereBetween('starts_at', $interval)
                    ->orWhereBetween('ends_at', $interval);
            });
        
        // if updating
        if($request->route('resourceId')){
            $model_query->where('id', '!=', $request->route('resourceId'));
        }
        $lesson = $model_query->first();
        
        // checking fails
        if ($lesson) {
            $label_week_day = GlobalVariable::$week_days[$weekday];
            $validator->errors()->add(
                'room',
                "The room is occupied at this time on {$label_week_day}. (lesson id - {$lesson->id})"
            );
        }
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
        return [
            new Filters\WeekDay,
        ];
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
