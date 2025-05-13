@if (session('success'))
  <script>
    Swal.fire({
      toast: true,
      icon: 'success',
      title: '{{ session('success') }}',
      timer: 5000,
      position: 'bottom-end',
      showConfirmButton: false,
      background: '#131523',
      color: '#fff',
    });
  </script>
@endif

@if (session('error'))
  <script>
    Swal.fire({
      toast: true,
      icon: 'error',
      title: '{{ session('error') }}',
      timer: 5000,
      position: 'bottom-end',
      showConfirmButton: false,
      background: '#131523',
      color: '#fff',
    });
  </script>
@endif
