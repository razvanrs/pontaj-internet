<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Models\Employee;

class OreRecuperareController extends Controller
{

   public function index()
	{

        $employees = Employee::get();
         $persons = [
            [ "id" => 1, "name" => "Persoană 1" ],
            [ "id" => 2, "name" => "Persoană 2" ],
            [ "id" => 3, "name" => "Persoană 3" ],
            [ "id" => 4, "name" => "Persoană 4" ],
            [ "id" => 5, "name" => "Persoană 5" ],
        ];

         $hourTypes = [
            [ "id" => 1, "name" => "Tip oră 1" ],
            [ "id" => 2, "name" => "Tip oră 2" ],
            [ "id" => 3, "name" => "Tip oră 3" ],
            [ "id" => 4, "name" => "Tip oră 4" ],
            [ "id" => 5, "name" => "Tip oră 5" ],
        ];

        return Inertia::render('OreRecuperare')
        ->with([
            "employees" => $employees,
            "persons" => $persons,  
            "hourTypes" => $hourTypes,  
        ]);
	}

    public function addRecovery(Request $request)
    {

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'dateStart' => 'Data începerii recuperării',
            'dateEnd' => 'Data finalizării recuperării',
        ];

        $validator = Validator::make($request->all(), [
            'dateStart' => ['required'],
            'dateEnd' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        return to_route('ore-recuperare');
    }
}
