<?php

namespace App\Imports;

use App\Models\BusinessEntity;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($row[0] !== 'Nama') { // skip header
                $businessEntity = BusinessEntity::where('name', $row[1])->first();
                $jobTitle = JobTitle::where('title', $row[2])->first();

                if (!$businessEntity) {
                    throw ValidationException::withMessages(['business_entity' => "Badan Usaha '{$row[1]}' tidak ditemukan"]);
                }

                if (!$jobTitle) {
                    throw ValidationException::withMessages(['job_title' => "Jabatan '{$row[2]}' tidak ditemukan"]);
                }

                User::create([
                    'name' => $row[0],
                    'business_entity_id' => $businessEntity->id,
                    'job_title_id' => $jobTitle->id,
                ]);
            }
        }
    }
}
