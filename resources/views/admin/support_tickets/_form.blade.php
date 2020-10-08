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

                                </div>

                                <div class="row">

                                    <div class="form-group col-md-6">

                                       <label for="users" class="">{{ tr('user') }} <span class="admin-required">*</span></label>

                                        <select class="form-control select2" name="user_id" id="user_id">

                                        <option class="select-color" value="{{ $support_ticket_details->user_id ?: '' }}">

                                            
                                            {{ $support_ticket_details->user_id ? $users_name: 'Select User' }}

                                         

                                        </option>
                                        
                                        @foreach($users as $users)
                                        <option class="select-color" value="{{$users->id}}">{{$users->name}}</option>     
                                        @endforeach
                                        </select>
                                        
                                    </div>
                                    <div class="form-group col-md-6">

                                        <label for="subject" class="">{{ tr('support_member') }} <span class="admin-required">*</span></label>

                                        <select class="form-control select2" name="support_member_id" id="support_member_id">

                                        <option class="select-color" value="{{ $support_ticket_details->support_member_id ?: '' }}">
                                            
                                            {{ $support_ticket_details->support_member_id ? $support_members_name : 'Select Support Member' }}
                                        </option>
                                       
                                        @foreach($support_members as $support_members)
                                        <option class="select-color" value="{{$support_members->id}}">{{$support_members->name}}</option>     
                                        @endforeach
                                        </select>
                                       
                                        
                                    </div>
                                </div>
                                        

                                 <div class="row">

                                    <div class="form-group col-md-6">

                                        <label for="subject" class="">{{ tr('subject') }} <span class="admin-required">*</span></label>

                                        <input type="text" name="subject" class="form-control" id="subject" value="{{ old('subject') ?: $support_ticket_details->subject }}" placeholder="{{ tr('subject') }}" required >
                                        
                                    </div>
                                    <div class="form-group col-md-6">

                                       
                                    
                                        
                                        
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
