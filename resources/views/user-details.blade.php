<x-layout :pageTitle="$pageTitle">
    <div class="register-content">
        <h1>User details</h1>
        <p>This is the user details page for the MyDocs application.</p>

        <ul>
            <li>{{ $user['email'] }}</li>
            <li>{{ $user['first_name'] }}</li>
            <li>{{ $user['last_name'] }}</li>
        </ul>
    </div>
</x-layout>

