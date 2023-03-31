@extends('layout')

@section('title', 'Khat - Register')

@section('content')
    <div class="wrapper">
        <form action="{{route('register.post')}}" class="card signup" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-header">
                <h1>Register</h1>
            </div>
        
            @if($errors -> any())
                <div class="error-text">
                    @foreach ($errors -> all() as $error)
                        {{$error}}
                        <br />
                    @endforeach
                </div>
            @endif

            @if(session() -> has('error'))
                <div class="error-text">
                    {{session('error')}}
                </div>
            @endif

            @if(session() -> has('success'))
                <div class="error-text">
                    {{session('success')}}
                </div>
            @endif
            
            <div class="form-body">
                <input type="text" name="name" id="name" class="input-field" placeholder="Name" />
                <input type="text" name="username" id="username" class="input-field" placeholder="Username" />
                <input type="text" name="email" id="email" class="input-field" placeholder="Email" />
                <input type="password" name="password" id="password" class="input-field" placeholder="Password" />
                <div class="profile-picture-container">
                    <label for="profilePicture" class="btn btn-small">Choose Profile Picture</label>
                    <input type="file" name="profilePicture" id="profilePicture" hidden />
                    <p class="file-status">No Files Selected!</p>
                </div>
            </div>

            <div class="form-footer">
                <input type="submit" value="Register" name="register" class="btn" id="registerButton" />
                <p>Already have an Account?&nbsp;<span><a href={{route('login')}}>Login</a></span></p>
            </div>
        </form>
  </div>
@endsection