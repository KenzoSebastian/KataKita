@php
  function getProfileDefault($fullname)
  {
      $words = explode(' ', $fullname);
      $firstTwoWords = array_slice($words, 0, 2);
      return implode('', array_map(fn($word) => strtoupper($word[0]), $firstTwoWords));
  }
@endphp

<x-layout :title="$title">
  <div class="container mx-auto pt-10">
    <div class="relative mx-auto max-w-3xl rounded-2xl bg-white p-8 shadow-2xl">
      <a href="{{ route('profile', ['id' => $user->id]) }}" class="tablet:w-10 desktop:w-11 desktop:hover:w-21 tablet:hover:w-20 hover:w-18.5 group absolute left-4 top-4 z-30 flex w-9 items-center justify-center overflow-hidden rounded-full bg-black/40 p-2 transition-all duration-300 hover:bg-black/60">
        <svg xmlns="http://www.w3.org/2000/svg" class="tablet:h-6 tablet:w-6 desktop:h-7 desktop:w-7 tablet:translate-x-5 translate-x-4.5 h-5 w-5 shrink-0 text-white transition-all duration-300 group-hover:translate-x-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <p class="tablet:text-sm ml-2 -translate-x-4 text-xs text-white opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">Back</p>
      </a>
      <h2 class="mb-2 text-center text-2xl font-bold">{{ $user->fullname }}</h2>
      <div class="mb-6 text-center text-gray-500">@ {{ $user->username }}</div>
      <div>
        <div class="mb-6 flex justify-center gap-6">
          <a href="{{ route('profile.followers', $user->id) }}" id="followersTab" class="{{ $title === 'Followers' ? 'bg-kita/50 text-black' : 'bg-kita/5 text-gray-500' }} rounded-full px-6 py-2 font-semibold transition">
            Followers
          </a>
          <a href="{{ route('profile.followings', $user->id) }}" id="followingsTab" class="{{ $title === 'Following' ? 'bg-kita/50 text-black' : 'bg-kita/5 text-gray-500' }} rounded-full px-6 py-2 font-semibold transition">
            Following
          </a>
        </div>

        {{-- Skeleton & Data Container --}}
        <div id="followSkeleton">
          @for ($i = 0; $i < 5; $i++)
            <div class="flex animate-pulse items-center gap-3 py-3">
              <div class="h-10 w-10 rounded-full bg-slate-200"></div>
              <div class="flex-1">
                <div class="mb-2 h-4 w-4/5 rounded bg-slate-200"></div>
                <div class="h-3 w-3/4 rounded bg-slate-100"></div>
              </div>
            </div>
          @endfor
        </div>
        <div id="followData"></div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      function loadFollowData(type) {
        $('#followData').empty();
        $('#followSkeleton').show();
        let url = type === 'followers' ?
          "{{ route('profile.followers.data', $user->id) }}" :
          "{{ route('profile.followings.data', $user->id) }}";
        $.ajax({
          url: url,
          type: "GET",
          success: function(html) {
            $('#followSkeleton').hide();
            $('#followData').html(html);
          },
          error: function(xhr, status, error) {
            console.error(xhr);
            console.error(status);
            console.error(error);
            $('#followSkeleton').hide();
            $('#followData').html('<div class="py-8 text-center text-gray-400">Failed to load data.</div>');
          }
        });
      }

      // Initial load
      loadFollowData("{{ strtolower($title) === 'followers' ? 'followers' : 'followings' }}");

      // Tab click
      $('#followersTab').click(function(e) {
        e.preventDefault();
        window.history.replaceState(null, '', $(this).attr('href'));
        $(this).addClass('bg-kita/50 text-black').removeClass('bg-kita/5 text-gray-500');
        $('#followingsTab').removeClass('bg-kita/50 text-black').addClass('bg-kita/5 text-gray-500');
        loadFollowData('followers');
      });
      $('#followingsTab').click(function(e) {
        e.preventDefault();
        window.history.replaceState(null, '', $(this).attr('href'));
        $(this).addClass('bg-kita/50 text-black').removeClass('bg-kita/5 text-gray-500');
        $('#followersTab').removeClass('bg-kita/50 text-black').addClass('bg-kita/5 text-gray-500');
        loadFollowData('followings');
      });
    });
  </script>
</x-layout>
