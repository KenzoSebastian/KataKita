@php
  function getProfileDefault($fullname)
  {
      $words = explode(' ', $fullname);
      $firstTwoWords = array_slice($words, 0, 2);
      return implode('', array_map(fn($word) => strtoupper($word[0]), $firstTwoWords));
  }
@endphp

@if ($length > 0)
  <ul>
    @foreach ($list as $item)
      @php
        $profileDefault = $item->profile_picture ? null : getProfileDefault($item->fullname);
      @endphp
      <li class="flex items-center gap-3 border-b py-3 last:border-b-0">
        <a href="{{ route('profile', $item->id) }}">
          @if ($item->profile_picture)
            <img src="{{ asset($item->profile_picture) }}" class="h-10 w-10 rounded-full object-cover" alt="{{ $item->fullname }}">
          @else
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-300 text-lg font-bold text-white">{{ $profileDefault }}</div>
          @endif
        </a>
        <div>
          <a href="{{ route('profile', $item->id) }}" class="font-semibold hover:underline">{{ $item->fullname }}</a>
          <div class="text-xs text-gray-400">@ {{ $item->username }}</div>
        </div>
      </li>
    @endforeach
  </ul>
@else
  <div class="py-8 text-center text-gray-400">
    No {{ $type === 'followers' ? 'followers' : 'following' }} found.
  </div>
@endif
