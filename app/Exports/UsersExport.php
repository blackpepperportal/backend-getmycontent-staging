<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
  
class UsersExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->status = $request->status;
        $this->account_type = $request->account_type;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = \App\User::orderBy('created_at','desc');

            if($this->search_key) {

                $base_query = $base_query
                ->where('users.name','LIKE','%'.$this->search_key.'%')
                ->orWhere('users.email','LIKE','%'.$this->search_key.'%')
                ->orWhere('users.mobile','LIKE','%'.$this->search_key.'%');
            }

            if($this->status) {

                $status = $this->status;

                switch ($status) {

                    case SORT_BY_APPROVED:
                    $base_query = $base_query->where('users.status', USER_APPROVED);
                    break;

                    case SORT_BY_DECLINED:
                    $base_query = $base_query->where('users.status', USER_DECLINED);
                    break;

                    case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('users.is_email_verified',USER_EMAIL_VERIFIED);
                    break;

                    case SORT_BY_DOCUMENT_VERIFIED:

                    $base_query =  $base_query->whereHas('userDocuments', function($q) use ($status) {
                        return $q->where('user_documents.is_verified',USER_DOCUMENT_VERIFIED);
                    });
                    break;

                    default:
                    $base_query = $base_query->where('users.is_email_verified',USER_EMAIL_NOT_VERIFIED);
                    break;
                }
            }

            if($this->account_type != '') {

                $base_query = $base_query->where('users.user_account_type', $this->account_type);

            } 
                $base_query = $base_query->get();

    
     return view('exports.users', [
            'data' => $base_query
        ]);


    }

}