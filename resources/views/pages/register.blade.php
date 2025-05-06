<x-auth-layout title="Register">
  <form class="space-y-6" action="{{ route("post-register") }}" method="POST" novalidate>
    @csrf
    <div class="flex space-x-4">
      <div class="flex-1">
        <label for="first_name"
          class="block text-sm tablet:text-base desktop:text-lg font-bold text-gray-700 mb-1">First
          Name</label>
        <input value="{{ old("first_name") }}" type="text" id="first_name" name="first_name"
          required
          class="w-full px-4 py-2 {{ $errors->has("first_name") ? "border-red-500 border-2" : "border border-gray-500" }} rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" />
        @error("first_name")
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
      <div class="flex-1">
        <label for="last_name"
          class="block text-sm tablet:text-base desktop:text-lg font-bold text-gray-700 mb-1">Last
          Name</label>
        <input value="{{ old("last_name") }}" type="text" id="last_name" name="last_name"
          required
          class="w-full px-4 py-2 {{ $errors->has("last_name") ? "border-red-500 border-2" : "border border-gray-500" }} rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" />
        @error("last_name")
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>
    <div>
      <label for="username"
        class="block text-sm tablet:text-base desktop:text-lg font-bold text-gray-700 mb-1">Username</label>
      <input value="{{ old("username") }}" type="text" id="username" name="username" required
        class="w-full px-4 py-2 {{ $errors->has("username") ? "border-red-500 border-2" : "border border-gray-500" }} rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" />
      @error("username")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>
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
          <img x-show="!show" src="{{ asset("img/show.png") }}" alt="Show Password"
            class="w-6 h-6">
          <img x-show="show" src="{{ asset("img/hide.png") }}" alt="Hide Password"
            class="w-6 h-6">
        </button>
      </div>
      @error("password")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>
    <button type="submit"
      class="w-full py-3 bg-kata text-white font-semibold rounded-md hover:bg-kataDarken cursor-pointer transition-colors duration-300">
      Register
    </button>
  </form>
</x-auth-layout>
