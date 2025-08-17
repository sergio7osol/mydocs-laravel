<x-layout :pageTitle="$pageTitle">
    <div class="content category-manager">
        @if (session('message'))
            <div class="upload-form__alert upload-form__alert--success">
                {{ session('message') }}
            </div>
        @endif

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
            <h1 class="page-title">Categories management</h1>
            <a class="btn btn--primary btn--add" href="{{ route('categories.create') }}">➕ New Category</a>
        </div>

        <table class="table">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Active</th>
                    <th scope="col">Display Order</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $c)
                    <tr>
                        <td>
                            <span style="padding-left: {{ max(0, (int)($c['level'] ?? 0)) * 16 }}px">{{ $c['name'] }}</span>
                        </td>
                        <td>{{ $c['is_active'] ? 'Yes' : 'No' }}</td>
                        <td>{{ $c['display_order'] }}</td>
                        <td style="display:flex; align-items:center; gap:6px;">
                            <a class="btn btn--sm btn--secondary" href="{{ route('categories.show', $c['id']) }}">View</a>
                            <a class="btn btn--sm btn--secondary" href="{{ route('categories.edit', $c['id']) }}">Edit</a>
                            <a class="btn btn--sm btn--primary" href="{{ route('categories.create', ['parent_id' => $c['id']]) }}">Add child</a>
                            <form action="{{ route('categories.destroy', $c['id']) }}" method="POST" onsubmit="return confirm(this.getAttribute('data-confirm'));" data-confirm="Delete category: {{ e($c['name']) }}? This action cannot be undone.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn--sm btn--danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
