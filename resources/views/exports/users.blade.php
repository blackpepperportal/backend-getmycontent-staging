<!DOCTYPE html>
<html>

<head>
    <title>{{tr('users_management')}}</title>
</head>
<style type="text/css">

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .first_row_design{
        background-color: #187d7d;
        color: #ffffff;
    }

    .row_col_design{
        background-color: #cccccc;
    }

    th{
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-weight: bold;

    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;

    }
    
</style>

<body>

    <table>

        <!-- HEADER START  -->

        <tr class="first_row_design">

            <th>{{tr('s_no')}}</th>

            <th >{{tr('username')}}</th>

            <th>{{tr('email')}}</th>

            <th>{{tr('mobile')}}</th>

            <th >{{tr('picture')}}</th>
             
            <th> {{tr('about')}} </th>

            <th >{{tr('address')}}</th>

            <th >{{tr('user_type')}}</th>

            <th >{{tr('user_account_type')}}</th>

            <th >{{tr('payment_mode')}}</th>

            <th >{{tr('device_type')}}</th>

            <th >{{tr('amount_paid')}}</th>

            <th >{{tr('expiry_date')}}</th>
            
            <th >{{tr('no_of_days')}}</th>

            <th >{{tr('status')}}</th>

            <th >{{tr('created')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $user)

        <tr @if($i % 2 == 0) class="row_col_design" @endif >

            <td>{{$i+1}}</td>

            <td>{{$user->name}}</td>

            <td>{{$user->email}}</td>

            <td>{{$user->mobile}}</td>

            <td>
                @if($user->picture) {{$user->picture}} @else {{asset('admin-css/dist/img/avatar.png')}} @endif
            </td>

            <td>{{$user->about}}</td>

            <td >{{$user->address}}</td>

            <td >{{$user->user_type}}</td>

            <td >{{$user->user_account_type}}</td>

            <td >{{$user->payment_mode}}</td>

            <td >{{$user->device_type}}</td>

            <td >{{$user->amount_paid}}</td>

            <td >{{convertTimeToUSERzone($user->expiry_date, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

            <td >{{$user->no_of_days}}</td>

            @if($user->status == USER_APPROVED) 
            <td >{{tr('approved')}}</td>

            @else
            <td >{{tr('declined')}}</td>

            @endif

            <td>{{convertTimeToUSERzone($user->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

            <td>{{convertTimeToUSERzone($user->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

        </tr>

        @endforeach
    </table>

</body>

</html>