<?php

namespace App\Http\Controllers;

use App\Data\DayLimitData;
use App\Models\DayLimit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ManagementCalendarController extends Controller
{
    public function index()
    {
        // Get current month events only for initial load
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $events = DayLimit::whereBetween('start', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('finish', [$startOfMonth, $endOfMonth])
            ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('start', '<=', $startOfMonth)
                    ->where('finish', '>=', $endOfMonth);
            })
            ->get();

        $dayLimits = DayLimitData::collection($events);

        return Inertia::render('ManagementCalendar')
        ->with([
            'dayLimits' => $dayLimits
        ]);
    }

    public function addEvent(Request $request)
    {
        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'eventId' => 'Record id',
            'formAction' => 'Actiune form',
            'eventName' => 'Nume eveniment',
            'dateStart' => 'Data începere eveniment',
            'dateEnd' => 'Data finalizare eveniment',
        ];

        $validator = Validator::make($request->all(), [
            'formAction' => ['required'],
            'eventId' => ['required'],
            'eventName' => ['required'],
            'dateStart' => ['required'],
            'dateEnd' => ['required'],
        ], $messages, $attributes);

        // BLOCARE MAI MULTE EVENTURI IN ACEEASI ZI
        
        // $validator->after(function ($validator) use ($request){
        //     if($request->formAction == 'add'){
        //         $start = (new Carbon($request->dateStart));
        //         $end = (new Carbon($request->dateEnd));

        //         $events = DayLimit::whereBetween('start', [$start, $end])
        //             ->get();

        //         if($events->count() > 0){
        //             $validator->errors()->add(
        //                 'dateStart', 'Perioada selectată se suprapune peste un eveniment deja introdus !'
        //             );
        //         }
        //     }
        // });

        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        //action to add event
        if($request->formAction == 'add'){
            $dayLimit = new DayLimit();
            $dayLimit->name = $request->eventName;
            $dayLimit->start = (new Carbon($request->dateStart))->format("Y-m-d H:i:s");
            $dayLimit->finish = (new Carbon($request->dateEnd))->format("Y-m-d H:i:s");
            $dayLimit->save();
        }

        //action to edit event
        if($request->formAction == 'edit'){
            $dayLimit = DayLimit::find($request->eventId);
            if($dayLimit){
                $dayLimit->name = $request->eventName;
                $dayLimit->start = (new Carbon($request->dateStart))->format("Y-m-d H:i:s");
                $dayLimit->finish = (new Carbon($request->dateEnd))->format("Y-m-d H:i:s");
                $dayLimit->save();
            }
        }

        //action to delete event
        if($request->formAction == 'delete'){
            $dayLimit = DayLimit::find($request->eventId);
            if($dayLimit){
                $dayLimit->delete();
            }
        }

        return to_route('management-calendar');
    }

    public function getEventsByDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $events = DayLimit::whereBetween('start', [$startDate, $endDate])
            ->orWhereBetween('finish', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start', '<=', $startDate)
                    ->where('finish', '>=', $endDate);
            })
            ->get();

        return DayLimitData::collection($events);
    }
}