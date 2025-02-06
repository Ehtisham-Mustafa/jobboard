@extends('front.layouts.app')
@section('main')
    <section class="section-5 bg-2">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Account Settings</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    @include('front.account.sidebar')
                </div>
                <div class="col-lg-9">
                    @include('front.message')
                    <div class="card border-0 shadow mb-4">
                        <form action="" id="userForm" enctype="multipart/form-data" method="post" name="userForm" onsubmit="profileChange();">
                            {{csrf_field()}}
            
                            <div class="card-body  p-4">
                                <h3 class="fs-4 mb-1">My Profile</h3>

                                <div class="mb-4">
                                    <label for="" class="mb-2">Name*</label>
                                    <input type="text" placeholder="Enter Name" name="name" id="name"
                                           class="form-control" value="{{$user->name}}">
                                           <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Email*</label>
                                    <input type="text" placeholder="Enter Email" name="email" id="email"
                                           class="form-control" value="{{$user->email}}">
                                           <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Designation</label>
                                    <input type="text" placeholder="Designation" name="designation" id="designation"
                                           class="form-control" value="{{$user->designation}}">
                                           <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Mobile No</label>
                                    <input type="text" placeholder="Mobile" name="phone" id="phone" class="form-control"
                                           value="{{$user->mobile}}">
                                           <p></p>
                                </div>
                            </div>
                            <div class="card-footer  p-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>

                    <div class="card border-0 shadow mb-4">
                        <form action="" method="post" id="changePasswordForm" name="changePasswordForm" onsubmit="changePassword();">
                            {{csrf_field()}}
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">Change Password</h3>
                            <div class="mb-4">
                                <label for="old_password" class="mb-2">Old Password*</label>
                                <input type="password" id="old_password" name="old_password" placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="new_password" class="mb-2">New Password*</label>
                                <input type="password" id="new_password" name="new_password" placeholder="New Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="mb-2">Confirm Password*</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" class="form-control">
                                <p></p>
                            </div>
                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script type="text/javascript">

        function profileChange(){
            e.preventDefault();
            $.ajax({
                url: '{{route("account.updateProfile")}}',
                type: 'post',
                dataType: 'json',
                data: $("#userForm").serializeArray(),
                success:function (resp){
                    var response=JSON.parse(resp);
                    if(response.status==true){
                        console.log(response.status);
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    window.location.href="{{route('account.profile')}}"
                    }else{
                        var errors=response.errors;
                        if(errors.name){
                            $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                        }else{
                            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');

                        }
                        if(errors.email){
                            $("#email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email);
                        }else{
                            $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');

                        }
                    }
                }
            });
        }


        //change password ajax

        function changePassword(){
            e.preventDefault();
            $.ajax({
                url: '{{route("account.updatePassword")}}',
                type: 'post',
                dataType: 'json',
                data: $("#changePasswordForm").serializeArray(),
                success:function (resp){
                    var response=JSON.parse(resp);
                    console.log(response);
                    if(response.status==true){
                        console.log(response.status);
                        $("#old_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        $("#new_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    window.location.href="{{route('account.profile')}}"
                    }else{
                        var errors=response.errors;
                        if(errors.old_password){
                            $("#old_password").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.old_password);
                        }else{
                            $("#old_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');

                        }
                        if(errors.email){
                            $("#new_password").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.new_password);
                        }else{
                            $("#new_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');

                        }
                    }
                }
            });
        }
    </script>
