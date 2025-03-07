<?php

namespace Tests\Unit\Services;

use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\ExtraHour;
use App\Services\ExtraHourService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ExtraHourServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $employee;

    public function setUp(): void
    {
        parent::setUp();

        // Create service instance
        $this->service = new ExtraHourService();

        // Create test employee
        $this->employee = Employee::factory()->create();
    }

    /** @test */
    public function it_calculates_extra_hours_on_weekdays()
    {
        // Monday (1) to Tuesday (2)
        $monday = Carbon::now()->startOfWeek();

        $schedule = EmployeeSchedule::factory()->create([
            'employee_id' => $this->employee->id,
            'schedule_status_id' => 1, // Completed
            'date_start' => $monday->copy()->setHour(6)->setMinute(0), // 6 AM Monday
            'date_finish' => $monday->copy()->setHour(18)->setMinute(0), // 6 PM Monday
        ]);

        $extraHours = $this->service->calculateExtraHours($schedule);

        // Should create 2 extra hour entries: 6-8 AM and 4-6 PM
        $this->assertCount(2, $extraHours);

        // Verify morning extra hours
        $this->assertEquals(
            $monday->copy()->setHour(6)->format('Y-m-d H:i'),
            $extraHours[0]->start_time->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $monday->copy()->setHour(8)->format('Y-m-d H:i'),
            $extraHours[0]->end_time->format('Y-m-d H:i')
        );

        // Verify evening extra hours
        $this->assertEquals(
            $monday->copy()->setHour(16)->format('Y-m-d H:i'),
            $extraHours[1]->start_time->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $monday->copy()->setHour(18)->format('Y-m-d H:i'),
            $extraHours[1]->end_time->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function it_calculates_extra_hours_overnight_shift()
    {
        // Monday (1) to Tuesday (2)
        $monday = Carbon::now()->startOfWeek();

        $schedule = EmployeeSchedule::factory()->create([
            'employee_id' => $this->employee->id,
            'schedule_status_id' => 1, // Completed
            'date_start' => $monday->copy()->setHour(20)->setMinute(0), // 8 PM Monday
            'date_finish' => $monday->copy()->addDay()->setHour(8)->setMinute(0), // 8 AM Tuesday
        ]);

        $extraHours = $this->service->calculateExtraHours($schedule);

        // Should create multiple extra hour entries spanning the night shift
        $this->assertGreaterThanOrEqual(2, count($extraHours));

        // First segment should start at 8 PM Monday
        $this->assertEquals(
            $monday->copy()->setHour(20)->format('Y-m-d H:i'),
            $extraHours[0]->start_time->format('Y-m-d H:i')
        );

        // Last segment should end at 8 AM Tuesday
        $lastIndex = count($extraHours) - 1;
        $this->assertEquals(
            $monday->copy()->addDay()->setHour(8)->format('Y-m-d H:i'),
            $extraHours[$lastIndex]->end_time->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function it_calculates_extra_hours_on_weekends()
    {
        // Saturday
        $saturday = Carbon::now()->startOfWeek()->addDays(5);

        $schedule = EmployeeSchedule::factory()->create([
            'employee_id' => $this->employee->id,
            'schedule_status_id' => 1, // Completed
            'date_start' => $saturday->copy()->setHour(9)->setMinute(0), // 9 AM Saturday
            'date_finish' => $saturday->copy()->setHour(17)->setMinute(0), // 5 PM Saturday
        ]);

        $extraHours = $this->service->calculateExtraHours($schedule);

        // Should create extra hour entries covering the entire shift (depending on segment size)
        $this->assertGreaterThanOrEqual(1, count($extraHours));

        // First segment should start at 9 AM Saturday
        $this->assertEquals(
            $saturday->copy()->setHour(9)->format('Y-m-d H:i'),
            $extraHours[0]->start_time->format('Y-m-d H:i')
        );

        // Last segment should end at 5 PM Saturday
        $lastIndex = count($extraHours) - 1;
        $this->assertEquals(
            $saturday->copy()->setHour(17)->format('Y-m-d H:i'),
            $extraHours[$lastIndex]->end_time->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function it_calculates_extra_hours_for_24_hour_shift()
    {
        // Monday (1) to Tuesday (2)
        $monday = Carbon::now()->startOfWeek();

        $schedule = EmployeeSchedule::factory()->create([
            'employee_id' => $this->employee->id,
            'schedule_status_id' => 1, // Completed
            'date_start' => $monday->copy()->setHour(8)->setMinute(0), // 8 AM Monday
            'date_finish' => $monday->copy()->addDay()->setHour(8)->setMinute(0), // 8 AM Tuesday
        ]);

        $extraHours = $this->service->calculateExtraHours($schedule);

        // Should create extra hour entries for evening and morning hours
        $this->assertGreaterThanOrEqual(2, count($extraHours));

        // Verify that the evening extra hours start at 4 PM (16:00)
        $foundEveningStart = false;
        foreach ($extraHours as $hours) {
            if ($hours->start_time->format('H:i') === '16:00' && $hours->start_time->dayOfWeek === 1) {
                $foundEveningStart = true;
                break;
            }
        }
        $this->assertTrue($foundEveningStart, "Evening extra hours should start at 4 PM");
    }

    /** @test */
    public function it_calculates_extra_hours_with_custom_config()
    {
        // Temporarily modify config
        Config::set('extra_hours.regular_hours.start', 9); // 9 AM
        Config::set('extra_hours.regular_hours.end', 17);  // 5 PM

        // Monday
        $monday = Carbon::now()->startOfWeek();

        $schedule = EmployeeSchedule::factory()->create([
            'employee_id' => $this->employee->id,
            'schedule_status_id' => 1, // Completed
            'date_start' => $monday->copy()->setHour(7)->setMinute(0), // 7 AM Monday
            'date_finish' => $monday->copy()->setHour(19)->setMinute(0), // 7 PM Monday
        ]);

        $extraHours = $this->service->calculateExtraHours($schedule);

        // Should create 2 extra hour entries: 7-9 AM and 5-7 PM
        $this->assertCount(2, $extraHours);

        // Verify morning extra hours with custom config
        $this->assertEquals(
            $monday->copy()->setHour(7)->format('Y-m-d H:i'),
            $extraHours[0]->start_time->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $monday->copy()->setHour(9)->format('Y-m-d H:i'),
            $extraHours[0]->end_time->format('Y-m-d H:i')
        );

        // Verify evening extra hours with custom config
        $this->assertEquals(
            $monday->copy()->setHour(17)->format('Y-m-d H:i'),
            $extraHours[1]->start_time->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $monday->copy()->setHour(19)->format('Y-m-d H:i'),
            $extraHours[1]->end_time->format('Y-m-d H:i')
        );
    }
}
