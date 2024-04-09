<?php

namespace App\Exports;

use App\Models\major;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class MajorsExport implements FromCollection,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return major::all();
    }
    public function map($major): array
    {
        return [
            $major->id,
            $major->major_name,
        ];
    }
}
