<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class SchoolingPeriod extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\SchoolingPeriod::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'short_description';

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
        'id', 'short_description'
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

            Text::make('Short Description')
                ->sortable()
                ->rules('required', 'max:255'),

            Date::make('Started At')
                ->sortable()
                ->rules('required', 'date'),

            Date::make('Finished At')
                ->sortable()
                ->rules('required', 'date', 'after:started_at'),

            Number::make('Sel Order')
                ->sortable()
                ->help('Display order for this schooling period')
                ->rules('required')
                ->default(0),

            Number::make('ERP ID', 'erp_id')
                ->sortable()
                ->rules('required', 'integer'),

            DateTime::make('Created At')
                ->hideFromIndex()
                ->exceptOnForms(),

            DateTime::make('Updated At')
                ->hideFromIndex()
                ->exceptOnForms(),
        ];
    }
}
