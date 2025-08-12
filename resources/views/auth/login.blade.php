<x-layout pageTitle="Login">
  <div class="upload-box">
    <div class="upload-form__container">
      @if ($errors->any())
      <div class="upload-form__alert upload-form__alert--danger">
        <div class="upload-form__general-error">There were some errors with your submission. Please check the fields below.</div>
      </div>
      @endif

      @if (session('message'))
      <div class="upload-form__alert upload-form__alert--success">
        {{ session('message') }}
      </div>
      @endif

      <article class="card">
        <div class="card__header">
          <h2 class="card__header-title">Login to Your Account</h2>
          <a href="{{ route('documents.index') }}" class="upload-form__header-button">Back to Document List</a>
        </div>

        <div class="card__body">
          <form action="/login" method="POST" class="upload-form">
            @csrf

            <div class="upload-form__line">
              <label for="email" class="upload-form__line-title">Email Address:</label>
              <input type="email" name="email" id="email"
                class="upload-form__line-input {{ $errors->has('email') ? 'is-invalid' : '' }}" required maxlength="70"
                value="{{ old('email') }}">
              @error('email')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Enter your registered email address.</small>
            </div>

            <div class="upload-form__line">
              <label for="password" class="upload-form__line-title">Password:</label>
              <input type="password" name="password" id="password"
                class="upload-form__line-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required>
              @error('password')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Enter your password.</small>
            </div>

            <div class="upload-form__line upload-form__line--button">
              <button type="submit" class="upload-form__line-button">Login</button>
            </div>

            <div class="upload-form__line">
              <small class="upload-form__line-clarification">
                Don't have an account? <a href="/register">Create one</a>
              </small>
            </div>
          </form>
        </div>
      </article>
    </div>
  </div>
</x-layout>
