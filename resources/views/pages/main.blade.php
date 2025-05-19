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
<x-layout title="Main Page">
  <div class="desktop:pt-32 container mx-auto flex gap-10 pt-24">
    {{-- navbar --}}
    <x-navbar :profileDefault="auth()->check() && isset($profileDefault) ? $profileDefault : null" :activeUser="auth()->check() ? $activeUser->toArray() : []" :allUser="$allUser->toArray()" />

    {{-- register/profile side --}}
    <div class="desktop:block relative hidden w-1/4">
      @guest
        <div class="w-78 fixed bottom-2 top-32 rounded-2xl bg-cover bg-center px-8 py-10 shadow-2xl" style="background-image: url('https://picsum.photos/200/300');">
          <div class="flex h-full w-full flex-col items-center justify-around rounded-2xl bg-white/25 px-5 backdrop-blur-sm">
            <h1 class="text-shadow-2xs text-5xl font-bold text-white">Don't have an account?</h1>
            <a href="{{ route('register') }}" class="bg-kata hover:bg-kataDarken w-full rounded-md py-3 text-center font-semibold text-white transition-colors duration-300">
              Sign Up
            </a>
          </div>
        </div>
      @endguest

      @auth
        <div class="w-78 fixed bottom-2 top-32 flex flex-col items-center justify-between overflow-hidden rounded-2xl bg-white pb-8 shadow-2xl">
          <div class="h-full w-full text-center">
            <div class="mb-17 relative h-2/5 w-full rounded-t-2xl bg-cover bg-center" style="background-image: url('https://picsum.photos/200/300');">
              <form method="POST" enctype="multipart/form-data" id="changeProfileForm" action="{{ route('update-profile', $activeUser->id) }}" class="desktop:block -bottom-13 absolute left-1/2 mr-5 hidden w-fit -translate-x-1/2 rounded-full shadow transition">
                <label for="changeProfile" class="w-30 h-30 group relative flex cursor-pointer items-center justify-center rounded-full bg-slate-300 text-4xl font-bold shadow-lg transition-all">
                  @if (isset($profileDefault))
                    <p class="transition group-hover:text-black/30">{{ $profileDefault }}</p>
                  @else
                    <img class="h-full w-full rounded-full object-cover" src="{{ asset($activeUser->profile_picture) }}" alt="profile picture">
                  @endif
                  <div class="absolute flex h-full w-full items-center justify-center rounded-full bg-black/40 opacity-0 transition group-hover:opacity-100">
                    <svg class="h-17 w-17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                      <g id="SVGRepo_iconCarrier">
                        <path d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                          d="M3 16.8V9.2C3 8.0799 3 7.51984 3.21799 7.09202C3.40973 6.71569 3.71569 6.40973 4.09202 6.21799C4.51984 6 5.0799 6 6.2 6H7.25464C7.37758 6 7.43905 6 7.49576 5.9935C7.79166 5.95961 8.05705 5.79559 8.21969 5.54609C8.25086 5.49827 8.27836 5.44328 8.33333 5.33333C8.44329 5.11342 8.49827 5.00346 8.56062 4.90782C8.8859 4.40882 9.41668 4.08078 10.0085 4.01299C10.1219 4 10.2448 4 10.4907 4H13.5093C13.7552 4 13.8781 4 13.9915 4.01299C14.5833 4.08078 15.1141 4.40882 15.4394 4.90782C15.5017 5.00345 15.5567 5.11345 15.6667 5.33333C15.7216 5.44329 15.7491 5.49827 15.7803 5.54609C15.943 5.79559 16.2083 5.95961 16.5042 5.9935C16.561 6 16.6224 6 16.7454 6H17.8C18.9201 6 19.4802 6 19.908 6.21799C20.2843 6.40973 20.5903 6.71569 20.782 7.09202C21 7.51984 21 8.0799 21 9.2V16.8C21 17.9201 21 18.4802 20.782 18.908C20.5903 19.2843 20.2843 19.5903 19.908 19.782C19.4802 20 18.9201 20 17.8 20H6.2C5.0799 20 4.51984 20 4.09202 19.782C3.71569 19.5903 3.40973 19.2843 3.21799 18.908C3 18.4802 3 17.9201 3 16.8Z"
                          stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                      </g>
                    </svg>
                  </div>
                </label>
                @csrf
                @method('PATCH')
                <input type="file" class="hidden" name="image" id="changeProfile" accept="image/*" onchange="changeProfileImage(event)">
              </form>
            </div>
            <h2 class="text-lg font-bold">{{ $activeUser->fullname }}</h2>
            <strong class="mb-4 block text-sm font-normal">{{ "@{$activeUser->username}" }}</strong>
            <p class="text-base">{{ $activeUser['bio'] }}</p>
            <div class="mt-8 flex justify-center gap-20">
              <div class="flex flex-col items-center">
                <p class="text-lg font-bold"> {{ $activeUser->followers()->count() }}</p>
                <p class="text-base">Follower</p>
              </div>
              <div class="flex flex-col items-center">
                <p class="text-lg font-bold"> {{ $activeUser->followings()->count() }}</p>
                <p class="text-base">Following</p>
              </div>
            </div>
          </div>
          <a href="#" class="bg-kata hover:bg-kataDarken w-3/4 rounded-md py-3 text-center font-semibold text-white transition-colors duration-300">
            My Profile
          </a>
        </div>
      @endauth
    </div>

    {{-- feed --}}
    <div class="desktop:min-h-[calc(100vh-8.5rem)] desktop:w-3/4 min-h-[calc(100vh-7rem)] w-full overflow-hidden rounded-2xl bg-white p-4 shadow-2xl">
      @auth
        {{-- posting form --}}
        <form action="{{ route('post.store') }}" class="mb-6 flex flex-col gap-1" method="POST" enctype="multipart/form-data">
          @php
            $id = substr(md5(uniqid()), 0, 8);
            $slug = implode('-', str_split(preg_replace('/[\$\/0-9.,]/', '', bcrypt($id)), 10));
          @endphp
          @csrf
          @method('POST')
          <div class="tablet:px-5 tablet:pt-5 {{ $errors->any() ? 'border-red-500 border-2' : 'border-0' }} relative h-auto rounded-xl bg-gray-100 px-3 pb-8 pt-3 shadow-[5px_9px_6px_-1px_rgba(0,0,0,0.30)]">
            <textarea name="content" class="w-full resize-none focus:outline-0" placeholder="What's on your mind?" required>{{ old('content') }}</textarea>
            <input type="file" name="image" id="image" class="hidden" accept="image/*" onchange="previewImage(event)">
            <label id="imageLabel" for="image" class="tablet:left-5 absolute bottom-1 left-3 w-fit cursor-pointer">
              <span class="material-symbols-outlined pl2">Image</span>
            </label>
            <div class="relative w-fit">
              <img id="imagePreview" src="" alt="Image Preview" class="tablet:max-h-95 desktop:max-h-110 mt-4 hidden max-h-80 rounded-lg object-contain object-left" />
              <p id="removeImage" class="absolute -right-8 top-1 hidden cursor-pointer">
                <span class="material-symbols-outlined pl2">close</span>
              </p>
            </div>
          </div>
          <input type="hidden" name="id" value="{{ $id }}">
          <input type="hidden" name="author_id" value="{{ $activeUser->id }}">
          <input type="hidden" name="slug" value="{{ $slug }}">
          @if ($errors->any())
            <p class="errorMessage pl-2">{{ $errors->first() }}</p>
          @endif
          <button class="bg-kata hover:bg-kataDarken mt-5 w-full cursor-pointer rounded-md py-3 text-center font-semibold text-white transition-colors duration-300">Post</button>
        </form>
      @endauth

      {{-- tab feed --}}
      <div x-data="{ activeTab: 'all' }">
        @auth
          <div class="tablet:gap-10 mb-6 flex items-center justify-center gap-7">
            <!-- Tab Following Posts -->
            <span @click="activeTab = 'following'" :class="activeTab === 'following' ? 'bg-kita/50 text-black' : 'bg-kita/5 text-gray-500'" class="tablet:px-8 tablet:py-3 tablet:text-base desktop:text-lg inline-block cursor-pointer rounded-full px-6 py-2 text-sm font-bold transition duration-300 ease-in-out" id="following">
              Following Posts
            </span>

            <!-- Tab All Posts -->
            <span @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-kita/50 text-black' : 'bg-kita/5 text-gray-500'" class="tablet:px-8 tablet:py-3 tablet:text-base desktop:text-lg inline-block cursor-pointer rounded-full px-6 py-2 text-sm font-bold transition duration-300 ease-in-out" id="all">
              All Posts
            </span>
          </div>
        @endauth
        {{-- data posts --}}
        {{-- tab following --}}
        <div x-show="activeTab === 'following'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform translate-x-full" class="transition">
          <template x-if="activeTab === 'following'">
            <div id="followingPostsContainer">
              <x-skeleton-post count=3 />
            </div>
          </template>
        </div>
        {{-- tab allPost --}}
        <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform -translate-x-full" class="transition">
          <template x-if="activeTab === 'all'">
            <div id="allPostsContainer">
              <x-skeleton-post count=5 />
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // ajax allPost
      const ajaxPosts = (route, container) => {
        $.ajax({
          url: route,
          type: "GET",
          success: function(data) {
            const postsContainer = $(`#${container}`);
            postsContainer.empty(); // Clear skeletons

            if (data.error) {
              console.error(data.message);
              data.message === "No posts found" ?
                postsContainer.append('<p class="text-center mt-7 tablet:mt-10 text-gray-500">No posts available.</p>') :
                postsContainer.append('<p class="text-center mt-7 tablet:mt-10 text-gray-500">' + data.message + '</p>');
              Swal.fire({
                toast: true,
                icon: 'error',
                title: data.message,
                timer: 3000,
                position: 'bottom-end',
                showConfirmButton: false,
                background: '#131523',
                color: '#fff',
              });
            } else {
              console.log(data);
              // Update the UI with the fetched posts
              postsContainer.append(data); // Append the fetched posts
            }
          },
          error: function(xhr, status, error) {
            // Handle any errors here
            console.error(error);
            postsContainer.empty(); // Clear skeletons
            postsContainer.append('<p class="text-center mt-7 tablet:mt-10 text-gray-500">' + error + '</p>');
            Swal.fire({
              toast: true,
              icon: 'error',
              title: error,
              timer: 3000,
              position: 'bottom-end',
              showConfirmButton: false,
              background: '#131523',
              color: '#fff',
            });
          }
        })
      }
      // Initial load of all posts
      ajaxPosts("{{ route('post.index') }}", "allPostsContainer");

      //tab allPost
      $("#all").click(() => ajaxPosts("{{ route('post.index') }}", "allPostsContainer"));

      //tab following
      $("#following").click(() => ajaxPosts("{{ route('post.following') }}", "followingPostsContainer"));


      // Event listener for image preview post
      window.previewImage = function(event) {
        const imagePreview = $('#imagePreview');
        const removeImageButton = $('#removeImage');
        const label = $('#imageLabel');
        const file = event.target.files[0];
        if (file) {
          if (!(file.type).includes('image/')) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Invalid file type. Please select an image file.',
            });
            event.target.value = '';
            return;
          }
          const reader = new FileReader();
          reader.onload = function(e) {
            imagePreview.attr('src', e.target.result);
            imagePreview.removeClass('hidden'); // Show the image
            removeImageButton.removeClass('hidden'); // Show the remove button
            label.addClass('hidden'); // Hide the label
          };
          reader.readAsDataURL(file);
        } else {
          imagePreview.addClass('hidden'); // Hide the image if no file is selected
          removeImageButton.addClass('hidden'); // Hide the remove button
          label.removeClass('hidden'); // Show the label
        }
      };

      // event listener for remove image post
      $('#removeImage').click(function() {
        const imagePreview = $('#imagePreview');
        const removeImageButton = $('#removeImage');
        const fileInput = $('#image');

        // Hide the image preview and remove button
        imagePreview.addClass('hidden').attr('src', '');
        removeImageButton.addClass('hidden');
        // Show the label again
        $('#imageLabel').removeClass('hidden');

        // Clear the file input
        fileInput.val('');
      });

      // event listener for changing profile picture
      @auth
      window.changeProfileImage = function(event) {
        const file = event.target.files[0];
        const form = $('#changeProfileForm');
        const activeUser = @json($activeUser);
        if (file) {
          if (!(file.type).includes('image/')) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Invalid file type. Please select an image file.',
            });
            event.target.value = '';
            return;
          }
          const reader = new FileReader();
          reader.onload = function(e) {
            Swal.fire({
              title: "Change Profile Picture",
              html: `
          <div class="flex justify-center items-center gap-5">
            <!-- Gambar Profil Lama -->
            <div class="flex flex-col items-center">
              <p class="text-lg mb-3">Current Profile</p>
              ${activeUser.profile_picture ? `<img src="${activeUser.profile_picture}" alt="Current Profile Picture" class="w-35 h-35 rounded-full shadow-lg object-cover">` : `<div class="w-30 h-30 flex  items-center justify-center rounded-full bg-slate-300 text-4xl font-bold shadow-lg"> {{ isset($profileDefault) ? $profileDefault : '' }}</div>`}
            </div>
            <!-- panah -->
            <div class="translate-y-5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m-7.5-7.5l7.5 7.5-7.5 7.5" />
              </svg>
            </div>
            
            <!-- Gambar Profil Baru -->
            <div class="flex flex-col items-center">
              <p class="text-lg mb-3">New Profile</p>
              <img src="${e.target.result}" alt="New Profile Picture" class="w-30 h-30 rounded-full shadow-lg object-cover">
            </div>
          </div>
        `,
              showCancelButton: true,
              confirmButtonText: "Save",
              cancelButtonText: "Cancel",
              preConfirm: () => {
                Swal.fire({
                  title: 'Updating Profile...',
                  text: 'Please wait while we update your profile.',
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  showConfirmButton: false,
                  didOpen: () => {
                    Swal.showLoading(); // Menampilkan animasi loading
                  }
                });
                form.submit();
              },
            })
          };
          reader.readAsDataURL(file); // Baca file sebagai Data URL
        }
      };
    @endauth

    // Event listener for height adjustment of textarea
    $("textarea").on("input", function() {
      // Reset tinggi textarea untuk menghitung ulang
      $(this).css("height", "auto");
      // Sesuaikan tinggi berdasarkan scrollHeight
      $(this).css("height", this.scrollHeight + "px");
    });

    });
  </script>

</x-layout>
