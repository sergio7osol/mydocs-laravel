<x-layout :pageTitle="$pageTitle" :users="$users" :currentUserId="$currentUserId" :currentCategory="$currentCategory">
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
          <h2 class="card__header-title">Upload New Document</h2>
          <a href="{{ route('documents.index') }}" class="upload-form__header-button">Back to Document List</a>
        </div>

        <div class="card__body">
          <form action="/documents" class="upload-form" enctype="multipart/form-data" method="POST">
            @csrf

            <input type="hidden" name="user_id" value="{{ $currentUserId }}">

            <div class="upload-form__line">
              <label for="title" class="upload-form__line-title">Document Title:</label>
              <input type="text" name="title" id="title"
                class="upload-form__line-input {{ $errors->has('title') ? 'is-invalid' : '' }}" required maxlength="70"
                value="{{ old('title', $document->title ?? '') }}">
              @error('title')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">The title of the document being uploaded.</small>
            </div>

            <div class="upload-form__line upload-form__line--file">
              <label for="document" class="upload-form__line-title">Select document to upload:</label>
              <div class="upload-form__line-input">
                <input type="file" name="document" id="document"
                  class="{{ $errors->has('document') ? 'is-invalid' : '' }}" {{ !isset($document) ? 'required' : '' }}
                  accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx">

                <div class="upload-form__line-formats">
                  <span class="upload-form__line-format">PDF</span>
                  <span class="upload-form__line-format">DOC</span>
                  <span class="upload-form__line-format">DOCX</span>
                  <span class="upload-form__line-format">TXT</span>
                  <span class="upload-form__line-format">XLS/XLSX</span>
                  <span class="upload-form__line-format">PPT/PPTX</span>
                  <span class="upload-form__line-size">Max: 15MB</span>
                </div>
                @error('document')
                <div class="upload-form__error-message">{{ $message }}</div>
                @enderror
              </div>
              <small class="upload-form__line-clarification">Upload a document in supported formats (max 15MB).</small>
            </div>

            <div class="upload-form__line upload-form__line--select">
              <label for="category_id" class="upload-form__line-title">Category:</label>
              <select name="category_id" id="category_id"
                class="upload-form__line-input {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required>
                @foreach ($categories as $id => $name)
                <option value="{{ $id }}" {{ old('category_id', $currentCategory ?? '') == $id ? 'selected' : '' }}>
                  {{ $name }}
                </option>
                @endforeach
              </select>
              @error('category_id')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
            </div>

            <div class="upload-form__line">
              <label for="created_date" class="upload-form__line-title">Document Date (optional):</label>
              <input type="date" name="created_date" id="created_date"
                class="upload-form__line-input {{ $errors->has('created_date') ? 'is-invalid' : '' }}"
                value="{{ old('created_date', $document->created_date ?? '') }}">
              @error('created_date')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
            </div>

            <!-- Description -->
            <div class="upload-form__line upload-form__line--textarea">
              <label for="description" class="upload-form__line-title">Description (optional):</label>
              <textarea name="description" id="description"
                class="upload-form__line-input {{ $errors->has('description') ? 'is-invalid' : '' }}"
                maxlength="300">{{ old('description', $document->description ?? '') }}</textarea>
              @error('description')
              <div class="upload-form__error-message">{{ $message }}</div>
              @enderror
              <small class="upload-form__line-clarification">Brief description of the document (maximum 300
                characters)</small>
            </div>

            <!-- Submit Button -->
            <div class="upload-form__line upload-form__line--button">
              <button type="submit" class="upload-form__line-button">Upload Document</button>
            </div>
          </form>
        </div>
      </article>
    </div>
  </div>
</x-layout>