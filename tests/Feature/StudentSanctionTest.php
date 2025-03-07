<?php

namespace Tests\Feature;

use App\Models\StudentClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentSanctionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_teacher_can_sanction_a_student(): void
    {
        // Arrange
        $user = User::factory()->create();
        Role::create(['name' => 'teacher']);
        $user->assignRole('teacher');
        // create employee details and assign to user
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $user->employee()->save($employee);
        // create teacher details and assign to employee
        $teacher = \App\Models\Teacher::factory()->create(['employee_id' => $employee->id]);
        $employee->teacher()->save($teacher);
        // create student details
        $studentClass = StudentClass::factory()->create();
        $student = \App\Models\Student::factory()->create(['student_class_id' => $studentClass->id]);
        // create sanction details
        $sanction = \App\Models\Sanction::factory()->create();
        // Act
        $this->get(route('sanction'));
        $date = Carbon::now();
        $response = $this->actingAs($user)->post(route('sanction.store'), [
            'student' => $student->id,
            'sanction' => $sanction->id,
            'date' => $date,
        ]);

        $this->assertDatabaseHas('student_sanction', [
            'student_id' => $student->id,
            'sanction_id' => $sanction->id,
            'user_id' => $user->id,
            'date' => $date->format("Y-m-d"),
        ]);

        $response->assertRedirectToRoute('sanction');
    }

    public function test_sanctions_are_correctly_displayed_on_sanctions_index_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'teacher']);
        $user->assignRole('teacher');
        // create employee details and assign to user
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $user->employee()->save($employee);
        // create teacher details and assign to employee
        $teacher = \App\Models\Teacher::factory()->create(['employee_id' => $employee->id]);
        $employee->teacher()->save($teacher);
        // create student details
        $studentClass = StudentClass::factory()->create();
        $student = \App\Models\Student::factory()->create(['student_class_id' => $studentClass->id]);
        // create sanction details
        $sanctions = \App\Models\Sanction::factory(4)->create();

        $student->sanctions()->syncWithPivotValues($sanctions->pluck('id'), [
            'user_id' => $user->id,
            'date' => Carbon::now()->format("Y-m-d"),
        ]);


        $response = $this->actingAs($user)->get(route('sanction'));
        $response->assertInertia(
            fn(Assert $page) => $page
                ->has(
                    'studentsWithSanctions.data',
                    1,
                )
            // ->has(
            //     'studentsWithSanctions.data.0.student.sanctions',
            //     4,
            // )
        );
    }
}
