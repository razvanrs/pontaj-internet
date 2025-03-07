<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Student extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Student::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'full_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'full_name', 'first_name', 'last_name', 'matriculation_number', 'identity_card_number'
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

            Text::make('Full Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('First Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Last Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Father First Name')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Mother First Name')
                ->nullable()
                ->hideFromIndex(),

            Date::make('Birthday')
                ->rules('required')
                ->hideFromIndex(),

            BelongsTo::make('Student Class', 'studentClass', StudentClass::class)
                ->rules('required'),

            BelongsTo::make('Schooling Period', 'schoolingPeriod', SchoolingPeriod::class)
                ->rules('required'),

            BelongsTo::make('Birth County', 'birthCounty', County::class)
                ->nullable(),

            Text::make('Birth Town')
                ->rules('required', 'max:255')
                ->hideFromIndex(),

            BelongsTo::make('Domicile County', 'domicileCounty', County::class)
                ->rules('required'),

            Text::make('Domicile Town')
                ->rules('required', 'max:255')
                ->hideFromIndex(),

            BelongsTo::make('Selection County', 'selectionCounty', County::class)
                ->rules('required'),

            BelongsTo::make('Residence County', 'residenceCounty', County::class)
                ->nullable(),

            Text::make('Residence Town')
                ->nullable()
                ->hideFromIndex(),

            BelongsTo::make('Practice County', 'practiceCounty', County::class)
                ->nullable(),

            Text::make('Practice Town')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Address')
                ->nullable()
                ->hideFromIndex(),

            BelongsTo::make('Ethnicity')
                ->rules('required'),

            BelongsTo::make('Sex')
                ->rules('required'),

            BelongsTo::make('Language')
                ->rules('required'),

            BelongsTo::make('Marital Status', 'maritalStatus')
                ->rules('required'),

            Text::make('Matriculation Number')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Identity Card Series')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Identity Card Number')
                ->nullable(),

            Text::make('Admission Exam Code')
                ->rules('required', 'max:255')
                ->hideFromIndex(),

            Number::make('Admission Exam Score')
                ->step(0.01)
                ->rules('required'),

            Number::make('BAC Grades Average', 'bac_grades_average')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Number::make('BAC Romanian Language Grade', 'bac_romanian_language_grade')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Number::make('BAC Main Subject Profile Grade', 'bac_main_subject_profile_grade')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Number::make('BAC Subject of Choice Profile Grade', 'bac_subject_of_choice_profile_grade')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Number::make('High School Avg Grade for 1st Foreign Lang', 'high_school_avg_grade_for_1st_foreign_lang')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Number::make('High School Avg Grade for 2nd Foreign Lang', 'high_school_avg_grade_for_2nd_foreign_lang')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Text::make('Car Brand')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Car Registration Number')
                ->nullable()
                ->hideFromIndex(),

            Number::make('ERP ID', 'erp_id')
                ->rules('required', 'unique:students,erp_id,{{resourceId}}'),

            BelongsToMany::make('Sanctions'),
        ];
    }
}
