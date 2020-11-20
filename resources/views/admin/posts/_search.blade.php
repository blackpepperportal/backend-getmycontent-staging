
<form method="GET" action="{{route('admin.posts.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            @if(Request::has('search_key'))
                <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
          <input type="hidden" class="form-control" name="scheduled" value="{{Request::get('scheduled')}}">
            <select class="form-control select2" name="status">

                <option  class="select-color" value="">{{tr('select_status')}}</option>

                <option  class="select-color" value="{{SORT_BY_APPROVED}}">{{tr('approved')}}</option>

                <option  class="select-color" value="{{SORT_BY_DECLINED}}">{{tr('declined')}}</option>

                <option  class="select-color" value="{{SORT_BY_PAID_POST}}">{{tr('paid')}}</option>

                <option  class="select-color" value="{{SORT_BY_FREE_POST}}">{{tr('free_post')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key"
                placeholder="{{tr('post_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default reset-btn">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                
               <a class="btn btn-default reset-btn" href="{{route('admin.posts.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>