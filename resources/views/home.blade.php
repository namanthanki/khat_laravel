@extends('layout')

@section('title', 'Khat - Chats')

@section('content')
    @auth
        <div class="wrapper">
            <div class="card card-start">
                <header>
                    <h1>Khat</h1>
                    <a href="{{route('settings')}}"><i class="fas fa-gear" id="settings-btn"></i></a>
                </header>

                <div class="search-container">
                    <input type="search" name="search" id="search" class="input-field" placeholder="Search Users" />
                    <i class="fas fa-search" id="#searchBtn"></i>
                </div>

                <div class="chats-container">
                    <div class="chats">
                    </div>
                </div>
            </div>
        </div>

        <script>
            setInterval(() => {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('users') }}",
                    success: function(data) {
                        if (!$("#search").hasClass("active")) {
                            $(".chats").html(data);
                        }
                    }
                })
            }, 500);

            $("#search").keyup(function() {
                let searchContent = $("#search").val();
                if (searchContent !== "") {
                    $("#search").addClass("active");
                    $(".chats").addClass("default");
                } else {
                    $("#search").removeClass("active");
                    $(".chats").removeClass("default");
                }

                $.ajax({
                    type: "POST",
                    url: "{{route('users')}}",
                    data: { searchValue: searchContent, _token: '{{csrf_token()}}'},
                    dataType: "html",
                    success: function(data) {
                        if (data != "User Not Found!") {
                                console.log(data);
                                $(".chats").removeClass("default");
                        }
                        $(".chats").html(data);
                    }
                });
            });
        </script>
    @endauth
@endsection
