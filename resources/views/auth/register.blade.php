<x-layout pageTitle="Register">
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
          <h2 class="card__header-title">Create Your Account</h2>
          <a href="{{ route('documents.index') }}" class="upload-form__header-button">Back to Document List</a>
        </div>

        <div class="card__body">
          <form action="/register" method="POST" class="upload-form">
            @csrf

            <div class="upload-form__line">
              <label for="first_name" class="upload-form__line-title">First Name:</label>
              <input type="text" name="first_name" id="first_name"
                class="upload-form__line-input {{ $errors->has('first_name') ? 'is-invalid' : '' }}" required minlength="1" maxlength="70"
                value="{{ old('first_name') }}">
              @error('first_name')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Your given name.</small>
            </div>

            <div class="upload-form__line">
              <label for="last_name" class="upload-form__line-title">Last Name:</label>
              <input type="text" name="last_name" id="last_name"
                class="upload-form__line-input {{ $errors->has('last_name') ? 'is-invalid' : '' }}" required minlength="1" maxlength="70"
                value="{{ old('last_name') }}">
              @error('last_name')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Your family name.</small>
            </div>

            <div class="upload-form__line">
              <label for="email" class="upload-form__line-title">Email Address:</label>
              <input type="email" name="email" id="email"
                class="upload-form__line-input {{ $errors->has('email') ? 'is-invalid' : '' }}" required maxlength="70"
                value="{{ old('email') }}">
              @error('email')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Must be unique.</small>
            </div>

            <div class="upload-form__line">
              <label for="password" class="upload-form__line-title">Password:</label>
              <input type="password" name="password" id="password"
                class="upload-form__line-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required minlength="8">
              @error('password')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">At least 8 characters.</small>
            </div>

            <div class="upload-form__line">
              <label for="password_confirmation" class="upload-form__line-title">Confirm Password:</label>
              <input type="password" name="password_confirmation" id="password_confirmation"
                class="upload-form__line-input {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" required>
              @error('password_confirmation')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Re-enter the password to confirm.</small>
            </div>

            <div class="upload-form__line upload-form__line--button">
              <button type="submit" class="upload-form__line-button">Create Account</button>
            </div>

            <div class="upload-form__line">
              <small class="upload-form__line-clarification">
                Already have an account? <a href="/login">Log in</a>
              </small>
            </div>
          </form>
        </div>
      </article>
    </div>
  </div>
  </x-layout>