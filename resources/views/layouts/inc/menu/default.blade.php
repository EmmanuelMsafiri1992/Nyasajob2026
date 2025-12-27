{{-- Default Menu Rendering View --}}
@foreach($items as $item)
    @if($item->type === 'divider')
        <li class="nav-item dropdown-divider d-none d-md-block"></li>
    @elseif($item->type === 'text')
        <li class="nav-item nav-text">
            @if($item->icon)<i class="{{ $item->icon }}"></i> @endif
            <span>{{ $item->title }}</span>
        </li>
    @elseif($item->isDropdown() && isset($item->children) && $item->children->isNotEmpty())
        {{-- Dropdown item --}}
        <li class="nav-item dropdown no-arrow open-on-hover">
            <a href="#" class="dropdown-toggle nav-link {{ $item->css_class ?? '' }}" data-bs-toggle="dropdown">
                @if($item->icon)<i class="{{ $item->icon }}"></i> @endif
                <span>{{ $item->title }}</span>
                <i class="fa-solid fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu shadow-sm">
                @foreach($item->children->filter(fn($child) => $child->isVisibleToCurrentUser()) as $child)
                    @if($child->type === 'divider')
                        <li class="dropdown-divider"></li>
                    @else
                        <li class="dropdown-item">
                            <a href="{{ $child->getUrl() }}" target="{{ $child->target }}">
                                @if($child->icon)<i class="{{ $child->icon }}"></i> @endif
                                {{ $child->title }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
    @elseif($item->type === 'button')
        {{-- Button item --}}
        <li class="nav-item">
            <a class="btn btn-block btn-border btn-listing {{ $item->css_class ?? '' }}"
               href="{{ $item->getUrl() }}"
               target="{{ $item->target }}"
            >
                @if($item->icon)<i class="{{ $item->icon }}"></i> @endif
                {{ $item->title }}
            </a>
        </li>
    @else
        {{-- Regular link item --}}
        <li class="nav-item">
            <a href="{{ $item->getUrl() }}" class="nav-link {{ $item->css_class ?? '' }}" target="{{ $item->target }}">
                @if($item->icon)<i class="{{ $item->icon }}"></i> @endif
                {{ $item->title }}
            </a>
        </li>
    @endif
@endforeach
