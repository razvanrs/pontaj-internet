<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class StudentClass extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\StudentClass::class;

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
        'id', 'name', 'erp_id'
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

            Number::make('ERP ID', 'erp_id')
                ->sortable()
                ->rules('required', 'integer'),

            Number::make('ERP Schooling Period ID', 'erp_schooling_period_id')
                ->sortable()
                ->rules('required', 'integer'),

            Number::make('ERP Form Teacher ID', 'erp_form_teacher_id')
                ->nullable()
                ->sortable(),

            BelongsTo::make('Form Teacher', 'formTeacher', Teacher::class)
                ->nullable()
                ->searchable(),

            Number::make('Sel Order')
                ->sortable()
                ->rules('nullable', 'integer')
                ->default(0),

            BelongsTo::make('Form Teacher', 'formTeacher', Teacher::class)
                ->nullable()
                ->searchable(),

            BelongsTo::make('Schooling Period', 'schoolingPeriod', SchoolingPeriod::class)
                ->nullable()
                ->searchable(),

            HasMany::make('Students'),
        ];
    }
}
