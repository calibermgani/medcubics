<?php

namespace App\Imports;

use App\User;
use App\Models\Cpt;
use App\Models\Favouritecpts;
use App\Models\MultiFeeschedule;
use Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Schema;
use Validator;
use Redirect;
use DB;
class FeescheduleImport implements ToModel
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
