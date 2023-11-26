<?php

namespace App\Nova\Actions;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class AddStudentsToGroup extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = "Add to Group";

    /**
     * The text to be used for the action's confirm button.
     *
     * @var string
     */
    public $confirmButtonText = 'Add If Not Exist';

    /**
     * The text to be used for the action's cancel button.
     *
     * @var string
     */
    public $cancelButtonText = 'Cancel';

    /**
     * The text to be used for the action's confirmation text.
     *
     * @var string
     */
    public $confirmText = 'Please, choose group to add selected students.';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $groupId = $fields->group;
        $group = \App\Models\Group::findOrFail($groupId);
        $studentIds = $models->pluck('id')->toArray();
        $group->students()->syncWithoutDetaching($studentIds);
        return Action::message(count($studentIds) . ' students added to the group successfully!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $groups = \App\Models\Group::with(['teacher.staff.user', 'course'])->get();
        $formattedGroups = [];
        foreach ($groups as $group) {
            $formattedGroups[$group->id] = [
                'label' => $group->teacher->staff->user->name,
                'group' => $group->name  . ' (' . $group->course->name . ')'
            ];
        }
        return [
            Select::make('Group')
                ->searchable()
                ->options($formattedGroups)
                ->displayUsingLabels()
                ->rules('required')
        ];
    }
}
