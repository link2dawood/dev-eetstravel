<?php
namespace App\Imports;

use App\Tour;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TourImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
dd();
        return new Tour([
            'name' => $row['name'],
            'description' => $row['description'],
            'duration' => $row['duration'],
            'price' => $row['price'],
            // Add more attributes based on your Excel columns
        ]);
    }
}
