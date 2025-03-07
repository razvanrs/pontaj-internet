<?php

namespace App\Console\Commands;

use App\Models\Day;
use App\Models\Month;
use App\Models\Week;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTimelineData extends Command
{
     /**
      * The name and signature of the console command.
      *
      * @var string
      */
     protected $signature = 'app:generate-timeline-data';

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Generate calendar schedule';

     /**
      * Execute the console command.
      */
     public function handle()
     {

        setlocale(LC_TIME, 'ro_RO');

          DB::table('years')->truncate();
          DB::table('weeks')->truncate();
          DB::table('days')->truncate();

          $current = Carbon::now();
          $current = $current->firstOfYear();

          $year = $current->year;
          $month = $current->month;
          $wk = $current->weekOfYear;
          $day = $current->day;

          $lastDate = Carbon::createFromDate('2035-01-01');

          while ($current < $lastDate) {

               $month = $current->month;
               $wk = $current->weekOfYear;
               $day = $current->day;

               $yearChk = new Year();
               $yearChk = $yearChk->whereValue($year)->first();

               if (!$yearChk) {
                    $yearSv = new Year();
                    $yearSv->code = $year;
                    $yearSv->value = $year;
                    $yearSv->sel_order = 1;
                    $yearSv->save();
               }

               $monthChk = new Month();
               $monthChk = $monthChk->whereValue($month)->first();

               if (!$monthChk) {
                    $monthSv = new Month();
                    $monthSv->year_id = $yearChk ? $yearChk->id : $yearSv->id;
                    $monthSv->code = $month;
                    $monthSv->value = $month;
                    $monthSv->name = $current->englishMonth;
                    $monthSv->lokalize_short_name = ucfirst($current->locale('ro')->shortMonthName);
                    $monthSv->lokalize_long_name = ucfirst($current->locale('ro')->monthName);
                    $monthSv->save();
               }


               $wkChk = new Week();
               $wkChk = $wkChk->whereValue($wk)->first();

               if (!$wkChk) {
                    $wkSv = new Week();
                    $wkSv->year_id = $yearChk ? $yearChk->id : $yearSv->id;
                    $wkSv->month_id = $monthChk ? $monthChk->id : $monthSv->id;
                    $wkSv->code = $wk;
                    $wkSv->value = $wk;
                    $wkSv->save();
               }

               $daySv = new Day();
               $daySv->year_id = $yearChk ? $yearChk->id : $yearSv->id;
               $daySv->week_id = $wkChk ? $wkChk->id : $wkSv->id;
               $daySv->month_id = $monthChk ? $monthChk->id : $monthSv->id;
               $daySv->code = $day;
               $daySv->value = $day;
               $daySv->day_of_year = $current->dayOfYear;
               $daySv->day_of_week = $current->dayOfWeek;
               $daySv->lokalize_short_name = ucfirst($current->locale('ro')->shortDayName);
               $daySv->lokalize_long_name = ucfirst($current->locale('ro')->dayName);
               $daySv->string_representation = $current->format('Y-m-d');
               $daySv->save();

               $current->addDay();
               $year = $current->year;
          }
     }
}
