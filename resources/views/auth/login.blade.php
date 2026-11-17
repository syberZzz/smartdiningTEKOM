@extends('layouts.app')

@section('title', 'Login — Smart Dining')

@push('styles')
<style>
    .login-bg {
        background: linear-gradient(135deg, #0B1926 0%, #162E47 50%, #1B3856 100%);
        min-height: 100vh;
    }

    .login-card {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .login-input {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        transition: all 0.3s ease;
    }

    .login-input:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.05);
    }

    .login-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .login-btn {
        background: linear-gradient(135deg, #E0C17E 0%, #C5A059 100%);
        color: #14293E;
        transition: all 0.3s ease;
    }

    .login-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
    }

    .login-btn:active {
        transform: translateY(0);
    }

    .floating-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.04);
        animation: float 8s ease-in-out infinite;
    }

    .floating-shape:nth-child(2) {
        animation-delay: -3s;
        animation-duration: 10s;
    }

    .floating-shape:nth-child(3) {
        animation-delay: -5s;
        animation-duration: 12s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-30px) rotate(10deg); }
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="login-bg flex items-center justify-center px-4 py-8 relative overflow-hidden">

    {{-- Floating Decorations --}}
    <div class="floating-shape w-72 h-72 -top-20 -left-20" style="position: absolute;"></div>
    <div class="floating-shape w-96 h-96 -bottom-32 -right-32" style="position: absolute;"></div>
    <div class="floating-shape w-48 h-48 top-1/3 right-10" style="position: absolute;"></div>

    <div class="w-full max-w-md relative z-10 fade-in">

        {{-- Logo & Title --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white rounded-2xl p-2 mx-auto mb-4 shadow-lg shadow-black/20 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Smart Dining Logo" class="w-full h-full object-contain" />
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">Smart Dining</h1>
            <p class="text-sm text-white/50">Masuk ke dashboard Anda</p>
        </div>

        {{-- Login Card --}}
        <div class="login-card rounded-3xl p-8">

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-[18px] text-red-400" style="font-variation-settings: 'FILL' 1;">error</span>
                        <p class="text-sm font-medium text-red-400">Login gagal</p>
                    </div>
                    <p class="text-xs text-red-300/80 ml-[26px]">{{ $errors->first('email') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium text-white/60 mb-2 uppercase tracking-wider">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-white/30">mail</span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="nama@smartdining.com"
                            class="login-input w-full pl-12 pr-4 py-3.5 rounded-xl text-sm"
                        />
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-medium text-white/60 mb-2 uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-white/30">lock</span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="login-input w-full pl-12 pr-4 py-3.5 rounded-xl text-sm"
                        />
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-white/20 bg-white/5 text-[#C5A059] focus:ring-[#C5A059]/20 focus:ring-offset-0" />
                        <span class="text-xs text-white/50 group-hover:text-white/70 transition-colors">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="login-btn w-full py-3.5 rounded-xl text-sm font-bold uppercase tracking-wider flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Masuk
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-white/25 mt-6">
            &copy; {{ date('Y') }} Smart Dining. All rights reserved.
        </p>

    </div>
</div>
@endsection
