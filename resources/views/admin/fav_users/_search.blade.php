  <form method="GET" action="{{route('admin.fav_users.index')}}" class="form-bottom">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12"></div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key"
                placeholder="{{tr('fav_user_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <input type="hidden" name="user_id" id="user_id" value="{{ (request()->user_id) ? request()->user_id : '' }}">

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>
                
                <button class="btn btn-default"><a  href="{{route('admin.fav_users.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>