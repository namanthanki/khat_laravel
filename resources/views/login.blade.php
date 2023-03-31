@extends('layout')

@section('title', 'Khat - Login')

@section('content')
    <div class="wrapper">
        <form action="{{route('login.post')}}" class="card login" method="POST">
            @csrf

            <div class="form-header">
                <h1>Login</h1>
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
                <input type="text" name="email" id="email" class="input-field" placeholder="Email Address" />
                <input type="password" name="password" id="password" class="input-field" placeholder="Password" />
            </div>

            <div class="form-footer">
                <input type="submit" value="Login" name="login" class="btn" id="loginBtn" />
                <p>Don't have an Account?&nbsp;<span><a href={{route('register')}}>Register</a></span></p>
            </div>
        </form>
    </div>
@endsection