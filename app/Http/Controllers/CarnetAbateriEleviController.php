<?php

namespace App\Http\Controllers;

use App\Enums\MilitaryRankTypeEnum;
use App\Http\Requests\StoreStudentSanctionRequest;
use App\Http\Requests\UpdateStudentSanctionRequest;
use App\Models\Employee;
use App\Models\Sanction;
use App\Models\Student;
use App\Models\StudentSanction;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\StudentSanctionInformFormTeacherNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CarnetAbateriEleviController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentSanction::query()
            ->select(
                'student_sanction.id',
                'student_sanction.date as student_sanction_date',
                'students.id as student_id',
                'students.full_name as student_full_name',
                'users.id as officer_id',
                'users.name as officer_name',
                'sanctions.long_description as sanction_long_description',
                'sanctions.short_description as sanction_short_description',
                'student_classes.name as student_class_name'
            )
            ->orderByDesc('student_sanction.id')
            ->join('students', 'students.id', '=', 'student_sanction.student_id', 'left')
            ->join('users', 'users.id', '=', 'student_sanction.user_id', 'left')
            ->join('sanctions', 'sanctions.id', '=', 'student_sanction.sanction_id', 'left')
            ->join('student_classes', 'student_classes.id', '=', 'students.student_class_id');

        // Aplică filtrele trimise de la frontend
        if ($filters = $request->input('filters')) {
            foreach ($filters as $field => $filter) {
                if (!empty($filter['value'])) {
                    if ($field === 'global') {
                        $query->where(function ($q) use ($filter) {
                            $q->where('students.full_name', 'like', '%' . $filter['value'] . '%')
                                ->orWhere('student_classes.name', 'like', '%' . $filter['value'] . '%')
                                ->orWhere('sanctions.name', 'like', '%' . $filter['value'] . '%')
                                ->orWhere('users.name', 'like', '%' . $filter['value'] . '%');
                        });
                    } else {
                        $columnName = "";
                        switch ($field) {
                            case 'id':
                                $columnName = 'id';
                                break;
                            case 'student_id':
                                $columnName = 'students.id';
                                break;
                            case 'student_full_name':
                                $columnName = 'students.full_name';
                                break;
                            case 'officer_id':
                                $columnName = 'users.id';
                                break;
                            case 'officer_name':
                                $columnName = 'users.name';
                                break;
                            case 'sanction_long_description':
                                $columnName = 'sanctions.long_description';
                                break;
                            case 'sanction_short_description':
                                $columnName = 'sanctions.short_description';
                                break;
                            case 'student_class_name':
                                $columnName = 'student_classes.name';
                                break;
                            case 'student_sanction_date':
                                $columnName = 'student_sanction.date';
                                break;
                        }

                        if ($field === 'student_sanction_date') {
                            $query->whereDate($columnName, Carbon::parse($filter['value'])->format('Y-m-d'));
                        } else {
                            $query->where($columnName, 'like', '%' . $filter['value'] . '%');
                        }
                    }
                }
            }
        }


        $studentSanctions = $query->paginate($request->input('perPage', 10));
        $studentSanctions->transform(function ($item) {
            $item['can'] = [
                'update' => ((auth()->user()->can('updateStudentSanction') && auth()->user()->id === $item->officer_id) || auth()->user()->isSuperAdmin()),
                'delete' => ((auth()->user()->can('deleteStudentSanction') && auth()->user()->id === $item->officer_id) || auth()->user()->isSuperAdmin()),
            ];

            return $item;
        });

        return Inertia::render('CarnetAbateriElevi')
            ->with([
                "can" => [
                    'createUser' => Auth::user()->can('createUser'),
                ],
                "studentSanctions" => $studentSanctions,
                "studentSanction" => Inertia::lazy(fn() => Student::findOrFail($request->input('studentSanctionId'))),
                "filtered" => $request->input('filters') ?? null,
                'sanctions' => Inertia::lazy(fn() => Sanction::orderBy('sel_order')->get()),
            ]);
    }

    public function store(StoreStudentSanctionRequest $request)
    {
        $sanction = Sanction::query()->find($request->validated('sanction'));
        $student = Student::find($request->validated('student')["id"]);
        $pivot = new StudentSanction();

        $pivot->sanction_id = $sanction->id;
        $pivot->student_id = $student->id;
        $pivot->user_id = auth()->id();
        $pivot->date = $request->validated('date');

        $pivot->save();

        $teacher = $student->formTeacher;
        $user = $teacher->employee->user;

        if ($user) {
            $user->notify(new StudentSanctionInformFormTeacherNotification($student, $sanction));
        }

        return redirect()->back()->with('success', 'Sanctiune adaugata cu succes.');
    }


    public function edit(Request $request)
    {
        $studentSanction = StudentSanction::query()
            ->with(['user', 'sanction', 'student'])
            ->findOrFail($request->id);
        return response()->json($studentSanction);
    }

    public function update(StudentSanction $studentSanction, UpdateStudentSanctionRequest $request)
    {
        $studentSanction->student_id = $request->student;
        $studentSanction->sanction_id = $request->sanction;
        $studentSanction->date = $request->date;
        $studentSanction->save();

        return redirect()->back()->with('success', 'Sanctiune actualizata cu succes.');
    }


    public function destroy(StudentSanction $studentSanction)
    {
        $studentSanction->delete();

        return redirect()->back()->with('success', 'Sanctiune stearsa cu succes.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $students = Student::where('full_name', 'LIKE', "%{$query}%")->get();

        return response()->json($students);
    }
}
