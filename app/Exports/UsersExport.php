<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
  
class UsersExport implements FromView 
{


    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {
    
     return view('exports.users', [
            'data' => $this->data
        ]);


    }

}