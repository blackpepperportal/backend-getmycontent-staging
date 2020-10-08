@extends('layouts.admin') 

@section('title', tr('view_sub_categories')) 

@section('content-header', tr('sub_categories')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="{{route('admin.sub_categories.index')}}">{{tr('sub_categories')}}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_sub_categories') }}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_sub_categories') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.sub_categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_sub_category') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data" id="myTable">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('category') }}</th>
                                    <th>{{ tr('picture') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($sub_categories as $i => $sub_category_details)
                                <tr>
                                    <td>{{ $i+$sub_categories->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.sub_categories.view' , ['sub_category_id' => $sub_category_details->id] )  }}">
                                        {{ $sub_category_details->name }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.categories.view' , ['category_id' => $sub_category_details->category_id] )  }}">
                                        {{ $sub_category_details->categoryDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td><img src="{{$sub_category_details->picture}}" class="category-image"></td>4
                                    
                                    <td>
                                        @if($sub_category_details->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.sub_categories.view', ['sub_category_id' => $sub_category_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.sub_categories.edit', ['sub_category_id' => $sub_category_details->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('sub_category_delete_confirmation' , $sub_category_details->name) }}&quot;);" href="{{ route('admin.sub_categories.delete', ['sub_category_id' => $sub_category_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($sub_category_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.sub_categories.status' , ['sub_category_id' => $sub_category_details->id] )  }}" onclick="return confirm(&quot;{{ $sub_category_details->name }} - {{ tr('sub_category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.sub_categories.status' , ['sub_category_id' => $sub_category_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $sub_categories->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection