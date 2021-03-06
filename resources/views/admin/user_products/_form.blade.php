<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$user_product->id ? tr('edit_user_product') : tr('add_user_product')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.user_products.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_user_products') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.user_products.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="user_product_id" id="user_product_id" value="{{ $user_product->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $user_product->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">{{tr('quantity')}}*</label>
                                            <input type="number" id="quantity" name="quantity" class="form-control" placeholder="{{tr('quantity')}}" value="{{ $user_product->quantity ?: old('quantity') }}" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">{{tr('price')}}*</label>
                                            <input type="number" id="price" name="price" class="form-control" placeholder="{{tr('price')}}" value="{{ $user_product->price ?: old('price') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">{{ tr('select_content_creator') }} <span class="admin-required">*</span> </label>
                                            <select class="form-control select2" id="user_id" name="user_id" required>
                                            <option value="">{{tr('select_content_creator')}}</option>
                                            @foreach($users as $user_details)
                                                <option class="select-color" value="{{$user_details->id}}"@if($user_details->is_selected == YES) selected @endif >
                                                    {{$user_details->name}}
                                                </option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                
                                    <div class="col-md-12">

                                        <div class="form-group">

                                            <label>{{ tr('select_picture') }}</label>

                                            <input type="file" class="form-control" name="picture" accept="image/png,image/jpeg" >
                                                                      
                                        </div>
                                        
                                    </div>

                                </div>
                                
                            </div>

                            <div class="row">

                                <div class="col-md-12"> 

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}<span class="admin-required">*</span></label>

                                        <textarea id="summernote" rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $user_product->description}}</textarea>

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

