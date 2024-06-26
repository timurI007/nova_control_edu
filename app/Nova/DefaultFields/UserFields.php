<?php

namespace App\Nova\DefaultFields;

use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\UiAvatar;

class UserFields
{
    public static function userID($attribute = 'user.id')
    {
        return Number::make('System ID', $attribute)
            ->sortable()
            ->hideWhenCreating()
            ->hideWhenUpdating();
    }

    public static function avtProfilePhoto($attribute = 'user.name')
    {
        // return Avatar::make('Profile Photo', $attribute)
        //     ->path('user/avatar')
        //     ->indexWidth(50)
        //     ->onlyOnIndex();
        return UiAvatar::make('Avatar', $attribute)
            ->indexWidth(50)
            ->onlyOnIndex();
    }

    public static function imgProfilePhoto($attribute = 'user.profile_photo')
    {
        return Image::make('Profile Photo', $attribute)
            ->path('user/avatar')
            ->hideFromIndex()
            ->hideWhenCreating()
            ->hideWhenUpdating()
            ->maxWidth(250);
    }
}