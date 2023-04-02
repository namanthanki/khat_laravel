@extends('layout')

@section('title', 'Khat - Messages')

@section('content')
    <div class="wrapper">
        <div class="card card-start chat-container">
            <div class="chat-header">
                <div class="go-back-container">
                    <a href="{{route('/')}}"><i class="fas fa-arrow-left" id="goBack"></i></a>
                </div>

                <div class="chat-user-details">
                    <div class="user-profile-picture">
                        <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture">
                    </div>

                    <div class="user-details">
                        <h3>{{ $user->name }}</h3>
                        <p>{{ '@' }}{{ $user->username }}</p>
                    </div>
                </div>

                <div class="status-container">
                    @if ($user->status === 'Active Now')
                        <i class='fas fa-circle online'></i>
                    @else
                        <i class='fas fa-circle offline'></i>
                    @endif
                </div>
            </div>

            <div class="chat-body">
            </div>

            <form class="chat-footer">
                @csrf
                <input type="text" name="outgoingId" value={{ auth()->user()->uid }} hidden>
                <input type="text" name="incomingId" value={{ $user -> uid }} hidden>
                <input type="text" name="sendMessage" id="sendMessage" placeholder="Send Message" class="input-field"
                    autocomplete="off" />
                <button class="btn btn-send"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $(".chat-body").mouseenter(function() {
                $(".chat-body").addClass("active");
            });

            $(".chat-body").mouseleave(function() {
                $(".chat-body").removeClass("active");
            });

            function scrollToBottom() {
                $(".chat-body").scrollTop($(".chat-body").height());
            }

            setInterval(() => {
                $.ajax({
                    url: '{{ route('chat', $user -> uid) }}?t=' + Date.now(),
                    success: function(data) {
                        console.log(data);
                        $('.chat-body').html(data);
                        if (!$(".chat-body").hasClass("active")) {
                            scrollToBottom();
                        }
                    },
                });
            }, 1000);
            
            $('.chat-footer').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('chat.save') }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function() {
                        $('#sendMessage').val('');
                        scrollToBottom();
                    }
                });
            });
        });
    </script>

@endsection
