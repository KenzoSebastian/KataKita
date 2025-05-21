@php
  function getProfileDefault($fullname)
  {
      $words = explode(' ', $fullname);
      $firstTwoWords = array_slice($words, 0, 2);
      return implode('', array_map(fn($word) => strtoupper($word[0]), $firstTwoWords));
  }
  $profileDefault = is_null($user->profile_picture) ? getProfileDefault($user->fullname) : null;
  if (isset($activeUser)) {
      $isSameLikeActiveUser = $user->id === $activeUser->id;
  }
@endphp
<x-layout title="Profile">
  <div class="tablet:pt-6 desktop:pt-7 container mx-auto pt-5">
    <div class="tablet:min-h-[calc(100vh-1.5rem)] desktop:min-h-[calc(100vh-1.75rem)] min-h-[calc(100vh-1.25rem)] w-full overflow-hidden rounded-2xl bg-white shadow-2xl">
      {{-- Banner --}}
      <div class="tablet:h-72 desktop:h-96 relative h-60 w-full rounded-t-2xl border-b-4 border-white bg-cover bg-center" style="background-image: url('{{ $user->banner ? asset($user->banner) : 'https://picsum.photos/900/300' }}');">
        {{-- Back Button --}}
        <a href="{{ route('beranda') }}" class="tablet:w-10 desktop:w-11 desktop:hover:w-21 tablet:hover:w-20 hover:w-18.5 group absolute left-4 top-4 z-30 flex w-9 items-center justify-center overflow-hidden rounded-full bg-black/40 p-2 transition-all duration-300 hover:bg-black/60">
          <svg xmlns="http://www.w3.org/2000/svg" class="tablet:h-6 tablet:w-6 desktop:h-7 desktop:w-7 tablet:translate-x-5 translate-x-4.5 h-5 w-5 shrink-0 text-white transition-all duration-300 group-hover:translate-x-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          <p class="tablet:text-sm ml-2 -translate-x-4 text-xs text-white opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">Back</p>
        </a>
        {{-- Change Banner --}}
        {{-- Only show if the user is logged in and is the same as the active user --}}
        @if (isset($activeUser) && $isSameLikeActiveUser)
          <form method="POST" enctype="multipart/form-data" id="changeBannerForm" action="{{ route('profile.updateBanner', $user->id) }}" class="group absolute inset-0">
            @csrf
            @method('PATCH')
            <label for="changeBanner" class="block h-full w-full cursor-pointer">
              <div class="absolute inset-0 flex items-center justify-center rounded-t-2xl bg-black/40 opacity-0 transition group-hover:opacity-100">
                <svg class="h-22 w-22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
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
              <input type="file" class="hidden" name="banner" id="changeBanner" accept="image/*" onchange="changeBannerImage(event)">
            </label>
          </form>
        @endif
        {{-- Profile Picture --}}
        <div class="tablet:-bottom-20 desktop:-bottom-24 absolute -bottom-16 left-5 z-10">
          @if (isset($activeUser) && $isSameLikeActiveUser)
            <form method="POST" enctype="multipart/form-data" id="changeProfileForm" action="{{ route('profile.updatePhoto', $user->id) }}" class="group relative">
              @csrf
              @method('PATCH')
              <label for="changeProfile" class="block cursor-pointer">
                <div class="tablet:w-40 tablet:h-40 desktop:w-48 desktop:h-48 relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-gray-300 shadow-xl">
                  @if ($user->profile_picture)
                    <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture" class="h-full w-full rounded-full object-cover">
                  @else
                    <div class="tablet:text-5xl flex h-full w-full items-center justify-center rounded-full bg-gray-400 text-4xl font-bold text-white">
                      {{ $profileDefault }}
                    </div>
                  @endif
                  <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 transition group-hover:opacity-100">
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
                </div>
              </label>
              <input type="file" class="hidden" name="image" id="changeProfile" accept="image/*" onchange="changeProfileImage(event)">
            </form>
          @else
            <div class="tablet:w-40 tablet:h-40 desktop:w-48 desktop:h-48 relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-gray-300 shadow-xl">
              @if ($user->profile_picture)
                <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture" class="h-full w-full rounded-full object-cover">
              @else
                <div class="tablet:text-5xl flex h-full w-full items-center justify-center rounded-full bg-gray-400 text-4xl font-bold text-white">
                  {{ $profileDefault }}
                </div>
              @endif
            </div>
          @endif

        </div>
      </div>

      {{-- Profile Info --}}
      <div class="tablet:pl-48 desktop:pl-60 tablet:pt-6 desktop:pt-8 tablet:pr-10 desktop:pr-15 bg-kata relative pb-6 pl-40 pr-5 pt-4 text-white">
        <div class="tablet:flex-row tablet:items-end tablet:justify-between flex flex-col gap-2">
          <div>
            <h2 class="tablet:text-2xl desktop:text-3xl text-lg font-bold">{{ $user->fullname }}</h2>
            <div class="tablet:text-base desktop:text-lg text-xs text-gray-200">{{ '@' . $user->username }}</div>
            <div class="tablet:text-sm desktop:text-base mt-1 flex items-center gap-2 text-xs text-gray-100">
              <span id="userBio" class="tablet:max-w-90 desktop:max-w-120 inline-block max-w-full">{{ $user->bio }}</span>
              @if (isset($activeUser) && $isSameLikeActiveUser)
                <button id="editBioBtn" class="hover:bg-kata cursor-pointer rounded-full p-2 transition-all duration-300" title="Edit Bio" type="button">
                  <svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15px" height="15px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#ffffff" stroke="#ffffff">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                      <path fill="#ffffff"
                        d="M62.828,12.482L51.514,1.168c-1.562-1.562-4.093-1.562-5.657,0.001c0,0-44.646,44.646-45.255,45.255 C-0.006,47.031,0,47.996,0,47.996l0.001,13.999c0,1.105,0.896,2,1.999,2.001h4.99c0.003,0,9.01,0,9.01,0s0.963,0.008,1.572-0.602 s45.256-45.257,45.256-45.257C64.392,16.575,64.392,14.046,62.828,12.482z M37.356,12.497l3.535,3.536L6.95,49.976l-3.536-3.536 L37.356,12.497z M8.364,51.39l33.941-33.942l4.243,4.243L12.606,55.632L8.364,51.39z M3.001,61.995c-0.553,0-1.001-0.446-1-0.999 v-1.583l2.582,2.582H3.001z M7.411,61.996l-5.41-5.41l0.001-8.73l14.141,14.141H7.411z M17.557,60.582l-3.536-3.536l33.942-33.94 l3.535,3.535L17.557,60.582z M52.912,25.227L38.771,11.083l2.828-2.828l14.143,14.143L52.912,25.227z M61.414,16.725l-4.259,4.259 L43.013,6.841l4.258-4.257c0.782-0.782,2.049-0.782,2.829-0.002l11.314,11.314C62.195,14.678,62.194,15.943,61.414,16.725z">
                      </path>
                    </g>
                  </svg>
                </button>
              @endif
            </div>
          </div>

          {{-- Follow --}}
          <div class="tablet:justify-end tablet:mt-0 tablet:flex-col tablet:items-end mt-5 flex flex-row-reverse items-center justify-start gap-5">
            @auth
              @if ($activeUser->id !== $user->id)
                <form method="POST">
                  @csrf
                  @if ($activeUser->followings->contains('following_id', $user->id))
                    <button data-follow="true" type="submit" class="follow-btn following-btn-class">
                      <span class="material-symbols-outlined">check</span>Following
                    </button>
                  @else
                    <button data-follow="false" type="submit" class="follow-btn follow-btn-class">
                      Follow
                    </button>
                  @endif
                  <span class="limiter hidden"></span>
                </form>
              @endif
            @endauth
            <div class="flex w-full justify-start gap-8">
              <a href="{{ route('profile.followers', ['id' => $user->id]) }}" class="flex flex-col items-center">
                <div class="tablet:text-xl desktop:text-2xl text-base font-bold" id="followersCount">{{ $user->followers_count ?? 0 }}</div>
                <div class="tablet:text-sm text-xs">Follower</div>
              </a>
              <a href="{{ route('profile.followings', ['id' => $user->id]) }}" class="flex flex-col items-center">
                <div class="tablet:text-xl desktop:text-2xl text-base font-bold" id="followingsCount">{{ $user->followings_count ?? 0 }}</div>
                <div class="tablet:text-sm text-xs">Following</div>
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Tweet/Post List --}}
      <div class="tablet:px-8 desktop:px-10 min-h-[300px] bg-gray-100 px-2 py-8">
        <h3 class="tablet:text-2xl mb-4 text-xl font-bold" id="profilePostCount">Tweet (0)</h3>
        <div class="flex flex-col gap-6">
          <div id="profilePostsContainer">
            <x-skeleton-post count=5 />
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Fungsi AJAX untuk ambil post user
      $.ajax({
        url: "{{ route('user.posts', ['id' => $user->id]) }}",
        type: "GET",
        success: function(data) {
          const postsContainer = $(`#profilePostsContainer`);
          postsContainer.empty(); // Hapus skeleton

          if (data.error) {
            postsContainer.append('<p class="text-center mt-7 tablet:mt-10 text-gray-500">' + data.message + '</p>');
          } else {
            postsContainer.append(data); // Render post user
            $(`#profilePostCount`).text(`Tweet ({{ $user->posts_count ?? 0 }})`); // Update jumlah tweet
          }
        },
        error: function(xhr, status, error) {
          const postsContainer = $(`#profilePostsContainer`);
          postsContainer.empty();
          postsContainer.append('<p class="text-center mt-7 tablet:mt-10 text-gray-500">' + error + '</p>');
        }
      });

      @auth
      // event listener for changing profile picture
      window.changeProfileImage = function(event) {
        const file = event.target.files[0];
        const form = $('#changeProfileForm');
        const user = @json($user);
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
                  <div class="flex flex-col items-center">
                    <p class="text-lg mb-3">Current Profile</p>
                    ${user.profile_picture ? `<img src="{{ asset($user->profile_picture) }}" alt="Current Profile Picture" class="w-28 h-28 rounded-full shadow-lg object-cover">` : `<div class="w-28 h-28 flex items-center justify-center rounded-full bg-slate-300 text-4xl font-bold shadow-lg">{{ isset($profileDefault) ? $profileDefault : '' }}</div>`}
                  </div>
                  <div class="translate-y-5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m-7.5-7.5l7.5 7.5-7.5 7.5" />
                    </svg>
                  </div>
                  <div class="flex flex-col items-center">
                    <p class="text-lg mb-3">New Profile</p>
                    <img src="${e.target.result}" alt="New Profile Picture" class="w-28 h-28 rounded-full shadow-lg object-cover">
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
                    Swal.showLoading();
                  }
                });
                form.submit();
              },
            })
          };
          reader.readAsDataURL(file);
        }
      };

      // event listener for changing banner
      window.changeBannerImage = function(event) {
        const file = event.target.files[0];
        const form = $('#changeBannerForm');
        const user = @json($user);
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
              title: "Change Banner",
              width: 1000,
              html: `
                <div class="flex flex-col items-center gap-5">
                  <div>
                    <p class="text-lg mb-3">Current Banner</p>
                    <img src="${user.banner ? '{{ asset($user->banner) }}' : 'https://picsum.photos/900/300'}" alt="Current Banner" class="w-250 h-60 tablet:h-125 rounded-xl shadow-lg object-cover">
                  </div>
                  <div>
                    <p class="text-lg mb-3">New Banner</p>
                    <img src="${e.target.result}" alt="New Banner" class="w-250 h-60 tablet:h-125 rounded-xl shadow-lg object-cover">
                  </div>
                </div>
        `,
              showCancelButton: true,
              confirmButtonText: "Save",
              cancelButtonText: "Cancel",
              preConfirm: () => {
                Swal.fire({
                  title: 'Updating Banner...',
                  text: 'Please wait while we update your banner.',
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  showConfirmButton: false,
                  didOpen: () => {
                    Swal.showLoading();
                  }
                });
                form.submit();
              },
            })
          };
          reader.readAsDataURL(file);
        }
      };

      // event listener for editing bio
      $('#editBioBtn').on('click', function() {
        const currentBio = $('#userBio').text().trim();
        Swal.fire({
          title: 'Edit Bio',
          input: 'textarea',
          inputLabel: 'Your Bio',
          inputValue: currentBio,
          inputAttributes: {
            maxlength: 100,
            autocapitalize: 'off',
            autocorrect: 'off'
          },
          showCancelButton: true,
          confirmButtonText: 'Save',
          cancelButtonText: 'Cancel',
          didOpen: () => {
            const textarea = Swal.getInput();
            // Tambahkan counter di bawah textarea
            const counter = $('<div id="bio-char-counter" class="text-xs mr-9 text-right mt-1 text-gray-400"></div>');
            counter.text(`${textarea.value.length}/100`);
            $(textarea).after(counter);

            // Event listener pakai jQuery
            $(textarea).on('input', function() {
              counter.text(`${this.value.length}/100`);
            });
          }
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: 'Updating Bio...',
              text: 'Please wait while we update your bio.',
              allowOutsideClick: false,
              allowEscapeKey: false,
              showConfirmButton: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
            $.ajax({
              url: "{{ route('profile.updateBio', ['id' => $user->id]) }}",
              type: "PATCH",
              data: {
                bio: result.value,
                _token: "{{ csrf_token() }}"
              },
              success: function(res) {
                console.log(res);
                if (res.status === 'success') {
                  $('#userBio').text(res.bio);
                  Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Bio updated successfully',
                    timer: 5000,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    background: '#131523',
                    color: '#fff',
                  });
                } else {
                  Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: res.message,
                    timer: 5000,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    background: '#131523',
                    color: '#fff',
                  });
                }
              },
              error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to update bio', 'error');
              }
            });
          }
        });
      });

      // event listener for follow/unfollow
      $('.follow-btn').click(function(e) {
        e.preventDefault();
        const btn = $(this);
        const form = btn.closest('form');
        const followClass = "follow-btn follow-btn-class";
        const followingClass = "follow-btn following-btn-class";
        const isNotFollowing = btn.data("follow");
        const url = isNotFollowing ? "{{ route('profile.unfollow', $user->id) }}" : "{{ route('profile.follow', $user->id) }}";
        btn.removeAttr("class").addClass(isNotFollowing ? followClass : followingClass)
          .html(isNotFollowing ? "Follow" : `<span class="material-symbols-outlined">check</span>Following`);
        $(".limiter").removeClass("hidden").addClass("inline-block");
        $.ajax({
          type: "POST",
          url: url,
          data: form.serialize(),
          success: function(res) {
            if (res.status === 'error') {
              btn.removeClass(isNotFollowing ? followClass : followingClass)
                .addClass(isNotFollowing ? followingClass : followClass)
                .html(isNotFollowing ?
                  `<span class="material-symbols-outlined">check</span>Following` :
                  "Follow");
              Swal.fire({
                toast: true,
                icon: 'error',
                title: res.message,
                timer: 5000,
                position: 'bottom-end',
                showConfirmButton: false,
                background: '#131523',
                color: '#fff'
              });
            } else {
              btn.data("follow", !isNotFollowing);
              $(`#followersCount`).text(res.followers_count);
              Swal.fire({
                toast: true,
                icon: 'success',
                title: res.message,
                timer: 5000,
                position: 'bottom-end',
                showConfirmButton: false,
                background: '#131523',
                color: '#fff'
              });
            }
            $(".limiter").removeClass("inline-block").addClass("hidden");
          },
          error: function() {
            Swal.fire({
              toast: true,
              icon: 'error',
              title: 'An error occurred.',
              timer: 3000,
              position: 'bottom-end',
              showConfirmButton: false,
              background: '#131523',
              color: '#fff'
            });
            $(".limiter").removeClass("inline-block").addClass("hidden");
          }
        });
      });
    @endauth
    });
  </script>
</x-layout>
