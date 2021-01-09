@extends('layouts.admin')

@section('title', tr('u_categories'))

@section('content-header', tr('u_categories'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('u_categories') }}</a>
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

                       @if($u_categories->count() >= 1)
                        <a class="btn btn-primary  dropdown-toggle  bulk-action-dropdown" href="#" id="dropdownMenuOutlineButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-plus"></i> {{tr('bulk_action')}}
                        </a>
                       @endif

                        <a href="{{ route('admin.u_categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_ucategory') }}</a>

                        <div class="dropdown-menu float-right" aria-labelledby="dropdownMenuOutlineButton2">

                                <a class="dropdown-item action_list" href="#" id="bulk_delete">
                                    {{tr('delete')}}
                                </a>

                                <a class="dropdown-item action_list" href="#" id="bulk_approve">
                                    {{ tr('approve') }}
                                </a>

                                <a class="dropdown-item action_list" href="#" id="bulk_decline">
                                    {{ tr('decline') }}
                                </a>
                                </div>

                                <div class="bulk_action">

                                  <form action="{{route('admin.u_categories.bulk_action')}}" id="user_category_form" method="POST" role="search">

                                    @csrf

                                    <input type="hidden" name="action_name" id="action" value="">

                                    <input type="hidden" name="selected_categories" id="selected_ids" value="">

                                    <input type="hidden" name="page_id" id="page_id" value="{{ (request()->page) ? request()->page : '1' }}">

                                   </form>

                                </div>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        <table class="table table-striped table-bordered sourced-data ">

                            <thead>
                                <tr>
                                    @if($u_categories->count() >= 1)
                                    <th>
                                        <input id="check_all" type="checkbox" class="chk-box-left">
                                    </th>
                                    @endif
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

                                   <td id="check{{$u_category->id}}"><input type="checkbox" name="row_check" class="faChkRnd chk-box-inner-left" id="{{$u_category->id}}" value="{{$u_category->id}}"></td>


                                    <td>{{ $i+$u_categories->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.u_categories.view' , ['u_category_id' => $u_category->id])}}" class="custom-a">
                                            {{$u_category->name}}
                                        </a>

                                    </td>

                                    <td>
                                        <a href="{{ route('admin.u_categories.view', ['u_category_id' => $u_category->id] ) }}">
                                        {{ $u_category->total_users }}
                                        </a>
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


@section('scripts')

@if(Session::has('bulk_action'))
<script type="text/javascript">
    $(document).ready(function() {
        localStorage.clear();
    });
</script>
@endif

<script type="text/javascript">
    $(document).ready(function() {
        get_values();

        // Call to Action for Delete || Approve || Decline
        $('.action_list').click(function() {
            var selected_action = $(this).attr('id');
            if (selected_action != undefined) {
                $('#action').val(selected_action);
               
                if ($("#selected_ids").val() != "") {
                    if (selected_action == 'bulk_delete') {
                        var message = "{{ tr('admin_u_categories_delete_confirmation') }}";
                    } else if (selected_action == 'bulk_approve') {
                        var message = "{{ tr('u_category_approve_confirmation') }}";
                    } else if (selected_action == 'bulk_decline') {
                        var message = "{{ tr('u_category_decline_confirmation') }}";
                    }
                    var confirm_action = confirm(message);

                    if (confirm_action == true) {
                        $("#user_category_form").submit();
                    }
                    // 
                } else {
                    alert('Please select the check box');
                }
            }
        });
        // single check
        var page = $('#page_id').val();
        $('.faChkRnd:checkbox[name=row_check]').on('change', function() {

            var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                    return this.id;
                })
                .get();

            localStorage.setItem("u_category_checked_items" + page, JSON.stringify(checked_ids));

            get_values();

        });
        // select all checkbox
        $("#check_all").on("click", function() {
            if ($("input:checkbox").prop("checked")) {
                $("input:checkbox[name='row_check']").prop("checked", true);
                var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                        return this.id;
                    })
                    .get();
                console.log("u_category_checked_items" + page);

                localStorage.setItem("u_category_checked_items" + page, JSON.stringify(checked_ids));
                get_values();
            } else {
                $("input:checkbox[name='row_check']").prop("checked", false);
                localStorage.removeItem("u_category_checked_items" + page);
                get_values();
            }

        });

        // Get Id values for selected User
        function get_values() {
            var pageKeys = Object.keys(localStorage).filter(key => key.indexOf('u_category_checked_items') === 0);
            var values = Array.prototype.concat.apply([], pageKeys.map(key => JSON.parse(localStorage[key])));

            if (values) {
                $('#selected_ids').val(values);
            }

            for (var i = 0; i < values.length; i++) {
                $('#' + values[i]).prop("checked", true);
            }
        }



    });

  
    

</script>

@endsection