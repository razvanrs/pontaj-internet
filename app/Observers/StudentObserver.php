<?php

namespace App\Observers;

use App\Models\Student;
use Illuminate\Support\Str;

class StudentObserver
{

    /**
     * Handle the Student "created" event.
     *
     * @param  \App\Models\Student $student
     * @return void
     */
    public function creating(Student $student)
    {
        $student->full_name = $this->generateFullName($student);
    }

    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "updated" event.
     *
     * @param  \App\Models\Student  $student
     * @return void
     */
    public function updating(Student $student)
    {
        $student->full_name = $this->generateFullName($student);
    }

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "restored" event.
     */
    public function restored(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "force deleted" event.
     */
    public function forceDeleted(Student $student): void
    {
        //
    }

    /**
     * Genereaza numele complet din nume, prenume, tata/mama
     */
    private function generateFullName($student): String
    {
        $middleName = Str::lower($student->father_first_name) !== 'natural' ? $student->father_first_name : $student->mother_first_name;
        $middleName = Str::title(Str::lower($middleName));

        return Str::upper($student->last_name) . ' ' . $middleName . ' ' . Str::upper($student->first_name);
    }
}
