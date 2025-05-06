<nav
  class="fixed flex top-0 left-0 w-full right-0 z-10 bg-white shadow px-1 desktop:px-7 py-3 justify-between">
  <a href="{{ route("beranda") }}"
    class="flex items-center text-2xl desktop:text-5xl font-jolly mr-2">
    <img src="{{ asset("img/logo.png") }}" alt="logo"
      class="w-15 desktop:w-24 mr-1.5 desktop:mr-4">
    <div class="flex flex-col desktop:flex-row">
      <span class="text-kata desktop:mr-3">Kata</span>
      <span class="text-kita">Kita</span>
    </div>
  </a>
  <div class="w-3/4 desktop:w-3/5 flex justify-between items-center">
    <form action="" class="w-full relative">
      <input type="text"
        class="h-full bg-light/75 shadow-desktop rounded-full pl-11 pr-4 py-1.5 desktop:py-2 w-full desktop:w-5/6"
        placeholder="Search...">
      <img src="{{ asset("img/search.svg") }}"
        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-6 desktop:w-8" alt="">
    </form>
    @guest
      <a href="{{ route("login") }}"
        class="bg-kita text-white px-7 py-1.5 rounded-lg mr-4 transition shadow hover:bg-kitaDarken hidden desktop:block">
        Login</a>
    @endguest

    <div x-data="{ open: false }" class="relative ml-1 tablet:ml-3 desktop:ml-5 desktop:hidden">
      <button @click="open = !open"
        class="w-10 h-10 flex flex-col justify-between items-center p-2 cursor-pointer">
        <!-- Bar 1 -->
        <span :class="open ? 'rotate-45 translate-y-2.5' : ''"
          class="block w-8 h-1 bg-gray-800 transition-transform duration-300 ease-in-out rounded-full origin-center"></span>
        <!-- Bar 2 -->
        <span :class="open ? 'opacity-0' : ''"
          class="block w-8 h-1 bg-gray-800 transition-all duration-300 ease-in-out rounded-full"></span>
        <!-- Bar 3 -->
        <span :class="open ? '-rotate-45 -translate-y-2.5' : ''"
          class="block w-8 h-1 bg-gray-800 transition-transform duration-300 ease-in-out rounded-full origin-center"></span>
      </button>
      <div x-show="open" @click.away="open = false" x-transition.scale.origin.top
        class="absolute top-12 right-0 bg-slate-100 shadow-lg rounded-lg w-48 overflow-hidden z-20">
        @guest
          <a href="{{ route("login") }}"
            class="block text-gray-800 hover:bg-gray-200 transition px-6 py-4 ">Login</a>
        @endguest

        @auth
          <a href="#"
            class="block text-gray-800 hover:bg-gray-200 transition px-6 py-4">
            <img class="inline-block w-5 mr-1" src="{{ asset("img/profile.png") }}" alt="profile">
            Profile
          </a>
          <form action="{{ route("logout") }}" method="POST">
            @csrf
            <button type="submit"
              class="w-full text-start text-gray-800 hover:bg-gray-200 transition px-6 py-4 cursor-pointer">
              <img class="inline-block w-4 mr-1" src="{{ asset("img/logout.png") }}" alt="logout">
              Logout
            </button>
          </form>
        @endauth
      </div>
    </div>
    @auth
      <div class="flex items-center">
        <a href="#"
          class="rounded-full mr-5 transition shadow hover:bg-light hidden desktop:block">
          <div
            class="w-12 h-12 flex items-center justify-center bg-light/75 shadow-lg rounded-full font-bold text-lg">
            {{ $profileDefault }}
          </div>
        </a>
        <form action="{{ route("logout") }}" method="POST">
          @csrf
          <button type="submit"
            class="bg-red-500 cursor-pointer text-white font-bold px-7 py-1.5 rounded-lg transition shadow hover:bg-red-700 hidden desktop:block">
            Logout</button>
        </form>
      </div>
    @endauth
  </div>
</nav>
