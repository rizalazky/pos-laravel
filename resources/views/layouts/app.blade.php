@extends('adminlte::page')

@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('title', 'Dashboard')



@if (isset($header))
    @section('content_header')
    <div class="container-fluid">
        <h1>{{ $header }}</h1>
    </div>
    @stop
@endif

@section('content')
    {{ $slot }}
@stop



@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <!-- <script src="/js/app.js"></script> -->
    @include('sweetalert::alert')
    <!-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> -->
     <script>
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka).replace(/,00$/, ''); // Hilangkan ,00
        }

        document.querySelectorAll('.currency-format').forEach(function(input) {
            // Deteksi elemen input terkait (hidden)
            let hiddenInputName = input.name.replace('_display', '');
            let hiddenInput = document.querySelector(`input[name="${hiddenInputName}"]`);

            // Format ulang saat load
            if (input.value) {
                let raw = input.value.replace(/\D/g, '');
                input.value = formatRupiah(raw);
                if (hiddenInput) hiddenInput.value = raw;
            }

            // Saat diketik
            input.addEventListener('input', function(e) {
                let angka = e.target.value.replace(/\D/g, '');
                e.target.value = formatRupiah(angka);
                if (hiddenInput) hiddenInput.value = angka;
            });
            input.addEventListener('change', function(e) {
                console.log('CHANGE', e);
                let angka = e.target.value.replace(/\D/g, '');
                e.target.value = formatRupiah(angka);
                if (hiddenInput) hiddenInput.value = angka;
            });
        });
    </script>

@stop


