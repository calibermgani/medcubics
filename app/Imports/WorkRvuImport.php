<?php

namespace App\Imports;

use App\Cpt;
use Maatwebsite\Excel\Concerns\ToModel;

class WorkRvuImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
    }
     public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
