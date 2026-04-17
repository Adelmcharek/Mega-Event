@php
    $flashes = collect([
        ['type' => 'success', 'msg' => session('success')],
        ['type' => 'error', 'msg' => session('error')],
        ['type' => 'warning', 'msg' => session('warning')],
        ['type' => 'info', 'msg' => session('info')],
    ])->filter(fn ($f) => filled($f['msg']));
@endphp
@if ($flashes->isNotEmpty())
    <div
        class="fixed right-4 top-4 z-[100] max-w-md space-y-2"
        x-data="{
            items: @json($flashes->values()),
            dismiss(i) { this.items.splice(i, 1) },
            init() {
                setTimeout(() => { this.items = [] }, 6000)
            }
        }"
        x-cloak
    >
        <template x-for="(item, index) in items" :key="index">
            <div
                x-show="true"
                x-transition
                class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg"
                :class="{
                    'border-emerald-500/40 bg-emerald-950 text-emerald-50': item.type === 'success',
                    'border-red-500/40 bg-red-950 text-red-50': item.type === 'error',
                    'border-amber-500/40 bg-amber-950 text-amber-50': item.type === 'warning',
                    'border-blue-500/40 bg-slate-900 text-slate-50': item.type === 'info'
                }"
            >
                <p class="flex-1 text-sm font-medium" x-text="item.msg"></p>
                <button type="button" class="opacity-70 hover:opacity-100" @click="dismiss(index)" aria-label="Dismiss">&times;</button>
            </div>
        </template>
    </div>
@endif
