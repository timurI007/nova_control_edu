<?php

namespace App\Nova;

use App\Classes\GlobalVariable;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\CustomStatus;
use Laravel\Nova\Fields\Stack;

class TodaysLesson extends Lesson
{
    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Today (' . GlobalVariable::$week_days[date('w')] . ')';
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
        $query->where('weekday', date('w'));
        parent::indexQuery($request, $query);
    }

    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;

    /**
     * The interval at which Nova should poll for new resources.
     *
     * @var int
     */
    public static $pollingInterval = 300;

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;

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
            })->sortable(),

            // Edited Laravel\Nova\Fields\Status field
            CustomStatus::make('Status', function () {
                $start_time = strtotime($this->starts_at);
                $end_time = strtotime($this->ends_at);
                $current_time = strtotime(date('H:i'));
                if($current_time < $start_time){
                    return 'waiting';
                } else if($current_time >= $start_time && $current_time < $end_time){
                    return 'running';
                } else {
                    return 'ended';
                }
            })
                ->warningWhen(['waiting'])
                ->loadingWhen(['running'])
                ->failedWhen(['failed']),
        ];
    }
}
