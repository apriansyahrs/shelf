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
            if ($row[0] !== 'Nama' && !empty($row[0])) { // skip header and empty names
                // Cari atau buat entitas bisnis
                $businessEntity = BusinessEntity::firstOrCreate(
                    ['name' => $row[1]], // Kondisi pencarian
                );

                // Cari atau buat jabatan
                $jobTitle = JobTitle::firstOrCreate(
                    ['title' => $row[2]], // Kondisi pencarian
                );

                // Buat user baru
                User::create([
                    'name' => $row[0],
                    'business_entity_id' => $businessEntity->id,
                    'job_title_id' => $jobTitle->id,
                ]);
            }
        }
    }
}
