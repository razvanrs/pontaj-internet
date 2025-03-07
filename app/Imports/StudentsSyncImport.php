<?php

namespace App\Imports;

use App\Helpers\StrHelper;
use App\Models\SchoolingPeriod;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsSyncImport implements ToModel, WithHeadingRow
{
    /**
     * Tracking counters for import stats
     */
    protected $created = 0;
    protected $updated = 0;
    protected $newStudents = [];

    public function model(array $row)
    {
        $birthday = $row['birthday'];
        if (is_numeric($birthday)) {
            $birthday = Date::excelToDateTimeObject($birthday)->format('Y-m-d');
        }

        try {
            DB::beginTransaction();

            // Format names properly
            $firstName = StrHelper::formatName($row['first_name'] ?? '');
            $lastName = StrHelper::formatName($row['last_name'] ?? '');
            $fullName = StrHelper::formatName($firstName . ' ' . $lastName);
            $fatherFirstName = StrHelper::formatName($row['father_first_name'] ?? '');
            $motherFirstName = StrHelper::formatName($row['mother_first_name'] ?? '');
            $address = StrHelper::formatAddress($row['address'] ?? '');
            $residenceTown = StrHelper::formatName($row['residence_town'] ?? '');
            $practiceTown = StrHelper::formatName($row['practice_town'] ?? '');

            // Generate email for the student if needed
            $emailFirstName = StrHelper::formatForEmail($firstName);
            $emailLastName = StrHelper::formatForEmail($lastName);

            // Get first part of lastname for email
            preg_match('/^[^\s.-]+/', $emailLastName, $matches);
            $firstLastName = $matches[0] ?? $emailLastName;

            // Generate email
            $email = "{$emailFirstName}.{$firstLastName}@elev.sapvlc";

            // Check if the student already exists
            $existingStudent = Student::where('erp_id', $row['erp_id'])->first();

            if ($existingStudent) {
                // Update existing student
                $existingStudent->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'father_first_name' => $fatherFirstName,
                    'mother_first_name' => $motherFirstName,
                    'erp_student_class_id' => $row['erp_student_class_id'],
                    'student_class_id' => StudentClass::where('erp_id', $row['student_class_id'])->first()->id,
                    'erp_schooling_period_id' => $row['erp_schooling_period_id'] ?? null,
                    'schooling_period_id' => SchoolingPeriod::where('erp_id', $row['erp_schooling_period_id'])->first()->id,
                    'birthday' => $birthday,
                    'birth_county_id' => $row['birth_county_id'] ?? null,
                    'birth_town' => $row['birth_town'] ?? null,
                    'domicile_county_id' => $row['domicile_county_id'] ?? null,
                    'domicile_town' => $row['domicile_town'] ?? null,
                    'selection_county_id' => $row['selection_county_id'] ?? null,
                    'residence_county_id' => $row['residence_county_id'] ?? null,
                    'residence_town' => $residenceTown,
                    'practice_county_id' => $row['practice_county_id'] ?? null,
                    'practice_town' => $practiceTown,
                    'address' => $address,
                    'matriculation_number' => $row['matriculation_number'] ?? null,
                    'ethnicity_id' => $row['ethnicity_id'] ?? null,
                    'sex_id' => $row['sex_id'] ?? null,
                    'language_id' => $row['language_id'] ?? null,
                    'identity_card_series' => $row['identity_card_series'] ?? null,
                    'identity_card_number' => $row['identity_card_number'] ?? null,
                    'marital_status_id' => $row['marital_status_id'] ?? null,
                    'admission_exam_code' => $row['admission_exam_code'] ?? null,
                    'admission_exam_score' => $row['admission_exam_score'] ?? null,
                    'bac_grades_average' => $row['bac_grades_average'] ?? null,
                    'car_brand' => isset($row['car_brand']) ? StrHelper::formatName($row['car_brand']) : null,
                    'car_registration_number' => isset($row['car_registration_number']) ? Str::upper($row['car_registration_number']) : null,
                ]);

                // Update the associated user if needed
                if ($existingStudent->user) {
                    $existingStudent->user->update([
                        'name' => $fullName,
                    ]);
                } else {
                    // Check if user already exists with this email
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser) {
                        // Use the existing user
                        $user = $existingUser;
                    } else {
                        // Create a new user
                        $user = User::create([
                            'name' => $fullName,
                            'email' => $email,
                            'password' => bcrypt('Student2025!'),
                        ]);

                        Log::info('Student user created:', ['id' => $user->id, 'email' => $email]);
                    }

                    $existingStudent->user()->associate($user);
                    $existingStudent->save();
                }

                // Count as updated
                $this->updated++;

                DB::commit();
                return $existingStudent;
            } else {
                // Check if user already exists with this email
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Use the existing user
                    $user = $existingUser;
                } else {
                    // Create a new user
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => bcrypt('Student2024!'),
                    ]);

                    Log::info('Student user created:', ['id' => $user->id, 'email' => $email]);
                }

                // Create new student
                try {
                    $student = new Student([
                        'user_id' => $user->id,
                        'erp_id' => $row['erp_id'],
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'father_first_name' => $fatherFirstName,
                        'mother_first_name' => $motherFirstName,
                        'erp_student_class_id' => $row['erp_student_class_id'],
                        'student_class_id' => StudentClass::where('erp_id', $row['student_class_id'])->first()->id,
                        'erp_schooling_period_id' => $row['erp_schooling_period_id'] ?? null,
                        'schooling_period_id' => SchoolingPeriod::where('erp_id', $row['erp_schooling_period_id'])->first()->id,
                        'birthday' => $birthday,
                        'birth_county_id' => $row['birth_county_id'] ?? null,
                        'birth_town' => $row['birth_town'] ?? null,
                        'domicile_county_id' => $row['domicile_county_id'] ?? null,
                        'domicile_town' => $row['domicile_town'] ?? null,
                        'selection_county_id' => $row['selection_county_id'] ?? null,
                        'residence_county_id' => $row['residence_county_id'] ?? null,
                        'residence_town' => $residenceTown,
                        'practice_county_id' => $row['practice_county_id'] ?? null,
                        'practice_town' => $practiceTown,
                        'address' => $address,
                        'matriculation_number' => $row['matriculation_number'] ?? null,
                        'ethnicity_id' => $row['ethnicity_id'] ?? null,
                        'sex_id' => $row['sex_id'] ?? null,
                        'language_id' => $row['language_id'] ?? null,
                        'identity_card_series' => $row['identity_card_series'] ?? null,
                        'identity_card_number' => $row['identity_card_number'] ?? null,
                        'marital_status_id' => $row['marital_status_id'] ?? null,
                        'admission_exam_code' => $row['admission_exam_code'] ?? null,
                        'admission_exam_score' => $row['admission_exam_score'] ?? null,
                        'bac_grades_average' => $row['bac_grades_average'] ?? null,
                        'car_brand' => isset($row['car_brand']) ? StrHelper::formatName($row['car_brand']) : null,
                        'car_registration_number' => isset($row['car_registration_number']) ? Str::upper($row['car_registration_number']) : null,
                    ]);

                    $student->save();

                    // Count as created and store student details
                    $this->created++;
                    $this->newStudents[] = [
                        'id' => $student->id,
                        'erp_id' => $student->erp_id,
                        'full_name' => $firstName . ' ' . $lastName,
                        'email' => $user->email
                    ];

                    DB::commit();
                    return $student;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error creating student: ' . $e->getMessage(), [
                        'erp_id' => $row['erp_id'] ?? 'unknown',
                        'first_name' => $firstName ?? 'unknown',
                        'last_name' => $lastName ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e; // Re-throw to be caught by the parent try-catch
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Student sync import error: ' . $th->getMessage(), [
                'erp_id' => $row['erp_id'] ?? 'unknown',
                'first_name' => $row['first_name'] ?? 'unknown',
                'last_name' => $row['last_name'] ?? 'unknown',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            throw $th;
        }
    }

    /**
     * Get import statistics
     *
     * @return array
     */
    public function getImportStats()
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'newStudents' => $this->newStudents
        ];
    }
}
