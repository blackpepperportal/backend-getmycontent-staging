@extends('layouts.admin') 

@section('content-header', tr('categories')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.categories.index')}}">{{tr('categories')}}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_categories') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_categories') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_category') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">
                        
                        <table class="table table-striped table-bordered sourced-data" id="myTable">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('picture') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($categories as $i => $category)
                                <tr>
                                    <td>{{ $i+$categories->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.categories.view' , ['category_id' => $category->id] )  }}">
                                        {{ $category->name }}
                                        </a>
                                    </td>

                                    <td><img src="{{$category->picture}}" class="category-image"></td>4
                                    
                                    <td>
                                        @if($category->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.categories.view', ['category_id' => $category->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.categories.edit', ['category_id' => $category->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('category_delete_confirmation' , $category->name) }}&quot;);" href="{{ route('admin.categories.delete', ['category_id' => $category->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($category->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.categories.status' , ['category_id' => $category->id] )  }}" onclick="return confirm(&quot;{{ $category->name }} - {{ tr('category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.categories.status' , ['category_id' => $category->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $categories->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
