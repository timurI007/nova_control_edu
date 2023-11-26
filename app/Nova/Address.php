<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;

class Address extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Address>
     */
    public static $model = \App\Models\Address::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'addressable.user.name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'addressable.user.id',
        'addressable.user.name'
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
            MorphTo::make('addressable')
                ->types([
                    Staff::class,
                    Student::class,
                ])
                ->default(2)
                ->defaultResource(Student::class)
                ->searchable()
                ->withSubtitles(),

            Text::make('City', 'city')
                ->rules('max:30'),

            Text::make('District', 'district')
                ->rules('max:30'),
            
            Text::make('Street', 'street')
                ->rules('max:30'),

            Text::make('Housing', 'housing')
                ->rules('max:30'),
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
