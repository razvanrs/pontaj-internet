<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class BusinessUnitGroup extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\BusinessUnitGroup>
     */
    public static $model = \App\Models\BusinessUnitGroup::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The group the resource belongs to.
     *
     * @var string
     */
    public static $group = 'Nomenclatoare';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'code', 'name',
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

            Text::make('Code')
                ->sortable()
                ->rules('required', 'max:255')
                ->creationRules('unique:business_unit_groups,code')
                ->updateRules('unique:business_unit_groups,code,{{resourceId}}'),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Number::make('Display Order', 'sel_order')
                ->sortable()
                ->rules('required', 'integer')
                ->default(0),

            Textarea::make('Description')
                ->nullable()
                ->alwaysShow(),

            HasMany::make('Business Units', 'businessUnits', BusinessUnit::class),
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
