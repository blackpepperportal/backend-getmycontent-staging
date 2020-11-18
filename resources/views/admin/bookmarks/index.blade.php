@extends('layouts.admin') 

@section('title', tr('bookmarks')) 

@section('content-header', tr('bookmarks')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>
    
<li class="breadcrumb-item">{{ tr('bookmarks') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('bookmarks') }} - <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a></h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.bookmarks._search')
                        
                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('post') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($post_bookmarks as $i => $post_bookmark)

                                <tr>
                                    <td>{{ $i + $post_bookmarks->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_bookmark->user_id] )  }}">
                                        {{ $post_bookmark->username ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.posts.view' , ['post_id' => $post_bookmark->post_id] )  }}">
                                        {{ $post_bookmark->post->content ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-success dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                
                                                  <a class="dropdown-item" href="{{ route('admin.bookmarks.view', ['post_bookmark_id' => $post_bookmark->id, 'user_id' => $post_bookmark->user_id] ) }}">&nbsp;{{ tr('view') }}</a>


                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('bookmark_delete_confirmation' , $post_bookmark->post->content ?? '') }}&quot;);" href="{{ route('admin.bookmarks.delete', ['post_bookmark_id' => $post_bookmark->id, 'user_id' => $post_bookmark->user_id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                            </div>

                                        </div>


                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $post_bookmarks->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection