@props([
'users' => [],
'currentUserId' => null,
'userDocCounts' => [],
'currentCategory' => null,
])

<header class="main-header">
    <h1><a href="{{route('documents.index')}}" class="main-header__title">MyDocs Document Management</a></h1>
    <div class="user-selector">
        <span class="user-status">
            @auth
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            @else
                Guest
            @endauth
        </span>

        @auth
        <div class="user-avatar">
            <div class="user-avatar__image-container">
                <img src="/img/avatar.svg" alt="User Avatar" class="user-avatar__image">
            </div>
        </div>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="auth-links__link">Log Out</button>
        </form>
        @endauth

        @guest
        <ul class="auth-links">
            <li><a href="/login" class="auth-links__link {{request()->is('login') ? 'auth-links__link--active' : ''}}">Log In</a></li>
            <li><a href="/register" class="auth-links__link {{request()->is('register') ? 'auth-links__link--active' : ''}}">Register</a></li>
        </ul>
        @endguest

        @foreach ($users as $user)
        <div class="{{ ($currentUserId == $user['id']) ? 'active' : '' }}">
            <a href="/?route=list&user_id={{ $user['id'] }} {{ isset($currentCategory) && !empty($currentCategory) ? '&category=' . htmlspecialchars($currentCategory) : '' }}" id="user-{{ $user['id'] }}" class="user-button">
                <span>{{ htmlspecialchars($user['first_name']) }}</span>
                <span class="user-button__icon">
                    {{ $userDocCounts[$user['id']] ?? 0 }}
                </span>
            </a>
        </div>
        @endforeach
    </div>
</header>

