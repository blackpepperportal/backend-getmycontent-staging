<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$sub_category_details->id ? tr('edit_sub_category') : tr('add_sub_category')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.sub_categories.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_sub_categories') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.sub_categories.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="sub_category_id" id="sub_category_id" value="{{ $sub_category_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $sub_category_details->name ?: old('name') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                             <label for="title">{{ tr('select_category') }} <span class="admin-required">*</span> </label>
                                            <select class="form-control select2" id="category_id" name="category_id" required>
                                            <option value="">{{tr('select_category')}}</option>
                                            @foreach($categories as $category_details)
                                                <option class="select-color" value="{{$category_details->id}}"@if($category_details->is_selected == YES) selected @endif >
                                                    {{$category_details->name}}
                                                </option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ tr('select_picture') }}</label>
                                        <input type="file" class="form-control"  id="picture" name="picture" accept="image/png,image/jpeg" src="{{ $sub_category_details->picture ? $sub_category_details->picture : asset('placeholder.png') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-12"> 

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}<span class="admin-required">*</span></label>

                                        <textarea rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $sub_category_details->description}}</textarea>

                                    </div>

                                </div>

                            </div>
                          
                            <div class="form-actions">

                                <div class="pull-right">
                                
                                    <button type="reset" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> {{ tr('reset') }} 
                                    </button>

                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                
                                </div>

                                <div class="clearfix"></div>

                            </div>

                        </form>
                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>

