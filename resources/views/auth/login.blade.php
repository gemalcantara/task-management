@extends('layouts.app')

@section('content')
    <div class="login-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card  shadow">
                    <div class="card-header">{{ __('Register') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <h3 class="card-title">Task Management</h3>
                            <div class="row mb-3">
                                <div class=" mb-3">
                                    <div class="form-floating">
                                        <input id="email" type="email"
                                            class="form-control @error('email') 
                                is-invalid @enderror"
                                            id="floatingInput" name="email" value="{{ old('email') }}"
                                            placeholder="Email address" required autocomplete="email" autofocus>
                                        <label for="floatingInput">{{ __('Email address') }}</label>
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="floatingPassword" placeholder="Password" name="password" required
                                            autocomplete="current-password">
                                        <label for="floatingPassword">{{ __('Password') }}</label>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
        
                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-footer text-body-secondary">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" type="submit" type="button">Login</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
