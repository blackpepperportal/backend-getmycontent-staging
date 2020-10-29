<section id="basic-form-layouts">

    <div class="row match-height">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ $user_details->id ? tr('edit_user') : tr('add_user') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.users.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_users') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.users.save') }}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf

                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="user_id" id="user_id" value="{{ $user_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('first_name') }}*</label>
                                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="{{ tr('first_name') }}" value="{{ $user_details->first_name ?: old('first_name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('last_name') }}*</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="{{ tr('last_name') }}" value="{{ $user_details->last_name ?: old('last_name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>



                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{tr('email')}}*</label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" value="{{ $user_details->email ?: old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">{{ tr('mobile') }}</label>
                                            <input type="number" minlength="10" maxlength="12" class="form-control" pattern="[0-9]{6,13}" id="mobile" name="mobile" placeholder="{{ tr('mobile') }}" value="{{ old('mobile') ?: $user_details->mobile}}"/>
                                        </div>
                                    </div>

                                </div>


                                @if(!$user_details->id)

                                <div class="row">

                                    <div class="col-md-6">                    
                                        <div class="form-group">
                                            <label for="password" class="">{{ tr('password') }} *</label>
                                            <input type="password" minlength="6" required name="password" class="form-control" id="password" placeholder="{{ tr('password') }}" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="confirm-password" class="">{{ tr('confirm_password') }} *</label>
                                            <input type="password" minlength="6" required name="password_confirmation" class="form-control" id="confirm-password" placeholder="{{ tr('confirm_password') }}">
                                        </div>
                                    </div>

                                </div>

                                @endif

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>{{ tr('select_picture') }}</label>

                                            <input type="file" class="form-control" name="picture" accept="image/*" >

                                        </div>

                                    </div>


                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>{{ tr('select_cover') }}</label>

                                            <input type="file" class="form-control" name="cover" accept="image/*" >

                                        </div>
                                    </div>



                                </div>


                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="website">{{ tr('website') }}</label>
                                            <input type="text" id="website" name="website" class="form-control" placeholder="{{ tr('website') }}" value="{{ $user_details->website ?: old('website') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amazon_wishlist">{{ tr('amazon_wishlist') }}</label>
                                            <input type="text" id="amazon_wishlist" name="amazon_wishlist" class="form-control" placeholder="{{ tr('amazon_wishlist') }}" value="{{ $user_details->amazon_wishlist ?: old('amazon_wishlist') }}" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="choose_account_type">{{ tr('choose_account_type') }}</label><br>

                                            <input type="radio"  id="free" name="user_account_type" value="{{USER_FREE_ACCOUNT}}"  {{ ($user_details->user_account_type  == USER_FREE_ACCOUNT)? "checked" : "" }} ><label for="{{USER_FREE_ACCOUNT}}"> {{tr('free_users')}} </label>&nbsp;

                                            <input type="radio" id="premium" name="user_account_type" value="{{USER_PREMIUM_ACCOUNT}}" {{ ($user_details->user_account_type  == USER_FREE_ACCOUNT)? "checked" : "" }} ><label for="{{USER_PREMIUM_ACCOUNT}}"> {{tr('premium_users')}} </label>

                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>{{ tr('gender') }}</label>

                                            <select class="form-control select2" name="gender" required>
                                                <option>{{tr('select_gender')}}</option>

                                                <option value="{{MALE}}" @if($user_details->gender == MALE) selected="true" @endif>{{ tr('male') }}</option>

                                                <option value="{{FEMALE}}" @if($user_details->gender == FEMALE) selected="true" @endif>{{ tr('female') }}</option>

                                                <option value="{{OTHERS}}" @if($user_details->gender == OTHERS) selected="true" @endif>{{ tr('others') }}</option>

                                            </select>

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

