<?php

namespace App\Exports;

use App\Models\history;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class HistoriesExport implements FromCollection,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return history::all();
    }
    public function map($history): array
    {
        return [
            $history->id,
            $history->major_name,
            $history->student->name,
            $history->bill_name,
            $history->price,
            $history->status,
            $history->history_time,
            $history->created_at->format('Y-m-d'),
        ];
    }
}
