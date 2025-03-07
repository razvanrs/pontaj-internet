<?php

namespace App\Imports;

use App\Helpers\StrHelper;
use App\Models\County;
use App\Models\SchoolingPeriod;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;  // Added User model import
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            // Format names properly for display
            $firstName = StrHelper::formatName($row['first_name']);
            $lastName = StrHelper::formatName($row['last_name']);
            $fullName = StrHelper::formatName($firstName . ' ' . $lastName);

            // Generate email for the student
            $emailFirstName = StrHelper::formatForEmail($firstName);
            $emailLastName = StrHelper::formatForEmail($lastName);

            // Get first part of lastname for email
            preg_match('/^[^\s.-]+/', $emailLastName, $matches);
            $firstLastName = $matches[0] ?? $emailLastName;

            // Generate email
            $email = "{$emailFirstName}.{$firstLastName}@elev.sapvlc";

            // Create a user for the student
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                // Use existing user if email already exists
                $user = $existingUser;
            } else {
                // Create new user
                $user = User::create([
                    'name' => $fullName,
                    'email' => $email,
                    'password' => bcrypt('Student2025!'),
                ]);

                Log::info('Student user created:', ['id' => $user->id, 'email' => $email]);
            }

            $birthday = $row['birthday'];
            if (is_numeric($birthday)) {
                $birthday = Date::excelToDateTimeObject($birthday)->format('Y-m-d');
            }

            $student = new Student([
                'user_id' => $user->id, // Associate with the created user
                'erp_id' => $row['erp_id'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'father_first_name' => StrHelper::formatName($row['father_first_name']),
                'mother_first_name' => StrHelper::formatName($row['mother_first_name']),
                'erp_student_class_id' => $row['erp_student_class_id'],
                'student_class_id' => StudentClass::where('erp_id', $row['erp_student_class_id'])->first()->id,
                'erp_schooling_period_id' => $row['erp_schooling_period_id'],
                'schooling_period_id' => SchoolingPeriod::where('erp_id', $row['erp_schooling_period_id'])->first()->id,
                'birthday' => $birthday,
                'birth_county_id' => empty($row['birth_county_id']) ? null : County::where('erp_id', $row['birth_county_id'])->first()->id,
                'birth_town' => $row['birth_town'],
                'domicile_county_id' => County::where('erp_id', $row['domicile_county_id'])->first()->id,
                'domicile_town' => $row['domicile_town'],
                'selection_county_id' => $row['selection_county_id'],
                'residence_county_id' => $row['residence_county_id'] == 0 ? null : County::where('erp_id', $row['residence_county_id'])->first()->id,
                'residence_town' => StrHelper::formatName($row['residence_town']),
                'practice_county_id' => $row['practice_county_id'] == 0 ? null : County::where('erp_id', $row['practice_county_id'])->first()->id,
                'practice_town' => StrHelper::formatName($row['practice_town']),
                'address' => StrHelper::formatAddress($row['address']),
                'matriculation_number' => $row['matriculation_number'],
                'ethnicity_id' => $row['ethnicity_id'],
                'sex_id' => $row['sex_id'],
                'language_id' => $row['language_id'],
                'identity_card_series' => $row['identity_card_series'],
                'identity_card_number' => $row['identity_card_number'],
                'marital_status_id' => $row['marital_status_id'],
                'admission_exam_code' => $row['admission_exam_code'],
                'admission_exam_score' => $row['admission_exam_score'],
                'bac_grades_average' => $row['bac_grades_average'],
                'car_brand' => StrHelper::formatName($row['car_brand']),
                'car_registration_number' => Str::upper($row['car_registration_number']),
                'deleted_at' => empty($row['deleted_at']) ? null : \Carbon\Carbon::parse($row['deleted_at'])->format('Y-m-d H:i:s'),
            ]);

            $student->save();
            DB::commit();
            return $student;

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Student import error at row with erp_id: ' . $row['erp_id'] . ' ' . $th->getMessage(), [
                'erp_id' => $row['erp_id'] ?? 'unknown',
                'name' => ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }
}
