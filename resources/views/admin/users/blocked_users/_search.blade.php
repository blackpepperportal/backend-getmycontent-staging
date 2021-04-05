<form method="GET" action="{{route('admin.block_users.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            @if(Request::has('search_key'))
            <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>

      
        <div class="col-md-3"></div>
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('users_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.block_users.index')}}" class="btn btn-default reset-btn">
                        <span class="glyphicon glyphicon-search"> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>