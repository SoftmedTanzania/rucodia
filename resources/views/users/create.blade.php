@extends('layouts.admin')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Add a new user  |  <a href="{{ url('excel/import/users') }}">import excel file</a></h4>
            </div>
            <div class="content">
                <form action="{{ url('users') }}" role="form" method="POST">
                {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control border-input" placeholder="firstname" id="firstname" name="firstname">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" class="form-control border-input" placeholder="middlename" id="middlename" name="middlename">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control border-input" placeholder="surname" id="surname" name="surname" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Mobile Phone</label>
                                <input type="phone" class="form-control border-input" placeholder="phone" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control border-input" placeholder="Username" id="username" name="username">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="level">User Role</label>
                                <select name="level" id="level" class="form-control border-input">
                                    <option>Select level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}">{{ ucfirst($level->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control border-input" id="password" name="password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Repeat Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-input">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="region">Region</label>
                                <select id="region" name="region" class="form-control border-input">
                                    <option>Select a region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="district">District</label>
                                <select id="district" name="district" class="form-control border-input">
                                    <option value="">Select a district</option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                                <label for="ward">Ward</label>
                                <select id="ward" name="ward" class="form-control border-input">
                                    <option value="">Select a ward</option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Store Name</label>
                                <input type="text" class="form-control border-input" placeholder="e.g Office" id="location" name="location">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>GPS Latitude</label>
                                <input type="text" class="form-control border-input" placeholder="e.g -2.1290" id="latitude" name="latitude">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>GPS Longitude</label>
                                <input type="text" class="form-control border-input" placeholder="e.g 30.15543" id="longitude" name="longitude">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-fill btn-wd">Add User</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ url('assets/js/jquery-3.2.1.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $('#region').on('change', function(e){
        console.log(e);
        var region_id = e.target.value;
 
        $.get('{{ url('users') }}/districts/' + region_id, function(data) {
            console.log(data);
            $('#district').empty();
            $.each(data, function(index,subCatObj){
                $('#district').append("<option value='"+subCatObj.id+"'>"+subCatObj.name+"</option>");
            });
        });
    });

    $('#district').on('change', function(e){
        console.log(e);
        var district_id = e.target.value;
 
        $.get('{{ url('users') }}/wards/' + district_id, function(data) {
            console.log(data);
            $('#ward').empty();
            $.each(data, function(index,subCatObj){
                $('#ward').append("<option value='"+subCatObj.id+"'>"+subCatObj.name+"</option>");
            });
        });
    });
</script>
@endsection
