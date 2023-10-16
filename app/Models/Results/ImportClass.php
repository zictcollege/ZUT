<?php

namespace App\Models\Results;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Results; // Replace with your actual model

class ImportClass implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Define the mapping of columns from the imported file to your model attributes
        // Adjust the column names as needed
        $importList = new ImportList([
            'column1' => $row['academicPeriodID'],
            'column2' => $row['programID'],
            'column3'=> $row['studentID'],
            'column4'=> $row['code'],
            'column5'=> $row['title'],
            'column6'=> $row['key'],
            'column7'=> $row['status'],
            'column8'=> $row['published'],
            'column9'=> $row['notifiedStudent'],
            'column10'=> $row['processed_by'],
            // Add more columns as necessary
        ]);
       // $importList->save();

        // Return the created model instance
        return $importList;
    }
}
