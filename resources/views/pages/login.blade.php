<x-auth-layout title="Login">
  <form class="space-y-6" action="{{ route('post-login') }}" method="POST" id="loginForm" novalidate>
    @csrf
    <div>
      <label for="email" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">Email</label>
      <input value="{{ old('email') }}" type="email" id="email" name="email" required class="{{ $errors->has('email') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" autocomplete="off" />
      @error('email')
        <p class="errorMessage">{{ $message }}</p>
      @enderror
    </div>
    <div x-data="{ show: false }">
      <label for="password" class="tablet:text-base desktop:text-lg mb-1 block text-sm font-bold text-gray-700">Password</label>
      <div class="relative">
        <input type="password" id="password" name="password" required :type="show ? 'text' : 'password'" class="{{ $errors->has('password') ? 'border-red-500 border-2' : 'border border-gray-500' }} w-full rounded-md px-4 py-2 pr-10 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" autocomplete="off" />
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
      Login
    </button>
  </form>

  <script>
    $(document).ready(function() {
      $('#loginForm').submit(function(event) {
        event.preventDefault();
        Swal.fire({
          title: 'Logging in...',
          text: 'Please wait while we log you in.',
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
