<?php

namespace App\Http\Controllers;

use App\Data\TeacherScheduleData;
use App\Models\Ability;
use App\Models\Day;
use App\Models\LearningActivityType;
use App\Models\Location;
use App\Models\Module;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\Theme;
use App\Models\Week;
use App\Models\Year;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PlanificareProfesoriController extends Controller
{

    public $successStatus = 200;

    public function index()
	{
        $teachers = Teacher::with(['employee'])->get();
        $modules = Module::get();
        $abilities = Ability::get();
        $activities = LearningActivityType::get();
        $themes = Theme::get();
        $locations = Location::get();

        return Inertia::render('PlanificareProfesori')
            ->with([
                "teachers" => $teachers,
                "modules" => $modules,
                "abilities" => $abilities,
                "activities" => $activities,
                "themes" => $themes,
                "locations" => $locations,
                "events" => []
            ]);
	}

    public function addTeacherActivity(Request $request)
    {

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'activityType' => 'Tip activitate',
            'module' => 'Modul',
            'ability' => 'Abilitate',
            'theme' => 'Temă',
            'location' => 'Locație',
            'teacher' => 'Profesor',
            'dateStart' => 'Data începerii activității',
            'dateEnd' => 'Data finalizării activității',
        ];

        $validator = Validator::make($request->all(), [
            'activityType' => ['required'],
            'module' => ['required'],
            'ability' => ['required'],
            'theme' => ['required'],
            'location' => ['required'],
            'teacher' => ['required'],
            'dateStart' => ['required'],
            'dateEnd' => ['required'],
        ], $messages, $attributes);

        //validat period overlaps
        $validator->after(function ($validator) use ($request){

            if($request->formAction == 'add'){

                $start = (new Carbon($request->dateStart));
                $end = (new Carbon($request->dateEnd));

                ray()->showQueries();

                /** Validate existing session in same period */
                if($request->teacher){

                    $teacherSchedule = new TeacherSchedule();
                    $events = $teacherSchedule
                        ->whereBetween('date_start', [$start, $end])
                        ->where('teacher_id', $request->teacher["id"])
                        ->get();

                    ray($events);

                    if($events->count() > 0){
                        $validator->errors()->add(
                            'dateStart', 'Perioada selectată se suprapune peste un eveniment deja introdus pentru acest profesor !'
                        );
                    }

                    $teacherSchedule = new TeacherSchedule();
                    $events = $teacherSchedule
                        ->whereBetween('date_finish', [$start, $end])
                        ->where('teacher_id', $request->teacher["id"])
                        ->get();

                    ray($events);

                    if($events->count() > 0){
                        $validator->errors()->add(
                            'dateStart', 'Perioada selectată se suprapune peste un eveniment deja introdus pentru acest profesor !'
                        );
                    }
                }

                if($request->location){

                    /** Validate location for existing session in the same period */
                    $teacherSchedule = new TeacherSchedule();
                    $events = $teacherSchedule
                        ->whereBetween('date_start', [$start, $end])
                        ->where('location_id', $request->location["id"])
                        ->get();

                    ray($events);

                    if($events->count() > 0){
                        $validator->errors()->add(
                            'dateStart', 'Exista un eveniment in aceasta sala in perioada specificata !'
                        );
                    }

                    $teacherSchedule = new TeacherSchedule();
                    $events = $teacherSchedule
                        ->whereBetween('date_finish', [$start, $end])
                        ->where('location_id', $request->location["id"])
                        ->get();

                    ray($events);

                    if($events->count() > 0){
                        $validator->errors()->add(
                            'dateStart', 'Exista un eveniment in aceasta sala in perioada specificata !'
                        );
                    }
                }


                if($request->teacher && $request->theme){

                    /** Validate if in the current year the teacher has already teach this theme  */
                    $start = (new Carbon())->startOfYear();
                    $end = (new Carbon())->endOfYear();

                    $teacherSchedule = new TeacherSchedule();
                    $events = $teacherSchedule
                        ->whereBetween('date_start', [$start, $end])
                        ->where('teacher_id', $request->teacher["id"])
                        ->where('theme_id', $request->theme["id"])
                        ->get();

                    ray($events);

                    if($events->count() > 0){
                        $validator->errors()->add(
                            'dateStart', 'Profesorul are deja planificata aceasta tema !'
                        );
                    }
                }

                ray()->stopShowingQueries();
            }
        });


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        if($request->formAction == 'add'){

            $dates = new Carbon($request->dateStart);
            $datef = new Carbon($request->dateEnd);

            $year = new Year();
            $year = $year->whereCode($dates->year)->first();

            $week = new Week();
            $week = $week->whereCode($dates->week)->first();

            $day = new Day();
            $day = $day->whereCode($dates->day)->first();

            $teacherSchedule = new TeacherSchedule();
            $teacherSchedule->learning_activity_type_id = $request->activityType["id"];
            $teacherSchedule->year_id = $year->id;
            $teacherSchedule->week_id = $week->id;
            $teacherSchedule->day_id = $day->id;
            $teacherSchedule->module_id = $request->module["id"];
            $teacherSchedule->ability_id = $request->ability["id"];
            $teacherSchedule->theme_id = $request->theme["id"];
            $teacherSchedule->location_id = $request->location["id"];
            $teacherSchedule->teacher_id = $request->teacher["id"];
            $teacherSchedule->date_start = $dates->format("Y-m-d H:i:s");
            $teacherSchedule->date_finish = $datef->format("Y-m-d H:i:s");
            $teacherSchedule->total_minutes =$datef->diffInMinutes($dates);
            $teacherSchedule->save();
        }

        return to_route('teacherSchedule');
    }

    public function getEvents(Request $request){

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'teacherId' => 'Profesor',
        ];

        $validator = Validator::make($request->all(), [
            'teacherId' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $teacherSchedule = new TeacherSchedule();
        $startDayMonth = !$request->startString ? now()->startOfWeek() : $request->startString;
        $endDayMonth = !$request->endString ? now()->endOfWeek() : $request->endString;

        $events = $teacherSchedule
            ->whereBetween('date_start', [$startDayMonth, $endDayMonth])
            ->where("teacher_id", "=", $request->teacherId)
            ->get();

        $events = TeacherScheduleData::collection($events);

        return response()->json([
            "result" => "RESULT_OK",
            "events" => $events,
            'error' => null
        ], $this->successStatus);

    }

    public function getEvent(Request $request){

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'rowId' => 'Record',
        ];

        $validator = Validator::make($request->all(), [
            'rowId' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $teacherSchedule = new TeacherSchedule();
        $teacherSchedule = $teacherSchedule
            ->with([
                'learningActivityType', 'module',
                'ability','theme',
                'location', 'teacher.employee'
            ])
            ->where("id", "=", $request->rowId)->first();

        if(!$teacherSchedule){
            return response()->json([
                "result" => "RESULT_ERRR",
                "event" => null,
                'error' => "Cannot found record !"
            ], $this->successStatus);
        }

        $abilities = new Ability();
        $abilities = $abilities->where("module_id", $teacherSchedule->module_id)->get();

        $themes = new Theme();
        $themes = $themes->where("ability_id", "=", $teacherSchedule->ability_id)->get();

        return response()->json([
            "result" => "RESULT_OK",
            "event" => $teacherSchedule,
            "themes" => $themes,
            "abilities" => $abilities,
            'error' => null
        ], $this->successStatus);

    }

    public function updateTeacherActivity(Request $request)
    {

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'Id' => 'Identificator',
            'activityType' => 'Tip activitate',
            'module' => 'Modul',
            'ability' => 'Abilitate',
            'theme' => 'Temă',
            'location' => 'Locație',
            'teacher' => 'Profesor',
            'dateStart' => 'Data începerii activității',
            'dateEnd' => 'Data finalizării activității',
        ];

        $validator = Validator::make($request->all(), [
            'recordId' => ['required'],
            'activityType' => ['required'],
            'module' => ['required'],
            'ability' => ['required'],
            'theme' => ['required'],
            'location' => ['required'],
            'teacher' => ['required'],
            'dateStart' => ['required'],
            'dateEnd' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        if($request->formAction == 'edit'){

            $dates = new Carbon($request->dateStart);
            $datef = new Carbon($request->dateEnd);

            $year = new Year();
            $year = $year->whereCode($dates->year)->first();

            $week = new Week();
            $week = $week->whereCode($dates->week)->first();

            $day = new Day();
            $day = $day->whereCode($dates->day)->first();

            $teacherSchedule = new TeacherSchedule();
            $teacherSchedule = $teacherSchedule->where("id", "=", $request->recordId)->first();

            if(!$teacherSchedule){
                throw new Exception('Missing teacher schedule');
            }

            $teacherSchedule->learning_activity_type_id = $request->activityType["id"];
            $teacherSchedule->year_id = $year->id;
            $teacherSchedule->week_id = $week->id;
            $teacherSchedule->day_id = $day->id;
            $teacherSchedule->module_id = $request->module["id"];
            $teacherSchedule->ability_id = $request->ability["id"];
            $teacherSchedule->theme_id = $request->theme["id"];
            $teacherSchedule->location_id = $request->location["id"];
            $teacherSchedule->teacher_id = $request->teacher["id"];
            $teacherSchedule->date_start = $dates->format("Y-m-d H:i:s");
            $teacherSchedule->date_finish = $datef->format("Y-m-d H:i:s");
            $teacherSchedule->total_minutes =$datef->diffInMinutes($dates);
            $teacherSchedule->save();
        }

        return to_route('teacherSchedule');
    }

    public function deleteTeacherActivity(Request $request)
    {

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'Id' => 'Identificator',
        ];

        $validator = Validator::make($request->all(), [
            'recordId' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        if($request->formAction == 'delete'){

            $teacherSchedule = new TeacherSchedule();
            $teacherSchedule = $teacherSchedule->where("id", "=", $request->recordId)->first();

            if(!$teacherSchedule){
                throw new Exception('Missing teacher schedule');
            }

            $teacherSchedule->delete();
        }

        return to_route('teacherSchedule');
    }
}
