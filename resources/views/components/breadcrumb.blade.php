@props(['items'])

{{--
    $items: array of ['label' => '...', 'url' => '...'] — last item has no url.
--}}
<nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-6 flex-wrap">
    @foreach($items as $i => $item)
        @if(!$loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-indigo-600 transition">{{ $item['label'] }}</a>
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
        @else
            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
