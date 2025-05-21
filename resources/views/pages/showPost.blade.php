@php
  function getProfileDefault($fullname)
  {
      $words = explode(' ', $fullname);
      $firstTwoWords = array_slice($words, 0, 2);
      return implode('', array_map(fn($word) => strtoupper($word[0]), $firstTwoWords));
  }
  if (isset($activeUser) && $activeUser->profile_picture === null) {
      $profileDefault = getProfileDefault($activeUser->fullname);
  }

  if ($post->author->profile_picture === null) {
      $post->author->profileDefault = getProfileDefault($post->author->fullname);
  }
  if (isset($activeUser)) {
      $post->likes->pluck('user_id')->contains($activeUser->id) ? ($post->isLikedByActiveUser = true) : ($post->isLikedByActiveUser = false);
  }
  $post->comments->each(function ($comment) {
      if ($comment->user->profile_picture === null) {
          $comment->user->profileDefault = getProfileDefault($comment->user->fullname);
      }
  });

@endphp

<x-layout title="Show Post">
  <div class="desktop:pt-32 container mx-auto flex gap-10 pt-24">
    <x-navbar :profileDefault="auth()->check() && isset($profileDefault) ? $profileDefault : null" :activeUser="auth()->check() ? $activeUser->toArray() : []" :allUser="$allUser->toArray()" />

    {{-- feed --}}
    <div class="desktop:min-h-[calc(100vh-8.5rem)] min-h-[calc(100vh-7rem)] w-full rounded-2xl bg-white p-4 shadow-2xl">
      <div class="relative mb-6 rounded-xl bg-gray-100 p-4 shadow-[5px_9px_6px_-1px_rgba(0,0,0,0.30)]">
        <!-- Header: Profile Picture and Author Info -->
        <div class="tablet:justify-start tablet:gap-4 flex items-center justify-between gap-2">
          <!-- Profile Picture -->
          <a href="{{ route('profile', $post->author->id) }}" class="w-15 h-15 tablet:text-xl flex items-center justify-center rounded-full bg-slate-300 text-lg font-bold shadow-lg">
            @if (isset($post->author->profileDefault))
              {{ $post->author->profileDefault }}
            @else
              <img class="h-full w-full rounded-full object-cover" src="{{ asset($post->author->profile_picture) }}" alt="profile picture">
            @endif
          </a>
          <!-- Author Info -->
          <a href="{{ route('profile', $post->author->id) }}">
            <h3 class="text-lg font-bold">{{ $post->author->fullname }}</h3>
            <p class="text-sm text-gray-500">{{ '@' . $post->author->username }}</p>
          </a>
          {{-- following button --}}
          @auth
            @if ($activeUser->id !== $post->author->id)
              <form method="POST" class="relative inline-block">
                @csrf
                @if ($activeUser->followings->contains('following_id', $post->author->id))
                  <button data-follow=true type="submit" class="follow-btn following-btn-class"><span class="material-symbols-outlined">
                      check
                    </span>Following</button>
                @else
                  <button data-follow=false type="submit" class="follow-btn follow-btn-class">Follow</button>
                @endif
                <span class="limiter hidden"></span>
              </form>
            @endif
          @endauth
        </div>

        <!-- Post Content -->
        <div class="mt-4">
          <p class="text-base text-gray-800">{{ $post->content }}</p>
          <!-- Post Image -->
          @isset($post->image)
            <img src="{{ asset($post->image) }}" alt="Post Image" class="tablet:max-h-95 desktop:max-h-110 mt-4 max-h-80 rounded-lg object-contain object-left">
          @endisset

        </div>

        <!-- Post Actions: Like -->
        <div class="mt-4 flex items-start justify-between">
          <div class="flex justify-start gap-4">
            <!-- Like Button -->
            <div class="flex items-start gap-2">
              @guest
                <a href="{{ route('login') }}" class="hover:text-red-500">
                  <span class="material-symbols-outlined pl2">favorite</span>
                </a>
              @endguest
              @auth
                <form action="{{ route('post.like', $post->slug) }}" method="POST" class="relative">
                  @csrf
                  @method('POST')
                  <button type="submit" class="cursor-pointer">
                    @if ($post->isLikedByActiveUser)
                      <span class="material-symbols-outlined like-btn liked text-red-500">favorite</span>
                    @else
                      <span class="material-symbols-outlined like-btn unliked pl2">favorite</span>
                    @endif
                  </button>
                  <span class="limiter hidden"></span>
                </form>
              @endauth
              <p class="text-sm">{{ $post->likes_count }}</p>
            </div>

          </div>
          <!-- Post Timestamp -->
          <span class="block pt-4 text-right text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
        </div>
      </div>
      <!-- Comments Form -->
      @auth
        <form action="{{ route('post.comment', $post->slug) }}" id="commentform" method="POST" class="my-10">
          @csrf
          @method('POST')
          <input type="hidden" value="{{ $post->id }}" name="post_id">
          <input type="hidden" value="{{ $activeUser->id }}" name="user_id">
          <div class="{{ $errors->any() ? 'items-start' : 'items-center' }} flex gap-4">
            <a href="{{ route('profile', $activeUser->id) }}" class="w-15 h-15 tablet:text-xl flex items-center justify-center rounded-full bg-slate-300 text-lg font-bold shadow-lg">
              @if (isset($profileDefault))
                <p class="transition group-hover:text-black/30">{{ $profileDefault }}</p>
              @else
                <img class="h-full w-full rounded-full object-cover" src="{{ asset($activeUser->profile_picture) }}" alt="profile picture">
              @endif
            </a>
            <div class="flex-1">
              <input type="text" name="content" placeholder="Write a comment..." class="{{ $errors->any() ? 'border-red-500 border-2' : 'border border-gray-300' }} w-full rounded-xl p-2 focus:outline-none focus:ring focus:ring-black" required autocomplete="off">
              @if ($errors->any())
                <p class="errorMessage">{{ $errors->first() }}</p>
              @endif
            </div>
            <button type="submit" class="bg-kata hover:bg-kataDarken cursor-pointer rounded-xl px-4 py-2 text-white">Comment</button>
          </div>
        </form>
      @endauth

      <!-- Comments Section -->
      <div class="px-4">
        <h3 class="text-lg font-bold">Comments ({{ $post->comments_count }})</h3>
        @if ($post->comments_count > 0)
          @foreach ($post->comments as $comment)
            <div class="mt-4 flex gap-4 rounded-xl bg-gray-50 p-4 shadow-[5px_9px_6px_-1px_rgba(0,0,0,0.30)] hover:bg-gray-100">
              <a href="{{ route('profile', $comment->user->id) }}" class="w-15 h-15 tablet:text-xl flex shrink-0 items-center justify-center rounded-full bg-slate-300 text-lg font-bold shadow-lg">
                @if (isset($comment->user->profileDefault))
                  {{ $comment->user->profileDefault }}
                @else
                  <img class="h-full w-full rounded-full object-cover" src="{{ asset($comment->user->profile_picture) }}" alt="profile picture">
                @endif
              </a>
              <div class="flex-1">
                <a class="block w-fit" href="{{ route('profile', $comment->user->id) }}">
                  <h3 class="w-fit text-lg font-bold">{{ $comment->user->fullname }}</h3>
                  <p class="text-sm text-gray-500">{{ '@' . $comment->user->username }}</p>
                </a>
                <p class="mt-4 text-base text-gray-800">{{ $comment->content }}</p>
                <span class="block pt-1 text-end text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
              </div>
            </div>
          @endforeach
        @else
          <p class="text-sm text-gray-500">No comments yet.</p>
        @endif
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      //follower button
      $(".follow-btn").click(function(e) {
        e.preventDefault();
        const btn = $(this),
          form = btn.closest("form");
        const followClass = "follow-btn follow-btn-class";
        const followingClass = "follow-btn following-btn-class";
        const isNotFollowing = btn.data("follow");
        const url = isNotFollowing ? "{{ route('profile.unfollow', $post->author->id) }}" : "{{ route('profile.follow', $post->author->id) }}";
        btn.removeAttr("class").addClass(isNotFollowing ? followClass : followingClass)
          .html(isNotFollowing ? "Follow" : `<span class="material-symbols-outlined">check</span>Following`);
        $(".limiter").removeClass("hidden").addClass("inline-block");
        $.ajax({
          type: form.attr("method"),
          url,
          data: form.serialize(),
          success: res => {
            if (res.status === 'error') {
              btn.removeClass(isNotFollowing ? followClass : followingClass)
                .addClass(isNotFollowing ? followingClass : followClass)
                .html(isNotFollowing ?
                  `<span class="material-symbols-outlined">check</span>Following` :
                  "Follow");
              Swal.fire({
                toast: true,
                icon: 'error',
                title: res.message,
                timer: 5000,
                position: 'bottom-end',
                showConfirmButton: false,
                background: '#131523',
                color: '#fff'
              });
            } else {
              btn.data("follow", !isNotFollowing);
              Swal.fire({
                toast: true,
                icon: 'success',
                title: res.message,
                timer: 5000,
                position: 'bottom-end',
                showConfirmButton: false,
                background: '#131523',
                color: '#fff'
              });
            }
            $(".limiter").removeClass("inline-block").addClass("hidden");
          },
          error: () => {
            Swal.fire({
              toast: true,
              icon: 'error',
              title: 'An error occurred while processing your request.',
              timer: 5000,
              position: 'bottom-end',
              showConfirmButton: false,
              background: '#131523',
              color: '#fff'
            });
            $(".limiter").removeClass("inline-block").addClass("hidden");
          }
        });
      });

      //comment button
      const formComment = $("#commentform");
      const commentButton = formComment.find("button");
      commentButton.click(function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Processing...',
          text: 'Please wait while we process your comment.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading(); // Menampilkan animasi loading
          }
        });
        formComment.submit();
      });

      // Like Button
      $(".like-btn").click(function(e) {
        e.preventDefault();
        const form = $(this).closest("form");
        const likeButton = $(this);
        const likeCount = form.siblings("p");
        $(".limiter").removeClass("hidden").addClass("inline-block");

        if (likeButton.hasClass("liked")) {
          likeButton.removeClass("text-red-500").addClass("pl2");
          likeCount.text(parseInt(likeCount.text()) - 1);
        } else {
          likeButton.removeClass("pl2").addClass("text-red-500");
          likeCount.text(parseInt(likeCount.text()) + 1);
        }

        $.ajax({
          type: form.attr("method"),
          url: form.attr("action"),
          data: form.serialize(),
          success: function(response) {
            console.log(response);

            // Jika ada error dari server
            if (response.error) {
              Swal.fire({
                toast: true,
                icon: 'error',
                title: response.message,
                timer: 5000,
                position: 'bottom-end',
                showConfirmButton: false,
                background: '#131523',
                color: '#fff',
              });

              // Balikkan perubahan UI jika error
              if (likeButton.hasClass("liked")) {
                likeButton.removeClass("pl2").addClass("text-red-500");
                likeCount.text(parseInt(likeCount.text()) + 1);
              } else {
                likeButton.removeClass("text-red-500").addClass("pl2");
                likeCount.text(parseInt(likeCount.text()) - 1);
              }
            } else {
              // Tidak ada error, toggle state
              if (likeButton.hasClass("liked")) {
                likeButton.removeClass("liked").addClass("unliked");
              } else {
                likeButton.removeClass("unliked").addClass("liked");
              }
            }

            $(".limiter").removeClass("inline-block").addClass("hidden");
          },
        });
      });
    });
  </script>

</x-layout>
