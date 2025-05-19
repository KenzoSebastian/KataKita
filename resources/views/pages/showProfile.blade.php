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
@endphp
<x-layout title="Profile">
  <div class="container mx-auto min-h-[calc(100vh)] w-full rounded-2xl bg-white p-4 shadow-2xl">
    hai
  </div>
</x-layout>
