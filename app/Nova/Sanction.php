<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Sanction extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Sanction::class;

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
        'id', 'name', 'short_description', 'long_description'
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
                ->rules('required', 'max:255'),

            Text::make('Long Description')
                ->rules('required'),

            Number::make('Sel Order')
                ->sortable()
                ->rules('nullable', 'integer')
                ->default(0),

            BelongsToMany::make('Students'),
        ];
    }
}
