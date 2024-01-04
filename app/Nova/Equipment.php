<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class Equipment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Equipment>
     */
    public static $model = \App\Models\Equipment::class;

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
            Image::make('Photo', 'photo')
                ->path('equipment/photo')
                ->textAlign('left')
                ->onlyOnIndex()
                ->maxWidth(50),
            
            Image::make('Photo', 'photo')
                ->path('equipment/photo')
                ->rules('image')
                ->hideFromIndex()
                ->maxWidth(250),
            
            Text::make('Name', 'name')
                ->textAlign('left')
                ->rules('required', 'max:50'),

            Number::make('Total', 'amount_sum')
                ->sortable()
                ->exceptOnForms()
                ->textAlign('left')
                ->hideFromIndex(function () use ($request) {
                    return $request->viaResource;
                }),

            KeyValue::make('Parameters', 'parameters')
                ->rules('json')
                ->nullable(),
            
            BelongsToMany::make('Rooms', 'rooms', Room::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Number::make('Amount', 'amount')
                            ->sortable()
                            ->default(1)
                            ->textAlign('center')
                            ->rules('required', 'numeric', 'min:0', 'max:255'),
                    ];
                })
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
