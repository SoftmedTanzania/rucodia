@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-lg-4 col-md-5">
        <div class="card card-user">
            <div class="image">
                <img src="{{ url('assets/img/background/user_bg.jpg') }}" alt="..."/>
            </div>
            <div class="content">
                <div class="author">
                    <img class="avatar border-white" src="{{ url('assets/img/faces/face-0.jpg') }}" alt="..."/>
                    <h4 class="title">{{ $user->firstname }} {{ $user->surname }}<br />
                        <a href="#"><small>{{ '@'.$user->username }}</small></a>
                    </h4>
                </div>
                <p class="description text-center">
                    <strong>Email:</strong> {{ $user->email }} <br>
                    <strong>Level:</strong> @foreach ($user->levels as $level) {{ $level->name }} @endforeach
                </p>
            </div>
            <hr>
            <div class="text-center">
                <div class="row">
                    <div class="col-md-3 col-md-offset-1">
                        <h5>12<br /><small>Products</small></h5>
                    </div>
                    <div class="col-md-4">
                    <h5>{{ count($user->transactions) }}<br /><small>Transactions</small></h5>
                    </div>
                    <div class="col-md-3">
                        <h5>{{ $user->revenue }}<br /><small>Revenue</small></h5>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-8 col-md-7">
        <div class="card">
            <div class="header">
                <h4 class="title">Other Details</h4>
            </div>
            <div class="content">
                <ul class="list-unstyled team-members">
                            <li>
                                <div class="row">
                                    <div class="col-xs-9">
                                        {{ $user->firstname }} {{ $user->middlename }} {{ $user->surname }}
                                        <br />
                                        <span class="text-muted"><small>Full Name</small></span>
                                    </div>

                                    <div class="col-xs-3 text-right">
                                        <btn class="btn btn-sm btn-success btn-icon"><i class="fa fa-asterisk"></i></btn>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-xs-9">
                                        @foreach($user->wards as $ward)
                                                {{ $ward->district->region->name }}
                                        @endforeach
                                        <br />
                                        <span class="text-success"><small>Region</small></span>
                                    </div>

                                    <div class="col-xs-3 text-right">
                                        <btn class="btn btn-sm btn-success btn-icon"><i class="fa fa-asterisk"></i></btn>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-xs-9">
                                        @foreach($user->wards as $ward)
                                            {{ $ward->district->name }}
                                        @endforeach
                                        <br />
                                        <span class="text-danger"><small>District</small></span>
                                    </div>

                                    <div class="col-xs-3 text-right">
                                        <btn class="btn btn-sm btn-success btn-icon"><i class="fa fa-asterisk"></i></btn>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-xs-9">
                                        @foreach($user->wards as $ward) {{ $ward->name }} @endforeach
                                        <br />
                                        <span class="text-danger"><small>Ward</small></span>
                                    </div>

                                    <div class="col-xs-3 text-right">
                                        <btn class="btn btn-sm btn-success btn-icon"><i class="fa fa-asterisk"></i></btn>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-xs-9">
                                        &nbsp;
                                        <br />
                                        <span class="text-danger"><small>&nbsp;</small></span>
                                    </div>

                                    <div class="text-center">
                                        <a href="{{url('users')}}/{{$user->id}}/edit" type="submit" class="btn btn-success btn-fill btn-wd">Update User</a href="{{ url('/user/$user->id/edit') }}">
                                    </div>
                                </div>
                            </li>
                        </ul>
            </div>
        </div>
    </div>
</div>
@endsection