<x-auth-layout title="Login">
  @if (session("error"))
  <div
    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm tablet:text-base desktop:text-lg mb-4">
    <strong class="font-bold">Error!</strong>
    <span class="block tablet:inline">{{ session("error") }}</span>
  </div>
  @endif
  <form class="space-y-6" action="{{ route("post-login") }}" method="POST" novalidate>
    @csrf
    <div>
      <label for="email"
        class="block text-sm tablet:text-base desktop:text-lg font-bold text-gray-700 mb-1">Email</label>
      <input value="{{ old("email") }}" type="email" id="email" name="email" required
        class="w-full px-4 py-2 {{ $errors->has("email") ? "border-red-500 border-2" : "border border-gray-500" }} rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" />
      @error("email")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>
    <div x-data="{ show: false }">
      <label for="password"
        class="block text-sm tablet:text-base desktop:text-lg font-bold text-gray-700 mb-1">Password</label>
      <div class="relative">
        <input type="password" id="password" name="password" required
          :type="show ? 'text' : 'password'"
          class="w-full px-4 py-2 {{ $errors->has("password") ? "border-red-500 border-2" : "border border-gray-500" }} rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 pr-10" />
        <button type="button" @click="show = !show"
          class="absolute right-2 top-1/2 transform -translate-y-1/2 focus:outline-none">
          <img x-show="!show" src="{{ asset("img/show.png") }}" alt="Show Password" class="w-6 h-6">
          <img x-show="show" src="{{ asset("img/hide.png") }}" alt="Hide Password" class="w-6 h-6">
        </button>
      </div>
      @error("password")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>
    <button type="submit"
      class="w-full py-3 bg-kata text-white font-semibold rounded-md hover:bg-kataDarken cursor-pointer transition-colors duration-300">
      Login
    </button>
  </form>
</x-auth-layout>
