<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class ViewExport implements FromArray {   
    private $data;

    public function __construct($data) {        
        $this->data = $data;
    }

    public function array(): array {     
        return $this->data;
    }    
}