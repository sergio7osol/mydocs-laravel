<x-layout :pageTitle="$pageTitle">
    <div class="content">
        @if ($errors->any())
            <div class="upload-form__alert upload-form__alert--error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="content-header">
            <h1>Create Category</h1>
            <a class="btn btn--secondary" href="{{ route('categories.index') }}">← Back to list</a>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="upload-form">
            @csrf

            <div class="upload-form__line">
                <label for="name" class="upload-form__line-title">Name:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="upload-form__line-input" required>
            </div>

            <div class="upload-form__line upload-form__line--select">
                <label for="parent_id" class="upload-form__line-title">Parent:</label>
                <select id="parent_id" name="parent_id" class="upload-form__line-input">
                    <option value="">— Root —</option>
                    @foreach ($parents as $p)
                        <option value="{{ $p->id }}" {{ (string) old('parent_id', $selectedParentId ?? '') === (string) $p->id ? 'selected' : '' }}>
                            {{ str_repeat('— ', (int) $p->level) . $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="upload-form__line">
                <span class="upload-form__line-title">Active:</span>
                <input type="hidden" name="is_active" value="0">
                <label class="checkbox">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}> Active
                </label>
            </div>

            <div class="upload-form__line">
                <label for="display_order" class="upload-form__line-title">Display Order:</label>
                <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" class="upload-form__line-input" step="1">
            </div>

            <div class="upload-form__actions">
                <button type="submit" class="btn btn--primary">Save</button>
                <a href="{{ route('categories.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layout>
