@extends('layouts.auth')

@section('title', 'Sign In')

@section('form-content')
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Sign in</h1>
    {{-- Social login icons removed as per request --}}
    <p class="text-sm text-gray-600 mb-8">atau gunakan akun anda</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4">
            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- NIP -->
        <div>
            {{-- <label for="nip" class="block font-medium text-sm text-gray-700">{{ __('NIP') }}</label> --}}
            <input id="nip" class="block mt-1 w-full px-4 py-3 bg-gray-100 border-transparent rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm" type="text" name="nip" value="{{ old('nip') }}" required autofocus autocomplete="username" placeholder="NIP" />
        </div>

        <!-- Password -->
        <div class="relative">
            {{-- <label for="password" class="block font-medium text-sm text-gray-700">{{ __('Password') }}</label> --}}
            <input id="password" class="block mt-1 w-full px-4 py-3 pr-10 bg-gray-100 border-transparent rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
            <span id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye text-sm" id="eyeIcon"></i>
            </span>
        </div>

        <div class="flex items-center justify-between mt-2">
            <span id="forgotPasswordLink" class="text-xs text-gray-600 hover:text-teal-600 cursor-pointer">
                Lupa kata sandi anda?
            </span>
        </div>

        <div>
            <button type="submit" class="w-full mt-6 flex items-center justify-center px-4 py-3 bg-teal-500 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-600 focus:bg-teal-700 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                SIGN IN
            </button>
        </div>
    </form>
@endsection

@section('info-content')
    <h1 class="text-4xl font-bold mb-4">Halo!</h1>
    <p class="text-center text-md mb-8 leading-relaxed">
        silahkan login dan mulai gunakan<br/>layanan kami segera
    </p>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }

        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function() {
                alert('Hubungi admin +62 123 4567 890'); // Replace with actual admin number
            });
        }
    });
</script>
@endpush
