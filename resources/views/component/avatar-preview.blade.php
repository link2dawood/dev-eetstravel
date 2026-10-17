@php
    $avatarName = trim($avatarUser->name ?: $avatarUser->email ?: 'User');
    $avatarInitials = collect(preg_split('/\s+/u', $avatarName))->filter()->take(2)->map(function ($part) {
        return mb_strtoupper(mb_substr($part, 0, 1));
    })->implode('');
    $avatarSource = null;
    $avatarCandidates = [$avatarUser->avatar_path ? 'storage/'.$avatarUser->avatar_path : null, $avatarUser->avatar];
    foreach ($avatarCandidates as $avatarCandidate) {
        if (!is_string($avatarCandidate) || $avatarCandidate === '') continue;
        if (preg_match('#^https?://#i', $avatarCandidate)) {
            $avatarSource = $avatarCandidate;
        } elseif (is_file(public_path(ltrim($avatarCandidate, '/')))) {
            $avatarSource = asset(ltrim($avatarCandidate, '/'));
        }
        if ($avatarSource) break;
    }
    $avatarSize = $avatarSize ?? 120;
@endphp
<span class="avatar-preview" role="img" aria-label="{{ $avatarName }} avatar"
      style="position:relative;display:inline-flex;align-items:center;justify-content:center;width:{{ $avatarSize }}px;height:{{ $avatarSize }}px;max-width:100%;border-radius:50%;overflow:hidden;background:#dbeafe;color:#1d4ed8;font-size:2rem;vertical-align:middle;">
    <span class="avatar-initials" aria-hidden="true">{{ $avatarInitials ?: 'U' }}</span>
    @if($avatarSource)
        <img src="{{ $avatarSource }}" alt="" onerror="this.remove()" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
    @endif
</span>
