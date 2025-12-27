{{-- Footer Menu Rendering View --}}
<ul class="list-unstyled footer-nav">
    @foreach($items as $item)
        @if($item->type !== 'divider')
            <li>
                <a href="{{ $item->getUrl() }}" target="{{ $item->target }}" class="{{ $item->css_class ?? '' }}">
                    @if($item->icon)<i class="{{ $item->icon }} me-1"></i>@endif
                    {{ $item->title }}
                </a>
            </li>
        @endif
    @endforeach
</ul>
