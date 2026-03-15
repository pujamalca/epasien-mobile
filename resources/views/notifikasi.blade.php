@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Notifikasi</div>
    </div>
    <div class="page-content">
        @if(empty($notifs))
        <div class="state-empty">
            <div class="state-icon">🔔</div>
            <div>Belum ada notifikasi</div>
        </div>
        @else
        <div class="card" style="padding:0;overflow:hidden;">
            @foreach($notifs as $notif)
            @php
                $act = $notif['act'] ?? '';
                $href = $act ? route('menu.dispatch', ['act' => $act]) : '#';
            @endphp
            <a href="{{ $href }}" style="display:block;text-decoration:none;color:var(--color-text);padding:14px 16px;border-bottom:1px solid var(--color-border);">
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <span style="font-size:20px;flex-shrink:0;">🔔</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;margin-bottom:3px;">{{ $notif['title'] }}</div>
                        <div style="font-size:13px;color:var(--color-text-muted);margin-bottom:4px;">{{ $notif['body'] }}</div>
                        <div style="font-size:11px;color:var(--color-text-muted);opacity:.7;">
                            {{ \Carbon\Carbon::parse($notif['sent_at'])->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
