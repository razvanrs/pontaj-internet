<?php

namespace App\Http\Controllers;

use App\Data\TeacherScheduleData;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\LearningActivityType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all teachers with their employee info, ordered by employee.full_name
        $teachers = Teacher::join('employees', 'teachers.employee_id', '=', 'employees.id')
            ->select('teachers.id', 'employees.full_name')
            ->orderBy('employees.full_name')
            ->get();

            // dd($teachers);
        
        // Get teacher schedules for the current week
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        
        $teacherSchedules = TeacherSchedule::with([
            'learningActivityType', 'module', 'ability', 'theme', 
            'location', 'teacher.employee'
        ])
        ->whereBetween('date_start', [$startDate, $endDate])
        ->get();

        $teachers = $teachers->map(function($item) use ($startDate, $endDate){

            $totalMinutes = 0;
            
            $teacherSchedules = TeacherSchedule::where('teacher_id', "=", $item->id)->whereBetween('date_start', [$startDate, $endDate])->get();
            foreach($teacherSchedules as $teacherSchedule){
                $totalMinutes += $teacherSchedule->total_minutes;
            }

            return [
                "id" => $item->id,
                "full_name" => $item->full_name,
                'total_hours' => number_format($totalMinutes/60,2,'.', ',')
            ];
        });
        
        // Transform schedules to calendar events
        $events = TeacherScheduleData::collection($teacherSchedules);
        
        // Get all learning activity types
        $activities = LearningActivityType::get();

        return Inertia::render('DashboardPage')
            ->with([
                "teachers" => $teachers,
                "events" => $events,
                "activities" => $activities,
            ]);
    }
}