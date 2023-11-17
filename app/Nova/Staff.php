<?php

namespace App\Nova;

use App\Classes\GlobalVariable;
use App\Nova\DefaultFields\UserFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphOne;
use Sadekd\NovaOpeningHoursField\NovaOpeningHoursField;

class Staff extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Staff>
     */
    public static $model = \App\Models\Staff::class;

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
        'user_id', 'user.name'
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
    public static $with = ['user', 'position', 'department'];

    /**
	 * Apply any applicable orderings to the query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  array<string, string>  $orderings
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected static function applyOrderings($query, array $orderings)
	{
		if (empty($orderings)) {
			$orderings['department_id'] = 'desc';
		}
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
            UserFields::userID()->textAlign('left'),
            UserFields::imgProfilePhoto()->textAlign('left'),

            BelongsTo::make('User')
                ->searchable()
                ->modalSize('3xl')
                ->showCreateRelationButton()
                ->rules('required')
                ->creationRules('unique:staff,user_id')
                ->updateRules('unique:staff,user_id,{{resourceId}}'),

            BelongsTo::make('Position', 'position')
                ->showCreateRelationButton()
                ->showWhenPeeking()
                ->filterable(),
                
            BelongsTo::make('Department', 'department', Department::class)
                ->showCreateRelationButton()
                ->showWhenPeeking()
                ->filterable(),
            
            Badge::make('Status')
                ->map([
                    'Working' => 'success',
                    'Sickleave' => 'warning',
                    'Absent' => 'warning',
                    'Vacation' => 'warning',
                    'Dismissed' => 'danger',
                ])
                ->filterable()
                ->exceptOnForms()
                ->withIcons(),
            
            Select::make('Status')->options([
                    'Working' => 'Working',
                    'Sickleave' => 'Sickleave',
                    'Absent' => 'Absent',
                    'Vacation' => 'Vacation',
                    'Dismissed' => 'Dismissed'
                ])
                ->onlyOnForms()
                ->rules('required'),

            NovaOpeningHoursField::make('Working Hours', 'working_hours')
                ->allowExceptions(false),
            
            Markdown::make('Notes', 'notes')
                ->rules('max:700')
                ->fullWidth(),
            
            MorphOne::make('Address', 'address', Address::class)
                ->onlyOnForms()
                ->required(),
        ];
    }

    /**
     * Get the fields displayed by the resource on index page.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForIndex(NovaRequest $request)
    {
        $date_of_week = date('w');
        $current_time = date('H:i');
        return [
            UserFields::userID()->textAlign('left'),
            UserFields::avtProfilePhoto()->textAlign('center'),

            BelongsTo::make('User'),
            
            BelongsTo::make('Position', 'position')
                ->filterable(),

            BelongsTo::make('Department', 'department', Department::class)
                ->filterable(),
            
            Boolean::make('Is Working Time', function() use ($date_of_week, $current_time){
                switch($date_of_week){
                    case 0: return GlobalVariable::getIsWorkTime($this->working_hours['sunday'], $current_time); break;
                    case 1: return GlobalVariable::getIsWorkTime($this->working_hours['monday'], $current_time); break;
                    case 2: return GlobalVariable::getIsWorkTime($this->working_hours['tuesday'], $current_time); break;
                    case 3: return GlobalVariable::getIsWorkTime($this->working_hours['wednesday'], $current_time); break;
                    case 4: return GlobalVariable::getIsWorkTime($this->working_hours['thursday'], $current_time); break;
                    case 5: return GlobalVariable::getIsWorkTime($this->working_hours['friday'], $current_time); break;
                    case 6: return GlobalVariable::getIsWorkTime($this->working_hours['saturday'], $current_time);
                }
            })
                ->textAlign('center')
                ->onlyOnIndex(),
            
            Badge::make('Status')
                ->map([
                    'Working' => 'success',
                    'Sickleave' => 'warning',
                    'Absent' => 'warning',
                    'Vacation' => 'warning',
                    'Dismissed' => 'danger',
                ])
                ->textAlign('left')
                ->filterable()
                ->withIcons(),
        ];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return $this->position->name . ', ' . $this->department->name;
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

    /**
     * Handle any post-validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    // protected static function afterValidation(NovaRequest $request, $validator)
    // {
    //     $user_id = $request->post('user');
    //     $category_id = $request->post('department');

    //     // logic of rule
    //     $unique = Rule::unique('staff', 'user_id')
    //         ->where('user_id', $user_id)
    //         ->where('category_id', $category_id);
        
    //     // if updating
    //     if ($request->route('resourceId')) {
    //         $unique->ignore($request->route('resourceId'));
    //     }

    //     // validate
    //     $uniqueValidator = Validator::make($request->only('user'), [
    //         'user' => [$unique],
    //     ]);
        
    //     // checking fails
    //     if ($uniqueValidator->fails()) {
    //         $validator->errors()->add(
    //             'user',
    //             'User with this staff category is already existed.'
    //         );
    //     }
    // }
}
