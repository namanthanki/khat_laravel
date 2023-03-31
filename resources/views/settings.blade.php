@extends('layout')

@section('title', 'Khat - Settings')

@section('content')

    <div class="wrapper">
        <div class="card card-start settings-container">
            <div class="settings-header">
                <a href="{{ route('/') }}"><i class="fas fa-arrow-left"></i></a>
                <h3>Settings</h3>
            </div>

            <form class="settings-body" method="POST">
                @csrf
                <div class="profile-picture-container">
                    <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture">
                </div>
                <div class="user-details">
                    <input type="text" name="username" id="user-name" value="{{ $user -> username }}" readonly
                        class="input-field">
                    <i class="fas fa-pen" id="editName"></i>
                </div>
                <div class="user-details">
                    <input type="email" name="email" id="user-email" value="{{ $user -> email }}" readonly
                        class="input-field">
                    <i class="fas fa-pen" id="editEmail"></i>
                </div>
                <button class="btn save-btn">Save</button>
            </form>

            <div class="settings-footer">
                <a class="btn" href="{{ route('logout') }}">Logout</a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const form = $(".settings-body");

            form.submit(function(event) {
                event.preventDefault();
            });

            $("#editName").click(function() {
                $("#user-name").prop("readonly", false);
                $(".save-btn").addClass("show");
            });

            $("#editEmail").click(function() {
                $("#user-email").prop("readonly", false);
                $(".save-btn").addClass("show");
            });

            $(".save-btn").click(function() {
                $("#user-name").val($("#user-name").val());
                $("#user-email").val($("#user-email").val());

                $("#user-name").prop("readonly", true);
                $("#user-email").prop("readonly", true);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('settings.save') }}",
                    type: "POST",
                    data: form.serialize(),
                    success: function(data) {
                        console.log(data);
                    }
                });

                $(".save-btn").removeClass("show");
            });
        });
    </script>

@endsection
