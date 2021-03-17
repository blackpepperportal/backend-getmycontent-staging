<section id="basic-form-layouts">

    <div class="row match-height">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ $user->id ? tr('edit_user') : tr('add_user') }}</h4>

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

                                    <input type="hidden" name="user_id" id="user_id" value="{{ $user->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('first_name') }}*</label>
                                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="{{ tr('first_name') }}" value="{{old('first_name') ?: $user->first_name}}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('last_name') }}*</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="{{ tr('last_name') }}" value="{{old('last_name') ?: $user->last_name}}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>



                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{tr('email')}}*</label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" value="{{ $user->email ?: old('email') }}" required pattern="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$"
                                              oninvalid="this.setCustomValidity(&quot;{{ tr('email_validate') }}&quot;)"
                                              oninput="this.setCustomValidity('')"
                                            
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">{{ tr('mobile') }}</label>
                                            <input type="number" minlength="10" maxlength="12" class="form-control" pattern="[0-9]{6,13}" id="mobile" name="mobile" placeholder="{{ tr('mobile') }}" value="{{ old('mobile') ?: $user->mobile}}"/>
                                        </div>
                                    </div>

                                </div>


                                @if(!$user->id)

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

                                    <div class="col-lg-12">
                                        <h3>{{tr('social_settings')}} ({{tr('optional')}})</h3>
                                        <hr>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="website">{{ tr('website') }}</label>
                                            <input type="url" id="website" name="website" class="form-control" placeholder="{{ tr('website') }}" value="{{ $user->website ?: old('website') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amazon_wishlist">{{ tr('amazon_wishlist') }}</label>
                                            <input type="url" id="amazon_wishlist" name="amazon_wishlist" class="form-control" placeholder="{{ tr('amazon_wishlist') }}" value="{{ $user->amazon_wishlist ?: old('amazon_wishlist') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instagram_link">{{ tr('instagram_link') }}</label>
                                            <input type="url" id="instagram_link" name="instagram_link" class="form-control" placeholder="{{ tr('instagram_link') }}" value="{{ $user->instagram_link ?: old('instagram_link') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="facebook_link">{{ tr('facebook_link') }}</label>
                                            <input type="url" id="facebook_link" name="facebook_link" class="form-control" placeholder="{{ tr('facebook_link') }}" value="{{ $user->facebook_link ?: old('facebook_link') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="twitter_link">{{ tr('twitter_link') }}</label>
                                            <input type="url" id="twitter_link" name="twitter_link" class="form-control" placeholder="{{ tr('twitter_link') }}" value="{{ $user->twitter_link ?: old('twitter_link') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="linkedin_link">{{ tr('linkedin_link') }}</label>
                                            <input type="url" id="linkedin_link" name="linkedin_link" class="form-control" placeholder="{{ tr('linkedin_link') }}" value="{{ $user->linkedin_link ?: old('linkedin_link') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pinterest_link">{{ tr('pinterest_link') }}</label>
                                            <input type="url" id="pinterest_link" name="pinterest_link" class="form-control" placeholder="{{ tr('pinterest_link') }}" value="{{ $user->pinterest_link ?: old('pinterest_link') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="youtube_link">{{ tr('youtube_link') }}</label>
                                            <input type="url" id="youtube_link" name="youtube_link" class="form-control" placeholder="{{ tr('youtube_link') }}" value="{{ $user->youtube_link ?: old('youtube_link') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="twitch_link">{{ tr('twitch_link') }}</label>
                                            <input type="url" id="twitch_link" name="twitch_link" class="form-control" placeholder="{{ tr('twitch_link') }}" value="{{ $user->twitch_link ?: old('twitch_link') }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="choose_account_type">{{ tr('choose_account_type') }}</label><br>
                                            
                                            <input type="radio" id="premium" onclick="premium_check();" name="user_account_type" value="{{USER_PREMIUM_ACCOUNT}}" {{ ($user->user_account_type  == USER_PREMIUM_ACCOUNT)? "checked" : "" }}   ><label for="{{USER_PREMIUM_ACCOUNT}}"> {{tr('premium_users')}} </label>

                                            <input type="radio"  id="free" onclick="premium_check();" name="user_account_type" value="{{USER_FREE_ACCOUNT}}"  {{ ($user->user_account_type  == USER_FREE_ACCOUNT)? "checked" : "" }} ><label for="{{USER_FREE_ACCOUNT}}"> {{tr('free_users')}} </label>&nbsp;

                                        </div>
                                    </div> -->

                                    <!-- <div class="col-md-6">

                                        <div class="form-group">

                                            <label>{{ tr('gender') }}</label>

                                            <select class="form-control select2" name="gender" required>
                                                <option>{{tr('select_gender')}}</option>

                                                <option value="{{MALE}}" @if($user->gender == MALE) selected="true" @endif>{{ tr('male') }}</option>

                                                <option value="{{FEMALE}}" @if($user->gender == FEMALE) selected="true" @endif>{{ tr('female') }}</option>

                                                <option value="{{OTHERS}}" @if($user->gender == OTHERS) selected="true" @endif>{{ tr('others') }}</option>

                                            </select>
                                        </div>

                                    </div> -->



                                </div>

                                <div class="row">

                                    <div class="col-md-6 premium_account" {{ ($user->user_account_type  == USER_FREE_ACCOUNT)? "style=display:none;": "" }}>
                                        <div class="form-group">
                                            <label for="monthly_amount">{{ tr('monthly_amount') }}</label><br>
                                            <input type="number" id="monthly_amount" name="monthly_amount" class="form-control" placeholder="{{ tr('monthly_amount') }}" value="{{ ($user->userSubscription) ? $user->userSubscription->monthly_amount: old('monthly_amount') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-6 premium_account" {{ ($user->user_account_type  == USER_FREE_ACCOUNT)? "style=display:none;": "" }}>
                                        <div class="form-group">
                                            <label for="yearly_amount">{{ tr('yearly_amount') }}</label><br>
                                            <input type="number" id="yearly_amount" name="yearly_amount" class="form-control" placeholder="{{ tr('yearly_amount') }}" value="{{ ($user->userSubscription)? $user->userSubscription->yearly_amount : old('yearly_amount') }}">

                                        </div>
                                    </div>

                                </div>

                            </div>


                          
                            <div class="row">

                            <div class="col-md-6">

                                    <div class="form-group">

                                        <label>{{ tr('category') }}</label>

                                        <select class="form-control select2" name="u_category_id" required>
                                            <option>{{tr('select_category')}}</option>

                                            @foreach($u_categories as $u_category)
                                                <option value="{{$u_category->id}}" @if($u_category->is_selected == YES) selected @endif>
                                                    {{ucfirst($u_category->name)}}
                                                </option>
                                            @endforeach

                                        </select>
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

