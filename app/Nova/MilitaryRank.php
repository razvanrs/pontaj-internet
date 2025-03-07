<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class MilitaryRank extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\MilitaryRank>
     */
    public static $model = \App\Models\MilitaryRank::class;

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
        'id','name','abbreviation'
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

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Abbreviation')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsTo::make('Military Rank Type', 'militaryRankType', MilitaryRankType::class)
                ->sortable()
                ->rules('required'),
        ];
    }
}
