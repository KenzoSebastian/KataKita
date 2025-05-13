<x-auth-layout title="Register">
  <form class="space-y-6" action="{{ route('post-register') }}" method="POST" id="registerForm" novalidate>
    @csrf
    <div class="flex space-x-4">
      <div class="flex-1">
        <label for="first_name" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">First
          Name</label>
        <input autocomplete="off" value="{{ old('first_name') }}" type="text" id="first_name" name="first_name" required class="{{ $errors->has('first_name') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        @error('first_name')
          <p class="errorMessage">{{ $message }}</p>
        @enderror
      </div>
      <div class="flex-1">
        <label for="last_name" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">Last
          Name</label>
        <input autocomplete="off" value="{{ old('last_name') }}" type="text" id="last_name" name="last_name" required class="{{ $errors->has('last_name') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        @error('last_name')
          <p class="errorMessage">{{ $message }}</p>
        @enderror
      </div>
    </div>
    <div>
      <label for="username" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">Username</label>
      <input autocomplete="off" value="{{ old('username') }}" type="text" id="username" name="username" required class="{{ $errors->has('username') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      @error('username')
        <p class="errorMessage">{{ $message }}</p>
      @enderror
    </div>
    <div>
      <label for="email" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">Email</label>
      <input autocomplete="off" value="{{ old('email') }}" type="email" id="email" name="email" required class="{{ $errors->has('email') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      @error('email')
        <p class="errorMessage">{{ $message }}</p>
      @enderror
    </div>
    <div x-data="{ show: false }">
      <label for="password" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">Password</label>
      <div class="relative">
        <input autocomplete="off" type="password" id="password" name="password" required :type="show ? 'text' : 'password'" class="{{ $errors->has('password') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 pr-10 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <button type="button" @click="show = !show" class="absolute right-2 top-1/2 -translate-y-1/2 transform focus:outline-none">
          <img x-show="!show" src="{{ asset('img/show.png') }}" alt="Show Password" class="h-6 w-6">
          <img x-show="show" src="{{ asset('img/hide.png') }}" alt="Hide Password" class="h-6 w-6">
        </button>
      </div>
      @error('password')
        <p class="errorMessage">{{ $message }}</p>
      @enderror
    </div>
    <button type="submit" class="bg-kata hover:bg-kataDarken w-full cursor-pointer rounded-md py-3 font-semibold text-white transition-colors duration-300">
      Register
    </button>
  </form>

  <script>
    $(document).ready(function() {
      $('#registerForm').submit(function(event) {
        event.preventDefault();
        Swal.fire({
          title: 'Creating Account...',
          text: 'Please wait while we register your account.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,   
          didOpen: () => {
            Swal.showLoading(); // Menampilkan animasi loading
          }
        });
        this.submit();
      })
    })
  </script>
</x-auth-layout>
