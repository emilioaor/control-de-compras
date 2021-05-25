@if(! isset($show) || $show)
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ $label }}
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            @foreach($items as $item)
                @if(! isset($item['show']) || $item['show'])
                    <a class="dropdown-item" href="{{ $item['route'] }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </li>
@endif
