<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Repeater;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Naoray\NovaJson\JSON;

class Employee extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Employee>
     */
    public static $model = \App\Models\Employee::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'novaId';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','social_number','full_name'
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
            BelongsTo::make('User'),
            BelongsTo::make('Military Rank'),
            BelongsTo::make('Military Rank Type'),
            Text::make('Social number')
                ->rules(['required']),
            Text::make('Name')
                ->rules(['required']),
            Text::make('Surname')
                ->rules(['required']),
            Text::make('Full name')
                ->rules(['required']),
            Text::make('Father surname')
                ->rules(['required']),
            Textarea::make('Address')
                ->rules(['required']),
            Date::make('Birthday')
                ->rules(['required', 'date'])
                ->sortable(),
            BelongsTo::make('Sex'),
            JSON::make('Phone Numbers', [
                Text::make('Fix'),
                Text::make('Int'),
                JSON::make('Mobile', [
                    Text::make('Mobile 1')->rules('nullable'),
                    Text::make('Mobile 2')->rules('nullable'),
                    Text::make('Mobile 3')->rules('nullable'),
                ]),
            ]),
            BelongsToMany::make('BusinessUnits'),
            Text::make('Erp id')
                ->rules(['required']),
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
