<?php

namespace App\Nova;

use App\Nova\DefaultFields\UserFields;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphOne;

class Student extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Student>
     */
    public static $model = \App\Models\Student::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'user.name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'user.id', 'user.name', 'user.email', 'user.phone', 'groups.name'
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
    public static $with = ['user', 'groups'];

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
            UserFields::userID()->textAlign('left'),
            UserFields::avtProfilePhoto()->textAlign('left'),
            UserFields::imgProfilePhoto(),

            BelongsTo::make('User')
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->doesnthave('staff')->doesntHave('student');
                })
                ->textAlign('left')
                ->modalSize('3xl')
                ->rules('required')
                ->creationRules('unique:students,user_id')
                ->updateRules('unique:students,user_id,{{resourceId}}')
                ->showCreateRelationButton(),
            
            Text::make('Contacts', function () {
                return view('user.contact_info', [
                    'phone' => $this->user->phone,
                    'email' => $this->user->email,
                ])->render();
            })->asHtml(),

            Text::make('Group / Teacher', function () use($nova_path) {
                $result = '';
                $br_need = count($this->groups);
                foreach($this->groups as $group){
                    $result .= '<a class="link-default" target="_blank" href="' . $nova_path. '/resources/groups/' . $group->id . '">';
                    $result .= $group->name;
                    $result .= '</a> / ';
                    $result .= '<a class="link-default" target="_blank" href="' . $nova_path. '/resources/teachers/' . $group->teacher->id . '">';
                    $result .= $group->teacher->staff->user->name;
                    $result .= '</a>';
                    if($br_need){
                        $result .= '<br>';
                    }
                }
                return $result;
            })->asHtml()->textAlign('left')->exceptOnForms(),
            
            Text::make('Father\'s name', 'fathers_name')
                ->hideFromIndex()
                ->rules('max:100'),

            Text::make('Father\'s phone number', 'fathers_phone')
                ->hideFromIndex()
                ->rules('max:50'),

            Text::make('Mother\'s name', 'mothers_name')
                ->hideFromIndex()
                ->rules('max:100'),

            Text::make('Mother\'s phone number', 'mothers_phone')
                ->hideFromIndex()
                ->rules('max:50'),
            
            Markdown::make('Notes', 'notes')
                ->rules('max:700')
                ->fullWidth(),
            
            MorphOne::make('Address', 'address', Address::class)
                ->required(),

            BelongsToMany::make('Groups', 'groups', Group::class)
                ->searchable()
                ->withSubtitles()
        ];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return $this->user->phone;
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
