<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\UiAvatar;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

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
        'id', 'name', 'email', 'phone'
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
            ID::make()->sortable(),

            // Avatar::make('Profile Photo', 'profile_photo')
            //     ->path('user/avatar')
            //     ->indexWidth(50)
            //     ->onlyOnIndex()
            //     ->prunable(),

            UiAvatar::make()
                ->indexWidth(50)
                ->onlyOnIndex(),

            Image::make('Profile Photo', 'profile_photo')
                ->path('user/avatar')
                ->hideFromIndex()
                ->rules('image')
                ->maxWidth(250),

            Text::make('Full Name', 'name')
                ->sortable()
                ->showWhenPeeking()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->showWhenPeeking()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Text::make('Phone')
                ->showWhenPeeking()
                ->rules('required', 'max:30')
                ->creationRules('unique:users,phone')
                ->updateRules('unique:users,phone,{{resourceId}}'),
            
            Text::make('Age', 'birthdate')
                ->displayUsing(function ($birthDate) {
                    //explode the date to get year, month and day
                    $birthDate = explode("-", date_create($birthDate)->format('Y-m-d'));
                    $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
                      ? ((date("Y") - $birthDate[0]) - 1)
                      : (date("Y") - $birthDate[0]));
                    return $age;
                })
                ->sortable()
                ->showWhenPeeking()
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            
            Date::make('Date Of Birth', 'birthdate')
                ->rules('required', 'before:today')
                ->hideFromIndex(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),
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
