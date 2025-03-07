<?php

namespace App\Imports;

use App\Helpers\StrHelper;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $birthday = $row['birthday'];
        if (is_numeric($birthday)) {
            $birthday = Date::excelToDateTimeObject($birthday)->format('Y-m-d');
        }

        try {
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
            $email = "{$emailName}.{$firstSurname}@sapvlc.internet";

            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'password' => bcrypt('Scoala2024!'),
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }

        $phoneNumbers = [
            'fix' => isset($row['phone_fix']) ? Str::of($row['phone_fix'])->trim() : '',
            'int' => isset($row['phone_int']) ? Str::of($row['phone_int'])->trim() : '',
            'mobile' => [
                "mobile_1" => isset($row['phone_mobile1']) ? Str::of($row['phone_mobile1'])->trim() : '',
                "mobile_2" => isset($row['phone_mobile2']) ? Str::of($row['phone_mobile2'])->trim() : '',
                "mobile_3" => isset($row['phone_mobile3']) ? Str::of($row['phone_mobile3'])->trim() : '',
            ],
        ];

        try {
            $employee = new Employee([
                'user_id' => $user->id,
                'erp_id' => $row["erp_id"],
                'social_number' => $row["social_number"],
                'name' => $name,
                'surname' => $surname,
                'full_name' => $fullName,
                'phone_numbers' => $phoneNumbers,
                'military_rank_id' => $row['military_rank_id'],
                'military_rank_type_id' => $row['military_rank_type_id'],
                'birthday' => $birthday,
                'sex_id' => $row['sex_id'],
                'father_surname' => $fatherSurname,
                'address' => $address,
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }

        return $employee;
    }
}
