<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ tr('documents') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.documents.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_documents') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.documents.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="document_id" id="document_id" value="{{ $document_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $document_details->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label>{{ tr('select_picture') }}</label>
                                            <label id="user_picture" class="file center-block">
                                                <input type="file" id="picture" name="picture" accept="image/png,image/jpeg" onchange="loadFile(this,'image_preview')">
                                                 <img id="image_preview" class="img-thumbnail img-fluid" style="width: 100px;margin: 10px;height: 100px; " src="{{ $document_details->picture ? $document_details->picture : asset('placeholder.png') }}">
                                            <span class="file-custom"></span>
                                            </label>                                
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group clearfix icheck_minimal skin">

                                          <div class="icheck-success d-inline">

                                            <fieldset>

                                                <input type="checkbox" id="input-6" name="is_required" value="{{YES}}" @if($document_details->is_required ==  YES) checked="checked" @endif>

                                                <label for="input-6">{{tr('is_required')}}</label>

                                            </fieldset>

                                          </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        
                                        <label for="description">{{ tr('description') }}</label>

                                        <textarea class="form-control" name="description" placeholder="{{ tr('description') }}">{{ $document_details->description ? $document_details->description :old('description') }}</textarea>
                                       
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
