<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Request;
class UsersExport implements FromQuery 
{

 use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function query()
    {
    	$base_query = \App\User::orderBy('created_at','desc');

    	if(Request::get('search_key')) {

    		$base_query = $base_query
    		->where('users.name','LIKE','%'.Request::get('search_key').'%')
    		->orWhere('users.email','LIKE','%'.Request::get('search_key').'%')
    		->orWhere('users.mobile','LIKE','%'.Request::get('search_key').'%');
    	}

    	if(Request::get('status')) {

    		switch (Request::get('status')) {

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

    			$base_query =  $base_query->whereHas('userDocuments', function($q) use ($request) {
    				return $q->where('user_documents.is_verified',USER_DOCUMENT_VERIFIED);
    			});
    			break;

    			default:
    			$base_query = $base_query->where('users.is_email_verified',USER_EMAIL_NOT_VERIFIED);
    			break;
    		}
    	}

    	if(Request::filled('account_type')) {

    		$base_query = $base_query->where('users.user_account_type', Request::get('account_type'));

    	} 

    	return $base_query;
    }

}