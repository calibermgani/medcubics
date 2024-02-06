<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
//use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BladeExport implements FromView, WithChunkReading {    
    
    private $data;
    private $filePath;

    public function __construct($data,$filePath='') {        
        $this->data = $data;
        $this->filePath = $filePath;
    }

    public function chunkSize(): int {
        return 1000;
    }

    public function view(): View {
        $resp = $this->data;
        if(isset($resp['file_path']) && $resp['file_path'] != '') {            
            $filePath = $this->data['file_path'];
            return view($filePath, [
                'result' => $resp
            ]);
        } else {
            $filePath = $this->filePath;    
            return view($filePath, $resp);
        }
    }
}