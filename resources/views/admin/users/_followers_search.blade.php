<form method="GET" action="{{route('admin.user_followers')}}">

    <div class="row">

        <div class="col-md-3"></div>
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            @if(Request::has('search_key'))
            <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
            @endif
            <input type="hidden" id="follower_id" name="follower_id" value="{{Request::get('follower_id') ?? ''}}">
        </div>



        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('followers_search_placeholder')}}">

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.user_followers',['follower_id'=>Request::get('follower_id')])}}" class="btn btn-default reset-btn">
                        <span class="glyphicon glyphicon-search"> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>
                 
                </span>

            </div>

        </div>

    </div>

</form>
<br>