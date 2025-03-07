<?php

namespace App\Http\Controllers\Rapoarte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RaportPlanificareProfesoriController extends Controller
{

    public function index()
	{

        $compartments = [
            [ "id" => 1, "name" => "BCI" ],
            [ "id" => 2, "name" => "SMPB" ],
            [ "id" => 3, "name" => "SMRU" ],
            [ "id" => 4, "name" => "SSULSI" ],
            [ "id" => 5, "name" => "BEH" ],
        ];

        $teachers = [
            [ "id" => 1, "name" => "Profesor 1" ],
            [ "id" => 2, "name" => "Profesor 2" ],
            [ "id" => 3, "name" => "Profesor 3" ],
            [ "id" => 4, "name" => "Profesor 4" ],
            [ "id" => 5, "name" => "Profesor 5" ],
        ];

        return Inertia::render('Rapoarte/RaportPlanificareProfesori')
        ->with([
            "compartments" => $compartments,
            "teachers" => $teachers, 
        ]);
	}
}
