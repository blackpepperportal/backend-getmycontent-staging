<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$stardom_product_details->id ? tr('edit_stardom_product') : tr('add_stardom_product')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.stardom_products.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_stardom_products') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.stardom_products.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="stardom_product_id" id="stardom_product_id" value="{{ $stardom_product_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $stardom_product_details->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">{{tr('quantity')}}*</label>
                                            <input type="number" id="quantity" name="quantity" class="form-control" placeholder="{{tr('quantity')}}" value="{{ $stardom_product_details->quantity ?: old('quantity') }}" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">{{tr('price')}}*</label>
                                            <input type="number" id="price" name="price" class="form-control" placeholder="{{tr('price')}}" value="{{ $stardom_product_details->price ?: old('price') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">{{ tr('select_stardom') }} <span class="admin-required">*</span> </label>
                                            <select class="form-control select2" id="stardom_id" name="stardom_id" required>
                                            <option value="">{{tr('select_stardom')}}</option>
                                            @foreach($stardoms as $stardom_details)
                                                <option class="select-color" value="{{$stardom_details->id}}"@if($stardom_details->is_selected == YES) selected @endif >
                                                    {{$stardom_details->name}}
                                                </option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">{{tr('price')}}*</label>
                                            <input type="number" id="price" name="price" class="form-control" placeholder="{{tr('price')}}" value="{{ $stardom_product_details->price ?: old('price') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                        <label>{{ tr('select_picture') }}</label>

                                            <label id="user_picture" class="file center-block">
                                                <input type="file" id="picture" name="picture" accept="image/png,image/jpeg" onchange="loadFile(this,'image_preview')">
                                                 <img id="image_preview" class="img-thumbnail img-fluid" style="width: 100px;margin: 10px;height: 100px; " src="{{ $stardom_product_details->picture ? $stardom_product_details->picture : asset('placeholder.png') }}">
                                            <span class="file-custom"></span>

                                            </label>                                
                                        </div>

                                    </div>

                                </div>
                                
                            </div>

                            <div class="row">

                                <div class="col-md-12"> 

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}<span class="admin-required">*</span></label>

                                        <textarea id="summernote" rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $stardom_product_details->description}}</textarea>

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

