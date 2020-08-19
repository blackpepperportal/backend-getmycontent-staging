@if(!Request::get('unverified'))
    <form method="GET" action="{{route('admin.stardoms.index')}}">

        <div class="row">

            <div class="col-3">
                @if(Request::has('search_key'))
                    <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
                @endif
            </div>

            <div class="col-3">

                <select class="form-control select2" name="status">

                    <option  class="select-color" value="">{{tr('select_status')}}</option>

                    <option  class="select-color" value="{{SORT_BY_APPROVED}}">{{tr('approved')}}</option>

                    <option  class="select-color" value="{{SORT_BY_DECLINED}}">{{tr('declined')}}</option>

                    <option  class="select-color" value="{{SORT_BY_EMAIL_VERIFIED}}">{{tr('verified')}}</option>

                    <option  class="select-color" value="{{SORT_BY_EMAIL_NOT_VERIFIED}}">{{tr('un_verified')}}</option>

                </select>

            </div>

            <div class="col-6">

                <div class="input-group">
                   
                    <input type="text" class="form-control" name="search_key"
                    placeholder="{{tr('stardoms_search_placeholder')}}"> <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default">
                       <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                    </button>
                    
                    <button class="btn btn-default"><a  href="{{route('admin.stardoms.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                    </a>
                       
                    </span>

                </div>
                
            </div>

        </div>

    </form>
    <br>
@else

    <form method="GET" action="{{route('admin.stardoms.index')}}">

        <div class="row">

            <div class="col-6"></div>

            <div class="col-6">

                <div class="input-group">
                   
                    <input type="text" class="form-control" name="search_key"
                    placeholder="{{tr('stardoms_search_placeholder')}}"> <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default">
                       <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                    </button>
                    
                    <button class="btn btn-default"><a  href="{{route('admin.stardoms.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                    </a>
                       
                    </span>

                </div>
                
            </div>

        </div>

    </form>
    <br>

@endif