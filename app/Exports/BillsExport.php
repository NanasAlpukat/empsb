<?php

namespace App\Exports;

use App\Models\bill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class BillsExport implements FromCollection,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return bill::all();
    }
    public function map($bill): array
    {
        return [
            $bill->id,
            $bill->bill_name,
        ];
    }
}
