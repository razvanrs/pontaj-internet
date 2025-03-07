<?php

namespace App\Nova;

use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class EmployeeSchedule extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EmployeeSchedule>
     */
    public static $model = \App\Models\EmployeeSchedule::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
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
            BelongsTo::make('Year'),
            BelongsTo::make('Week'),
            BelongsTo::make('Day'),
            BelongsTo::make('Employee')->searchable(),
            BelongsTo::make('Schedule status'),
            DateTime::make('Date start')->step(CarbonInterval::minutes(30)),
            DateTime::make('Date finish')->step(CarbonInterval::minutes(30)),
            Text::make('Total minutes')
                ->rules(['required'])
                ->readonly(),
            Text::make('Total hours')
                ->rules(['required'])
                ->readonly(),
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
