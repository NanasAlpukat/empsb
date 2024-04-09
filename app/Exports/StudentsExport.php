<?php

namespace App\Exports;
use App\Models\student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithProperties;

class StudentsExport implements FromCollection,WithMapping  
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return student::all();
    }
    public function map($student): array
    {
        return [
            $student->id,
            $student->nis,
            $student->name,
            $student->user->email,
            $student->major->major_name,
            $student->age,
            $student->date_of_birth,
            $student->class,
            $student->gender,
            $student->no_phone,
            $student->parents_name,
            $student->address,
            // $student->image,
            $student->created_at->format('d-m-y'),
            // Date::dateTimeToExcel($student->created_at),
        ];
    }

 
}
