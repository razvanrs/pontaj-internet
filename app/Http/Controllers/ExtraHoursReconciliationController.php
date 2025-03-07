<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnitGroup;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExtraHoursReconciliationController extends Controller
{
    /**
     * Display the extra hours reconciliation page
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $employees = Employee::orderBy('full_name')->get();
        $businessUnitGroups = BusinessUnitGroup::orderBy('sel_order')->get();

        return Inertia::render('OreRecuperare', [
            'employees' => $employees,
            'businessUnitGroups' => $businessUnitGroups,
        ]);
    }
}
