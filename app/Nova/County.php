<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class County extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\County::class;

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
        'id', 'name', 'abbreviation', 'short_description'
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

            Text::make('Long Description')
                ->rules('required', 'max:255'),

            Text::make('Abbreviation')
                ->sortable()
                ->rules('max:10'),

            Number::make('Sel Order')
                ->sortable()
                ->rules('nullable', 'integer')
                ->default(0),
        ];
    }
}
