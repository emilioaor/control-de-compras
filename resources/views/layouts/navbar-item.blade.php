@if(! isset($show) || $show)
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ $label }}
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            @foreach($items as $item)
                <a class="dropdown-item" href="{{ $item['route'] }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </li>
@endif
