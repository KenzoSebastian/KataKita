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

  foreach ($posts as $post) {
      $post->author->profileDefault = getProfileDefault($post->author->fullname);
      if (isset($activeUser)) {
          $post->likes->pluck("user_id")->contains($activeUser->id)
              ? ($post->isLikedByActiveUser = true)
              : ($post->isLikedByActiveUser = false);
      }
  }
@endphp

<x-layout title="Main Page">
  <div class="container mx-auto flex pt-24 desktop:pt-32 gap-10">
    @auth
      <x-navbar :$profileDefault />
    @endauth
    @guest
      <x-navbar />
    @endguest
    {{-- register --}}
    <div class="w-1/4 relative desktop:block hidden">
      @guest
        <div class="bg-cover bg-center px-8 py-10 rounded-2xl shadow-2xl w-78 fixed top-32 bottom-2"
          style="background-image: url('https://picsum.photos/200/300');">
          <div
            class="flex flex-col items-center w-full justify-around h-full px-5 bg-white/25 backdrop-blur-sm rounded-2xl">
            <h1 class="text-5xl font-bold text-white text-shadow-2xs">Don't have an account?</h1>
            <a href="{{ route("register") }}"
              class="w-full py-3 bg-kata text-white font-semibold rounded-md hover:bg-kataDarken transition-colors duration-300 text-center">
              Sign Up
            </a>
          </div>
        </div>
      @endguest

      @auth
        <div
          class="bg-white overflow-hidden rounded-2xl shadow-2xl w-78 fixed top-32 bottom-2 flex flex-col items-center justify-between pb-8">
          <div class="h-full w-full text-center">
            <div class="h-2/5 w-full mb-17 rounded-t-2xl relative bg-cover bg-center"
              style="background-image: url('https://picsum.photos/200/300');">
              <div
                class="rounded-full mr-5 w-fit transition shadow hidden desktop:block absolute -bottom-13 left-1/2 -translate-x-1/2">
                <div
                  class="w-30 h-30 flex items-center justify-center bg-slate-300 shadow-lg rounded-full font-bold text-4xl">
                  {{ $profileDefault }}
                </div>
              </div>
            </div>
            <h2 class="font-bold text-lg">{{ $activeUser->fullname }}</h2>
            <strong class="font-normal text-sm block mb-4">{{ "@" . $activeUser->username }}</strong>
            <p class="text-base ">{{ $activeUser["bio"] }}</p>
            <div class="flex justify-center gap-20 mt-8">
              <div class="flex flex-col items-center">
                <p class="font-bold text-lg"> {{ $activeUser->followers()->count() }}</p>
                <p class="text-base">Follower</p>
              </div>
              <div class="flex flex-col items-center">
                <p class="font-bold text-lg"> {{ $activeUser->followings()->count() }}</p>
                <p class="text-base">Following</p>
              </div>
            </div>
          </div>
          <a href="#"
            class="w-3/4 py-3 bg-kata text-white font-semibold rounded-md hover:bg-kataDarken transition-colors duration-300 text-center">
            My Profile
          </a>
        </div>
      @endauth
    </div>
    {{-- feed --}}
    <div class="w-full desktop:w-3/4 p-4 bg-white rounded-2xl shadow-2xl">
      <div class="mt-4">
        @foreach ($posts as $post)
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
                <p class="text-sm tablet:text-base">{{ Str::limit($post->content, 250) }}
                  @if (Str::length($post->content) > 250)
                    <span class="font-bold">Read more</span>
                  @endif
                </p>
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
        @endforeach
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
