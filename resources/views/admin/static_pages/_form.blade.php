<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$static_page->id ? tr('edit_static_page') : tr('add_static_page')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.static_pages.index') }}" class="btn btn-primary"><i class="ft-file icon-left"></i>{{ tr('view_static_pages') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        @if(Setting::get('is_demo_control_enabled') == NO )

                        <form class="forms-sample" action="{{ route('admin.static_pages.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @else

                            <form class="forms-sample" role="form">

                        @endif 

                            @csrf

                            <div class="card-body">

                                @if($static_page->id)

                                    <input type="hidden" name="static_page_id" value="{{$static_page->id}}">

                                @endif

                                <div class="form-body">

                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label for="title">{{tr('title')}}<span class="admin-required">*</span> </label>
                                                <input type="text" id="title" name="title" class="form-control" placeholder="Enter {{tr('title')}}" required  value="{{old('title')?: $static_page->title}}" onkeydown="return alphaOnly(event);">
                                            </div>
                                        </div>


                                        <div class="form-group col-md-4">

                                            <label for="page">
                                                {{tr('select_section_type')}}

                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label>

                                            <select class="form-control select2" name="section_type" required>
                                                <option value="">{{tr('select_section_type')}}</option>

                                                @foreach($section_types as $key => $value)

                                                <option value="{{$key}}" @if($key == $static_page->section_type) selected @endif>{{ $value }}</option>

                                                @endforeach 
                                            </select>

                                        </div>

                                        <div class="form-group col-md-6">

                                            <label for="page">
                                                {{tr('select_static_page_type')}}
                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label>
                                            
                                            <select class="form-control select2" name="type" required>
                                                <option value="">{{tr('select_static_page_type')}}</option>

                                                @foreach($static_keys as $value)

                                                    <option value="{{$value}}" @if($value == $static_page->type) selected="true" @endif>{{ ucfirst($value) }}</option>

                                                @endforeach 
                                            </select>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="row">

                                        <div class="col-md-12"> 

                                            <div class="form-group">

                                                <label for="description">{{tr('description')}}<span class="admin-required">*</span></label>

                                                <textarea id="summernote" rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $static_page->description}}</textarea>

                                            </div>

                                        </div>

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

