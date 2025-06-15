@props([
'users' => [],
'currentUserId' => null,
'userDocCounts' => [],
'currentCategory' => null,
]);

<header class="main-header">
    <h1><a href="/" class="main-header__title">MyDocs Document Management</a></h1>
    <div class="user-selector">
        <span class="user-status">{{ isset($_SESSION['user']) ? ($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) : 'Guest' }}</span>

        @if(!empty($_SESSION) && $_SESSION['user'] ?? false)
        <div class="user-avatar">
            <div class="user-avatar__image-container">
                <img src="/img/user-avatar-256x256.jpg" alt="User Avatar" class="user-avatar__image">
            </div>
        </div>
        <form action="/sessions" method="POST">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="auth-links__link">Log Out</button>
        </form>
        @else
        <ul class="auth-links">
            <li><a href="/sessions" class="auth-links__link {{request()->is('/sessions') ? 'auth-links__link--active' : ''}}">Log In</a></li>
            <li><a href="/register" class="auth-links__link {{request()->is('/register') ? 'auth-links__link--active' : ''}}">Register</a></li>
        </ul>

        @endif

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

