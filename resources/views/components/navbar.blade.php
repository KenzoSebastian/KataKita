<nav class="desktop:px-7 fixed left-0 right-0 top-0 z-10 flex w-full justify-between bg-white px-1 py-3 shadow">
  <a href="{{ route('beranda') }}" class="desktop:text-5xl font-jolly mr-2 flex items-center text-2xl">
    <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-15 desktop:w-24 desktop:mr-4 mr-1.5">
    <div class="desktop:flex-row flex flex-col">
      <span class="text-kata desktop:mr-3">Kata</span>
      <span class="text-kita">Kita</span>
    </div>
  </a>
  <div class="desktop:w-3/5 flex w-3/4 items-center justify-between">
    <form action="{{ route('beranda', ['search' => request('search')]) }}" class="relative w-full" id="searchForm">
      <input type="text" id="searchInput" class="bg-light/75 shadow-desktop desktop:py-2 desktop:w-5/6 h-full w-full rounded-full py-1.5 pl-11 pr-4" placeholder="Search..." name="search" autocomplete="off">
      <div class="desktop:top-1.5 desktop:bottom-1.5 desktop:w-8 absolute bottom-1 left-3 top-1 w-6">
        <span class="material-symbols-outlined">search</span>
      </div>
      <!-- Suggestion Box -->
      <div id="suggestionBox" class="absolute z-10 mt-1 hidden w-full rounded-lg bg-white shadow-lg">
        <!-- Suggestions will be dynamically added here -->
      </div>
    </form>
    @guest
      <a href="{{ route('login') }}" class="bg-kita hover:bg-kitaDarken desktop:block mr-4 hidden rounded-lg px-7 py-1.5 text-white shadow transition">
        Login</a>
    @endguest

    <div x-data="{ open: false }" class="tablet:ml-3 desktop:ml-5 desktop:hidden relative ml-1">
      <button @click="open = !open" class="flex h-10 w-10 cursor-pointer flex-col items-center justify-between p-2">
        <!-- Bar 1 -->
        <span :class="open ? 'rotate-45 translate-y-2.5' : ''" class="block h-1 w-8 origin-center rounded-full bg-gray-800 transition-transform duration-300 ease-in-out"></span>
        <!-- Bar 2 -->
        <span :class="open ? 'opacity-0' : ''" class="block h-1 w-8 rounded-full bg-gray-800 transition-all duration-300 ease-in-out"></span>
        <!-- Bar 3 -->
        <span :class="open ? '-rotate-45 -translate-y-2.5' : ''" class="block h-1 w-8 origin-center rounded-full bg-gray-800 transition-transform duration-300 ease-in-out"></span>
      </button>
      <div x-show="open" @click.away="open = false" x-transition.scale.origin.top class="absolute right-0 top-12 z-20 w-48 overflow-hidden rounded-lg bg-slate-100 shadow-lg">
        @guest
          <a href="{{ route('login') }}" class="block px-6 py-4 text-gray-800 transition hover:bg-gray-200">Login</a>
        @endguest

        @auth
          <a href="#" class="flex items-start px-6 py-4 text-gray-800 transition hover:bg-gray-200">
            <div class="mr-1 inline-block">
              <span class="material-symbols-outlined">person</span>
            </div>
            Profile
          </a>
          <form action="{{ route('logout') }}" method="POST" id="logoutFormMobile">
            @csrf
            <button type="submit" class="flex w-full cursor-pointer items-start px-6 py-4 text-start text-gray-800 transition hover:bg-gray-200">
              <div class="mr-1 inline-block">
                <span class="material-symbols-outlined">logout</span>
              </div>
              Logout
            </button>
          </form>
        @endauth
      </div>
    </div>
    @auth
      <div class="flex items-center">
        <a href="#" class="hover:bg-light desktop:block mr-5 hidden rounded-full shadow transition">
          <div class="bg-light/75 flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold shadow-lg">
            @if (isset($profileDefault))
              {{ $profileDefault }}
            @else
              <img class="h-full w-full rounded-full object-cover" src="{{ asset($activeUser['profile_picture']) }}" alt="profile picture">
            @endif
          </div>
        </a>
        <form action="{{ route('logout') }}" method="POST" id="logoutFormDesktop">
          @csrf
          <button type="submit" class="desktop:block hidden cursor-pointer rounded-lg bg-red-500 px-7 py-1.5 font-bold text-white shadow transition hover:bg-red-700">
            Logout</button>
        </form>
      </div>
    @endauth
  </div>
