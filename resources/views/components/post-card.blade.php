@php
  function getProfileDefault($fullname)
  {
      $words = explode(' ', $fullname);
      $firstTwoWords = array_slice($words, 0, 2);
      return implode('', array_map(fn($word) => strtoupper($word[0]), $firstTwoWords));
  }

  foreach ($posts as $post) {
      if ($post->author->profile_picture === null) {
          $post->author->profileDefault = getProfileDefault($post->author->fullname);
      }
      if (isset($activeUser)) {
          $post->likes->pluck('user_id')->contains($activeUser->id) ? ($post->isLikedByActiveUser = true) : ($post->isLikedByActiveUser = false);
      }
  }
@endphp

@foreach ($posts as $post)
  <div class="relative mb-6 rounded-xl bg-gray-100 p-4 shadow-[5px_9px_6px_-1px_rgba(0,0,0,0.30)] hover:bg-gray-200">
    <!-- Header: Profile Picture and Author Info -->
    <div class="flex items-center gap-4">
      <!-- Profile Picture -->
      <div class="w-15 h-15 tablet:text-xl flex items-center justify-center rounded-full bg-slate-300 text-lg font-bold shadow-lg">
        @if (isset($post->author->profileDefault))
          {{ $post->author->profileDefault }}
        @else
          <img class="h-full w-full rounded-full object-cover" src="{{ asset($post->author->profile_picture) }}" alt="profile picture">
        @endif
      </div>
      <!-- Author Info -->
      <a href="{{ route('profile', $post->author->id) }}">
        <h3 class="text-lg font-bold">{{ $post->author->fullname }}</h3>
        <p class="text-sm text-gray-500">{{ '@' . $post->author->username }}</p>
      </a>
    </div>

    <!-- Post Content -->
    <div class="mt-4">
      <p class="text-base text-gray-800">
        {{ Str::limit($post->content, 250) }}
        @if (Str::length($post->content) > 250)
          <a href="{{ route('post.show', $post->slug) }}" class="text-kita hover:underline font-bold">Read more</a>
        @endif
      </p>
      <!-- Post Image -->
      @isset($post->image)
        <img src="{{ asset($post->image) }}" alt="Post Image" class="tablet:max-h-95 desktop:max-h-110 mt-4 max-h-80 rounded-lg object-contain object-left">
      @endisset

    </div>

    <!-- Post Actions: Like and Comment -->
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

        <!-- Comment Button -->
        <div class="flex items-start gap-2">
          <a href="{{ route('post.show', $post->slug) }}" class="hover:text-kita transition">
            <span class="material-symbols-outlined pl2">chat</span>
          </a>
          <p class="text-sm">{{ $post->comments_count }}</p>
        </div>

      </div>
      <!-- Post Timestamp -->
      <span class="block pt-4 text-right text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
    </div>
  </div>
@endforeach
<script>
  $(document).ready(function() {
    // Event listener for like button
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
