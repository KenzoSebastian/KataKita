@php
  function getProfileDefault($fullname)
  {
      $words = explode(" ", $fullname);
      $firstTwoWords = array_slice($words, 0, 2);
      return implode("", array_map(fn($word) => strtoupper($word[0]), $firstTwoWords));
  }
  if (isset($activeUser)) {
      $profileDefault = getProfileDefault($activeUser->fullname);
  }

  $post->author->profileDefault = getProfileDefault($post->author->fullname);
  if (isset($activeUser)) {
      $post->likes->pluck("user_id")->contains($activeUser->id)
          ? ($post->isLikedByActiveUser = true)
          : ($post->isLikedByActiveUser = false);
  }
@endphp

<x-layout title="Show Post">
  <div class="container mx-auto flex pt-24 desktop:pt-32 gap-10">
    @auth
      <x-navbar :$profileDefault />
    @endauth
    @guest
      <x-navbar />
    @endguest
    {{-- feed --}}
    <div class="w-full min-h-[calc(100vh-7rem)] p-4 bg-white rounded-2xl shadow-2xl">
      <div class="mt-4">
        <div class="relative">
          <a href="{{ route("show-post", $post->slug) }}"
            class="flex gap-3 p-3 tablet:p-5 mb-6 bg-gray-100 rounded-xl hover:bg-gray-200 transition shadow-[5px_9px_6px_-1px_rgba(0,0,0,0.30)]">
            <div
              class="flex items-center justify-center bg-slate-300 shadow-lg rounded-full font-bold text-lg tablet:text-xl w-15 h-15">
              {{ $post->author->profileDefault }}
            </div>
            <div class="flex-1">
              <h3 class="font-bold text-base tablet:text-lg">{{ $post->author->fullname }}</h3>
              <p class="text-gray-500 mb-3 text-sm tablet:text-base">
                {{ "@" . $post->author->username }}</p>
              <p class="text-sm tablet:text-base">{{ $post->content }}</p>
              <span
                class="text-gray-500 text-end block pt-6 tablet:pt-4 text-sm tablet:text-base">{{ $post->created_at->diffForHumans() }}</span>
            </div>
          </a>
          <div class="flex gap-5 w-fit absolute bottom-3 tablet:bottom-5 left-21 tablet:left-23">
            <div class="flex items-center gap-2">
              @guest
                <a href="{{ route("login") }}">
                  <img src="{{ asset("img/loveOutline.png") }}" alt="loveOutline"
                    class="w-5 tablet:w-5.5 desktop:w-6 transition-all hover:scale-110">
                </a>
              @endguest
              @auth
                <form action="{{ route("like-post", $post->slug) }}" method="POST"
                  class="flex items-center gap-2">
                  @csrf
                  @method("POST")
                  <button type="submit" class="w-fit cursor-pointer">
                    @if ($post->isLikedByActiveUser)
                      <span class="material-symbols-outlined text-red-500 like-btn liked">
                        favorite
                      </span>
                    @else
                      <span class="material-symbols-outlined pl2 like-btn unliked">
                        favorite
                      </span>
                    @endif
                  </button>
                  <p class="text-sm tablet:text-base">{{ $post->likes_count }}</p>
                </form>
              @endauth
            </div>
            <div class="flex items-center gap-2">
              <a href="{{ route("login") }}">
                <img src="{{ asset("img/comment.png") }}" alt="comment"
                  class="w-5 tablet:w-5.5 desktop:w-6 transition-all hover:scale-110">
              </a>
              <p class="text-sm tablet:text-base">{{ $post->comments_count }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $(".like-btn").click(function(e) {
        e.preventDefault();

        const form = $(this).closest("form");
        const likeButton = $(this);
        const likeCount = form.find("p");

        if (likeButton.hasClass("liked")) {
          likeButton.removeClass("liked").addClass("unliked");
          likeButton.removeClass("text-red-500").addClass("pl2");
          likeCount.text(parseInt(likeCount.text()) - 1);
        } else {
          likeButton.removeClass("unliked").addClass("liked");
          likeButton.removeClass("pl2").addClass("text-red-500");
          likeCount.text(parseInt(likeCount.text()) + 1);
        }

        $.ajax({
          type: form.attr("method"),
          url: form.attr("action"),
          data: form.serialize(),
          success: function(response) {
            console.log(response);
          },
          error: function(xhr, status, error) {
            console.error(error);
            if (response.liked) {
              likeButton.removeClass("pl2").addClass("text-red-500");
              likeButton.removeClass("unliked").addClass("liked");
              likeCount.text(parseInt(likeCount.text()) + 1);
            } else {
              likeButton.removeClass("text-red-500").addClass("pl2");
              likeButton.removeClass("liked").addClass("unliked");
              likeCount.text(parseInt(likeCount.text()) - 1);
            }
          },
        });
      });
    });
  </script>

</x-layout>
