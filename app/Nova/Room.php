<?php

namespace App\Nova;

use App\Classes\GlobalVariable;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;

class Room extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Room>
     */
    public static $model = \App\Models\Room::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Room', 'name')
                ->sortable()
                ->rules('required', 'max:30'),
            
            Number::make('Capacity', 'capacity')
                ->default(10)
                ->sortable()
                ->rules('required', 'numeric', 'min:0')
                ->textAlign('left'),
            
            Badge::make('Status')
                ->map(GlobalVariable::get_room_styles_optional())
                ->label(function ($value) {
                    return GlobalVariable::$rooms_labels[$value];
                })
                ->filterable()
                ->withIcons()
                ->textAlign('left'),
            
            Select::make('Status')
                ->options(GlobalVariable::get_room_status_optional())
                ->default(GlobalVariable::$rooms_status[0])
                ->onlyOnForms()
                ->rules('required'),
            
            HasMany::make('Lessons', 'lessons', Lesson::class),
            
            BelongsToMany::make('Equipment', 'equipment', Equipment::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Number::make('Amount', 'amount')
                            ->sortable()
                            ->default(1)
                            ->textAlign('center')
                            ->rules('required', 'numeric', 'min:0', 'max:255'),
                    ];
                })
                ->searchable()
                ->showCreateRelationButton()
                ->modalSize('3xl'),
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
