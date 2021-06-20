<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <!-- Left Side Of Navbar -->
    <ul class="navbar-nav mr-auto">
        @auth
            @include('layouts.navbar-item', [
                    'label' => __('menu.management'),
                    'show' => Auth::user()->isAdmin() || Auth::user()->isBuyer(),
                    'items' => [
                        [
                            'show' => Auth::user()->isAdmin(),
                            'label' => __('menu.addUser'),
                            'route' => route('user.create'),
                        ],
                        [
                            'show' => Auth::user()->isAdmin(),
                            'label' => __('menu.users'),
                            'route' => route('user.index'),
                        ],
                        [
                            'show' => Auth::user()->isAdmin(),
                            'label' => __('menu.addProduct'),
                            'route' => route('product.create'),
                        ],
                        [
                            'show' => Auth::user()->isAdmin(),
                            'label' => __('menu.products'),
                            'route' => route('product.index'),
                        ],
                        [
                            'label' => __('menu.addSupplier'),
                            'route' => route('supplier.create'),
                        ],
                        [
                            'label' => __('menu.suppliers'),
                            'route' => route('supplier.index'),
                        ],
                    ]
                ])

            @include('layouts.navbar-item', [
                    'label' => __('menu.purchaseRequests'),
                    'show' => Auth::user()->isAdmin() || Auth::user()->isSeller() || Auth::user()->isBuyer(),
                    'items' => [
                        [
                            'show' => Auth::user()->isAdmin() || Auth::user()->isSeller(),
                            'label' => __('menu.addPurchaseRequest'),
                            'route' => route('purchase-request.create'),
                        ],
                        [
                            'show' => Auth::user()->isAdmin() || Auth::user()->isSeller(),
                            'label' => __('menu.purchaseRequests'),
                            'route' => route('purchase-request.index'),
                        ],
                        [
                            'show' => Auth::user()->isBuyer(),
                            'label' => __('menu.purchaseRequests'),
                            'route' => route('buyer.purchase-request.index'),
                        ]
                    ]
                ])

            @include('layouts.navbar-item', [
                    'label' => __('menu.purchases'),
                    'show' => Auth::user()->isAdmin() || Auth::user()->isBuyer(),
                    'items' => [
                        [
                            'label' => __('menu.addPurchase'),
                            'route' => route('purchase.create'),
                        ],
                        [
                            'label' => __('menu.purchases'),
                            'route' => route('purchase.index'),
                        ],
                        [
                            'label' => __('menu.inventory'),
                            'route' => route('buyer.inventory.index'),
                        ],
                        [
                            'label' => __('menu.inventoryDistribution'),
                            'route' => route('buyer.inventory.distribution'),
                        ]
                    ]
                ])
        @endauth
    </ul>

    <!-- Right Side Of Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Authentication Links -->
        @guest
            {{--<li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif--}}
        @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('user.config') }}">
                        {{ __('menu.security') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        @endguest
    </ul>
</div>
