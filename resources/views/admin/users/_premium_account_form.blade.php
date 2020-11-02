<div id="{{$user->id}}" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">

        <form action="{{route('admin.users.upgrade_account')}}">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">{{ ($user->user_account_type  == USER_FREE_ACCOUNT) ? tr('upgrade_to_premium') : tr('update_premium') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <input type="hidden" name="user_id" value="{{$user->id}}">
                        <input type="hidden" name="subscription_id" value="{{($user->userSubscription) ?$user->userSubscription->id : ''}}">


                        <div class="col-md-6 premium_account">
                            <h6 class="">
                                <label for="user_name">{{ tr('user_name') }}</label>&nbsp;: &nbsp;
                                <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}">
                                    {{$user->name}}
                                </a>
                            </h6>
                        </div>

                    </div>

                    <br>

                    <div class="row">

                        <div class="col-md-6 premium_account">
                            <div class="form-group">
                                <label for="monthly_amount">{{ tr('monthly_amount') }}</label><br>
                                <input type="number" min="0" step="any" id="monthly_amount" name="monthly_amount" class="form-control" placeholder="{{ tr('monthly_amount')}}" value="{{ ($user->userSubscription) ? $user->userSubscription->monthly_amount : '' }}" required>

                            </div>
                        </div>

                        <div class="col-md-6 premium_account">
                            <div class="form-group">
                                <label for="yearly_amount">{{ tr('yearly_amount') }}</label><br>
                                <input type="number" min="0" step="any" id="yearly_amount" name="yearly_amount" class="form-control" placeholder="{{ tr('yearly_amount') }}" value="{{ ($user->userSubscription) ? $user->userSubscription->yearly_amount : '' }}" required>

                            </div>
                        </div>

                    </div>

                    @if($user->userBillingAccounts->count() <= 0)

                        <div class="row">

                            <div class="col-md-12">
                                <hr>
                                <h5><b>{{tr('add_billing_account')}}</b></h5>
                                <hr>
                            </div>

                            <input type="hidden" name="is_billing_account" value="1">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nickname">{{ tr('nickname') }}</label><br>
                                    <input type="text" id="nickname" name="nickname" class="form-control" placeholder="{{ tr('nickname') }}" value="" required>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_holder_name">{{ tr('account_holder_name') }}</label><br>
                                    <input type="text" id="account_holder_name" name="account_holder_name" class="form-control" placeholder="{{ tr('account_holder_name') }}" value="" required>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_number">{{ tr('account_number') }}</label><br>
                                    <input type="number" id="account_number" name="account_number" class="form-control" placeholder="{{ tr('account_number') }}" value="" required>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ifsc_code">{{ tr('ifsc_code') }}</label><br>
                                    <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" placeholder="{{ tr('ifsc_code') }}" value="" required>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="swift_code">{{ tr('swift_code') }}</label><br>
                                    <input type="text" id="swift_code" name="swift_code" class="form-control" placeholder="{{tr('swift_code')}}" value="" required>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bank_name">{{ tr('bank_name') }}</label><br>
                                    <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="{{tr('bank_name')}}" value="" required>

                                </div>
                            </div>
                        </div>

                    @endif
                        
                    <br>

                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('cancel')}}</button>
                        <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

        </form>

    </div>

</div>