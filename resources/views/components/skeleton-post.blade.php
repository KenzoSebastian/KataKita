@for ($i = 0; $i < $count; $i++)
  <div class="relative mb-6 rounded-xl bg-gray-100 p-4 shadow-[5px_9px_6px_-1px_rgba(0,0,0,0.30)]">
    <!-- Header: Profile Picture and Author Info -->
    <div class="flex items-center gap-4">
      <!-- Profile Picture -->
      <div class="w-15 h-15 animate-pulse rounded-full bg-slate-300 shadow-lg"></div>
      <!-- Author Info -->
      <div class="mt-2 flex flex-col gap-2">
        <span class="block h-4 w-32 animate-pulse rounded-full bg-gray-300"></span>
        <span class="block h-3 w-24 animate-pulse rounded-full bg-gray-300"></span>
      </div>
    </div>

    <!-- Post Content Skeleton -->
    @php
      $widths = ['w-1/3', 'w-2/5', 'w-1/2', 'w-3/5', 'w-2/3', 'w-3/4', 'w-4/5', 'w-5/6', 'w-full'];
      $skeletonWidths = collect([1, 2, 3, 4])->map(fn() => $widths[array_rand($widths)]);
    @endphp
    <div class="mt-4 flex flex-col gap-2">
      @foreach ($skeletonWidths as $width)
        <span class="{{ $width }} block h-4 animate-pulse rounded-full bg-gray-300"></span>
      @endforeach
    </div>

    <!-- Post Actions Skeleton -->
    <div class="mt-4 flex items-start justify-between">
      <div class="flex justify-start gap-4">
        <!-- Like Button Skeleton -->
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined pl2 animate-pulse select-none text-gray-300">favorite</span>
          <span class="block h-4 w-6 animate-pulse rounded-full bg-gray-300"></span>
        </div>
        <!-- Comment Button Skeleton -->
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined pl2 animate-pulse select-none text-gray-300">chat</span>
          <span class="block h-4 w-6 animate-pulse rounded-full bg-gray-300"></span>
        </div>
      </div>
      <!-- Timestamp Skeleton -->
      <span class="block h-4 w-20 animate-pulse rounded-full bg-gray-300 pt-4"></span>
    </div>
  </div>
@endfor
