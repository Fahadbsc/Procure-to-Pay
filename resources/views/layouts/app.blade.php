
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acme P2P')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            align-self: flex-start;
            background: #172554;
            color: white;
            padding: 25px 15px;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: relative;
                height: auto;
                overflow-y: visible;
            }
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #1e40af;
            color: white;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 20px;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 sidebar">

            <h4 class="mb-4">ACME P2P</h4>

            @auth

                @php
                    $roleNames = auth()->user()->roles->pluck('name')
                        ->map(fn ($name) => mb_strtolower(trim($name)));
                    $isAdmin = $roleNames->contains('admin');
                @endphp

                {{-- Admin Menus --}}
                @if($isAdmin)

                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('vendors.index') }}">Vendors</a>

                    <a href="{{ route('users.index') }}">Users</a>

                    <a href="{{ route('items.index') }}">Items</a>

                    <a href="{{ route('units.index') }}">Units</a>

                    <a href="{{ route('cost-centres.index') }}">Cost Centres</a>

                    <a href="{{ route('purchase-requisitions.index') }}">
                        Purchase Requisitions
                    </a>

                    <a href="{{ route('purchase-orders.index') }}">
                        Purchase Orders
                    </a>

                    <a href="{{ route('goods-receipts.index') }}">
                        Goods Receipts
                    </a>

                    <a href="{{ route('supplier-invoices.index') }}">
                        Supplier Invoices
                    </a>

                    <a href="{{ route('payments.index') }}">Vendor Payments</a>

                    <a href="{{ route('budgets.index') }}">Budgets</a>

                    <a href="{{ route('approval-rules.index') }}"
                       class="{{ request()->routeIs('approval-rules.*') ? 'active' : '' }}">
                        Approval Rules
                    </a>

                    <a href="{{ route('reports.index') }}"
                       class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        Reports
                    </a>

                @endif

                @if($roleNames->contains('requester') && !$isAdmin)
                    <a href="{{ route('purchase-requisitions.index') }}">Purchase Requisitions</a>
                @endif
                @if($roleNames->contains('procurement officer') && !$isAdmin)
                    <a href="{{ route('vendors.index') }}">Vendors</a><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a>
                @endif
                @if($roleNames->contains('store keeper') && !$isAdmin)
                    <a href="{{ route('goods-receipts.index') }}">Goods Receipts</a>
                @endif
                @if($roleNames->contains('accounts') && !$isAdmin)
                    <a href="{{ route('supplier-invoices.index') }}">Supplier Invoices</a><a href="{{ route('payments.index') }}">Vendor Payments</a><a href="{{ route('reports.index') }}">Reports</a>
                @endif

                {{-- My Approvals - All Authenticated Users --}}
                <a href="{{ route('my-approvals.index') }}"
                   class="{{ request()->routeIs('my-approvals.*') ? 'active' : '' }}">
                    My Approvals
                </a>

            @endauth

        </div>


        <div class="col-md-10 p-4">

            <div class="d-flex justify-content-end align-items-center mb-4">
                @auth
                    @php($unreadCount = auth()->user()->unreadNotifications()->count())
                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-outline-primary btn-sm position-relative me-3"
                       aria-label="Notifications{{ $unreadCount ? ', '.$unreadCount.' unread' : '' }}"
                       title="Notifications">
                        <span aria-hidden="true">&#128276;</span>
                        <span class="ms-1">Notifications</span>
                        @if($unreadCount > 0)
                            <span class="badge rounded-pill text-bg-danger ms-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @endauth

                <span class="me-3">
                    {{ auth()->user()->name ?? '' }}
                </span>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button class="btn btn-outline-danger btn-sm">
                            Logout
                        </button>
                    </form>
                @endauth

            </div>


            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif


            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif


            @yield('content')

        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Prevent accidental value changes when scrolling over a focused number field.
    document.addEventListener('wheel', function (event) {
        if (event.target instanceof HTMLInputElement &&
            event.target.type === 'number' &&
            document.activeElement === event.target) {
            event.target.blur();
        }
    }, { passive: true });
</script>

@stack('scripts')

</body>
</html>
