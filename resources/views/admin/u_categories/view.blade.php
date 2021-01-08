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

                    <h4 class="card-title">{{$title ?? tr('view_ucategory')}} - {{$ucategory->name ?? ''}}


                    </h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i>

                    </a>

                    <div class="heading-elements">

                    </div>



                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        <table class="table table-striped table-bordered sourced-data ">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('category') }}</th>
                                    <th>{{ tr('username') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($user_categories as $i => $user)

                                <tr>

                                    <td>{{ $i+$user_categories->firstItem() }}</td>

                                    <td>
                                        {{$user->ucategory->name ?? ''}}

                                    </td>

                                    <td>
                                        {{$user->user->name ?? ''}}
                                    </td>

                                 
                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $user_categories->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection