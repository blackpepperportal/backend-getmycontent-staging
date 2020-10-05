<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$support_ticket_details->id ? tr('edit_subscription') : tr('add_support_tickets')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.support_tickets.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{tr('view_support_tickets')}}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        
                        <form class="forms-sample" action="{{ Setting::get('is_demo_control_enabled') == NO ? route('admin.support_tickets.save') : '#'}}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf

                            <div class="card-body">

                                <input type="hidden" name="support_ticket_id" id="support_ticket_id" value="{{$support_ticket_details->id}}">

                                <div class="row">

                                    <div class="form-group col-md-6">

                                        <label for="subject" class="">{{ tr('subject') }} <span class="admin-required">*</span></label>

                                        <input type="text" name="subject" class="form-control" id="subject" value="{{ old('subject') ?: $support_ticket_details->subject }}" placeholder="{{ tr('subject') }}" required >
                                        
                                    </div>

                                    
                                    
                                </div>

                                

                                

                                <div class="row">

                                    <div class="form-group col-md-12">

                                        <label for="simpleMde">{{ tr('message') }}</label>

                                        <textarea class="form-control" id="message" name="message">{{ old('message') ?: $support_ticket_details->message}}</textarea>

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
