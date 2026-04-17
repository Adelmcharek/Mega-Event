@props([
    'id',
    'title' => 'Are you sure?',
])

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    x-on:open-modal.window="if ($event.detail === '{{ $id }}') open = true"
    x-cloak
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[200] flex items-center justify-center bg-black/50 p-4"
        style="display: none;"
        @click.self="open = false"
    >
        <div
            x-show="open"
            x-transition
            class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
            role="dialog"
            aria-modal="true"
            @click.stop
        >
            <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
            <div class="mt-2 text-sm text-slate-600">{{ $slot }}</div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="open = false">Cancel</button>
                @isset($confirm)
                    {{ $confirm }}
                @endisset
            </div>
        </div>
    </div>
</div>
