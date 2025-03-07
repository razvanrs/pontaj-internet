<?php

namespace App\Imports;

use App\Helpers\StrHelper;
use App\Models\Employee;
use App\Models\User;
use App\Models\MilitaryRank;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeSyncImport implements ToModel, WithHeadingRow
{
    /**
     * Map of common variations to standardized military ranks
     */
    protected $militaryRankMap = null;

    /**
     * Tracking counters for import stats
     */
    protected $created = 0;
    protected $updated = 0;
    protected $newEmployees = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            $birthday = $row['birthday'];
            if (is_numeric($birthday)) {
                $birthday = Date::excelToDateTimeObject($birthday)->format('Y-m-d');
            }

            // Format names properly with preserved capitalization
            $name = StrHelper::formatName($row['name']);
            $surname = StrHelper::formatName($row['surname']);
            $fullName = StrHelper::formatName($row['full_name']);
            $fatherSurname = StrHelper::formatName($row['father_surname']);
            $address = StrHelper::formatAddress($row['address']);

            // Create email-friendly versions for email generation
            $emailName = StrHelper::formatForEmail($row['name']);
            $emailSurname = StrHelper::formatForEmail($row['surname']);

            // Get first part of surname for email
            preg_match('/^[^\s.-]+/', $emailSurname, $matches);
            $firstSurname = $matches[0] ?? $emailSurname;

            // Generate email
            $email = "{$emailName}.{$firstSurname}@angajat.sapvlc";

            $phoneNumbers = [
                'fix' => isset($row['phone_fix']) ? Str::of($row['phone_fix'])->trim() : '',
                'int' => isset($row['phone_int']) ? Str::of($row['phone_int'])->trim() : '',
                'mobile' => [
                    "mobile_1" => isset($row['phone_mobile1']) ? Str::of($row['phone_mobile1'])->trim() : '',
                    "mobile_2" => isset($row['phone_mobile2']) ? Str::of($row['phone_mobile2'])->trim() : '',
                    "mobile_3" => isset($row['phone_mobile3']) ? Str::of($row['phone_mobile3'])->trim() : '',
                ],
            ];

            // Check if the employee already exists
            $existingEmployee = Employee::where('erp_id', $row['erp_id'])->first();
            $existingEmployeeBySocialNumber = Employee::where('social_number', $row['social_number'])->first();

            if ($existingEmployee || $existingEmployeeBySocialNumber) {

                $employeeToUpdate = $existingEmployee ?: $existingEmployeeBySocialNumber;

                // Update existing employee
                $employeeToUpdate->update([
                    'military_rank_id' => $row['military_rank_id'],
                    'military_rank_type_id' => $row['military_rank_type_id'],
                    'name' => $name,
                    'surname' => $surname,
                    'full_name' => $fullName,
                    'social_number' => $row['social_number'],
                    'birthday' => $birthday,
                    'sex_id' => $row['sex_id'],
                    'father_surname' => $fatherSurname,
                    'address' => $address,
                    'phone_numbers' => $phoneNumbers,
                ]);

                // Count as updated
                $this->updated++;

                // Update the associated user if needed
                if ($employeeToUpdate->user) {
                    $employeeToUpdate->user->update([
                        'name' => $fullName,
                    ]);
                } else {
                    // Check if user already exists with this email
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser) {
                        // Use the existing user
                        $user = $existingUser;
                    } else {
                        // Create a new user only if one doesn't exist
                        $user = User::create([
                            'name' => $fullName,
                            'email' => $email,
                            'password' => bcrypt('Scoala2025!'),
                        ]);

                        Log::info('User created:', ['id' => $user->id, 'email' => $email]);
                    }

                    $employeeToUpdate->user()->associate($user);
                    $employeeToUpdate->save();
                }

                DB::commit();
                return $employeeToUpdate;
            } else {
                // Check if user already exists with this email
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Use the existing user
                    $user = $existingUser;
                } else {
                    // Create a new user only if one doesn't exist
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => bcrypt('Scoala2024!'),
                    ]);

                    Log::info('User created:', ['id' => $user->id, 'email' => $email]);
                }

                // Create new employee
                $employee = new Employee([
                    'user_id' => $user->id,
                    'erp_id' => $row['erp_id'],
                    'military_rank_id' => $row['military_rank_id'],
                    'military_rank_type_id' => $row['military_rank_type_id'],
                    'name' => $name,
                    'surname' => $surname,
                    'full_name' => $fullName,
                    'social_number' => $row['social_number'],
                    'birthday' => $birthday,
                    'sex_id' => $row['sex_id'],
                    'father_surname' => $fatherSurname,
                    'address' => $address,
                    'phone_numbers' => $phoneNumbers,
                ]);

                $employee->save();

                // Count as created and store employee details
                $this->created++;
                $this->newEmployees[] = [
                    'id' => $employee->id,
                    'erp_id' => $employee->erp_id,
                    'full_name' => $employee->full_name,
                    'email' => $user->email
                ];

                DB::commit();
                return $employee;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Employee sync import error: ' . $th->getMessage(), [
                'erp_id' => $row['erp_id'] ?? 'unknown',
                'name' => $row['name'] ?? 'unknown',
                'surname' => $row['surname'] ?? 'unknown',
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
            'newEmployees' => $this->newEmployees
        ];
    }
}
