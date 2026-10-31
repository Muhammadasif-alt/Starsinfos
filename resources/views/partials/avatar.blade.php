<span class="av">
    @if ($user->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="" loading="lazy">
    @else
        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
    @endif
</span>
