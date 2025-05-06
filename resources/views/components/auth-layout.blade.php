<x-layout :$title>
  <div
    class="flex flex-col desktop:flex-row w-full min-h-screen shadow-lg rounded-lg overflow-hidden bg-white">
    <div
      class="desktop:flex-1 {{ $title != "Register" ? "desktop:order-1" : "" }} bg-cover bg-center px-15 py-5"
      style="background-image: url('https://picsum.photos/200/300');">
      <div
        class="flex items-center w-full justify-center h-50 tablet:h-65 desktop:h-full desktop:pl-15 bg-white/25 backdrop-blur-sm rounded-2xl">
        <h1
          class="text-[8vw] tablet:text-[10vw] desktop:text-[7vw] font-bold text-white text-shadow-2xs">
          {{ $title == "Register" ? "Let's Get started!" : "Welcome Back!" }}</h1>
      </div>
    </div>

    <div class="container mx-auto p-6 flex-1">
      <a href="{{ route("beranda") }}"
        class="flex items-center justify-center flex-col desktop:flex-row desktop:justify-start text-3xl tablet:text-4xl desktop:text-5xl font-jolly mb-5 w-fit">
        <img src="{{ asset("img/logo.png") }}" alt="logo"
          class="w-20 tablet:w-24 desktop:w-28 desktop:mr-4">
        <div class="flex">
          <span class="text-kata mr-2">Kata</span>
          <span class="text-kita">Kita</span>
        </div>
      </a>
      <h3 class="text-2xl desktop:text-3xl font-black mb-6">{{ $title }}</h3>

      {{ $slot }}

      <p class="mt-6 text-center text-sm text-gray-600">
        {{ $title == "Register" ? "Already have an account?" : "Don't have an account?" }}
        <a href="{{ $title == "Register" ? route("login") : route("register") }}"
          class="text-kata hover:underline font-bold">{{ $title == "Register" ? "Sign in" : "Sign up" }}</a>
      </p>
    </div>
  </div>
</x-layout>
