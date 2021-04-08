<form method="GET" action="{{route('admin.users_subscriptions.index')}}" class="">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6">
            @if(Request::has('search_key'))
            <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{SORT_BY_HIGH}}" @if(Request::get('status') == SORT_BY_HIGH && Request::get('status')!='' ) selected @endif>{{tr('sort_by_high')}}</option>

                <option class="select-color" value="{{SORT_BY_LOW}}" @if(Request::get('status') == SORT_BY_LOW && Request::get('status')!='' ) selected @endif>{{tr('sort_by_low')}}</option>

                <option class="select-color" value="{{SORT_BY_FREE}}" @if(Request::get('status') == SORT_BY_FREE && Request::get('status')!='' ) selected @endif>{{tr('sort_by_free')}}</option>

                <option class="select-color" value="{{SORT_BY_PAID}}" @if(Request::get('status') == SORT_BY_PAID && Request::get('status')!='' ) selected @endif>{{tr('sort_by_paid')}}</option>

            </select>
        </div>

        
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('user_subscriptions_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.users_subscriptions.index')}}" class="btn btn-default reset-btn">
                        <span class="glyphicon glyphicon-search"> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>