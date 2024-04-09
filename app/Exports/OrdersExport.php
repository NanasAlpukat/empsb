<?php

namespace App\Exports;
use App\Models\order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;


class OrdersExport implements FromCollection,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return order::all();
    }
    public function map($order): array
    {
        return [
            $order->id,
            $order->student->name,
            $order->bill->bill_name,
            $order->major->major_name,
            $order->price,
            $order->status,
            $order->expired_date,
        ];
    }
}