</nav>

<script>
  $(document).ready(function() {
    const allUser = @json($allUser); // Data dari server

    // Event listener untuk input search
    $('#searchInput').on('input', function() {
      const query = $(this).val().toLowerCase(); // Ambil input dan ubah ke lowercase
      const suggestionBox = $('#suggestionBox');

      // Kosongkan suggestion box setiap kali input berubah
      suggestionBox.empty();

      // Jika input kosong, sembunyikan suggestion box
      if (query === '') {
        suggestionBox.addClass('hidden');
        return;
      }

      // Filter data berdasarkan input
      const filteredUsers = allUser.filter(user =>
        user.fullname.toLowerCase().includes(query) || user.username.toLowerCase().includes(query)
      );

      // Tambahkan hasil pencarian ke suggestion box
      filteredUsers.forEach(user => {
        suggestionBox.append(`
          <a href="/profile/${user.id}" class="block px-4 py-2 w-full bg-white hover:bg-gray-100 cursor-pointer suggestion-item" data-username="${user.username}">
            <p class="font-bold text-gray-800">${user.fullname}</p>
            <p class="text-sm text-gray-500">@${user.username}</p>
          </a>
        `);
      });

      // Tampilkan suggestion box
      suggestionBox.removeClass('hidden');
    });

    // Event listener untuk klik pada suggestion item
    $(document).on('click', '.suggestion-item', function() {
      const username = $(this).data('username');
      $('#searchInput').val(username); // Isi input dengan username yang dipilih
      $('#suggestionBox').addClass('hidden'); // Sembunyikan suggestion box
    });

    // Sembunyikan suggestion box jika klik di luar
    $(document).on('click', function(e) {
      if (!$(e.target).closest('#searchInput, #suggestionBox').length) {
        $('#suggestionBox').addClass('hidden');
      }
    });

    // Event listener untuk submit form
    $('#searchForm').on('submit', function(e) {
      e.preventDefault(); // Cegah submit default
      const query = $('#searchInput').val().toLowerCase(); // Ambil input
      const matchedUser = allUser.find(user =>
        user.fullname.toLowerCase().includes(query) || user.username.toLowerCase().includes(query)
      );

      if (matchedUser) {
        // Redirect ke halaman profile jika user ditemukan
        window.location.href = `/profile/${matchedUser.id}`;
      } else {
        // Tampilkan toast jika user tidak ditemukan
        Swal.fire({
          toast: true,
          icon: 'error',
          title: 'User not found',
          timer: 5000,
          position: 'bottom-end',
          showConfirmButton: false,
          background: '#131523',
          color: '#fff',
        });

        // Bersihkan text field
        $('#searchInput').val('');
      }
    });

    const alertLogout = (formLogout) => {
      const logoutButton = formLogout.find('button');
      // Event listener untuk tombol logout
      logoutButton.click(function(e) {
        e.preventDefault(); // Cegah submit default
        Swal.fire({
          title: "Logout",
          text: "Are you sure you want to logout?",
          icon: "info",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#fb2c36 ",
          confirmButtonText: "Yes"
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: 'Processing...',
              text: 'Please wait while we logout.',
              allowOutsideClick: false,
              allowEscapeKey: false,
              showConfirmButton: false,
              didOpen: () => {
                Swal.showLoading(); // Menampilkan animasi loading
              }
            });
            formLogout.submit();
          }
        })
      });
    }

    alertLogout($('#logoutFormDesktop'));
    alertLogout($('#logoutFormMobile'));

  });
</script>
