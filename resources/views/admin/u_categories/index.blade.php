@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('u_category') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('view_ucategory')}}</li>

@endsection

@section('content')

<section id="configuration">
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{$title ?? tr('view_ucategory')}}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">

                    <a href="{{ route('admin.u_categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_ucategory') }}</a>


                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        <table class="table table-striped table-bordered sourced-data ">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('total_users') }}</th>
                                    <th>{{ tr('description') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($u_categories as $i => $u_category)

                                <tr>

                                    <td>{{ $i+$u_categories->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $u_category->id])}}" class="custom-a">
                                            {{$u_category->name}}
                                        </a>

                                    </td>

                                    <td>
                                        {!! $u_category->total_users !!}
                                    </td>


                                    <td>
                                        {!! $u_category->description !!}
                                    </td>

                                    <td>
                                        @if($u_category->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>

                                        @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>

                                        @endif
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.u_categories.view', ['u_category_id' => $u_category->id] ) }}">&nbsp;{{ tr('view') }}</a>


                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.u_categories.edit', ['u_category_id' => $u_category->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('u_category_delete_confirmation' , $u_category->name) }}&quot;);" href="{{ route('admin.u_categories.delete', ['u_category_id' => $u_category->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($u_category->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.u_categories.status' , ['u_category_id' => $u_category->id] )  }}" onclick="return confirm(&quot;{{ $u_category->name }} - {{ tr('u_category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.u_categories.status' , ['u_category_id' => $u_category->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif




                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $u_categories->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection