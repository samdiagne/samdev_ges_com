<div>
    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">clients</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Particuliers
            </p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $stats['individual'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Entreprises
            </p>
            <p class="text-2xl font-bold text-secondary mt-1">{{ $stats['company'] }}</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h3 class="text-lg font-bold text-dark">Clients</h3>
            <p class="text-sm text-gray-400">{{ $customers->total() }} client(s)</p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                       text-white font-medium px-4 py-2.5 rounded-xl transition-all
                       shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau client
        </button>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Nom, téléphone, email..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <select wire:model.live="filterType"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous les types</option>
                <option value="individual">Particuliers</option>
                <option value="company">Entreprises</option>
            </select>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Contact
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden lg:table-cell">
                            Type
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Achats</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Total achats
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Nom --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center
                                                justify-center flex-shrink-0 font-bold text-sm
                                                {{ $customer->type === 'company'
                                                   ? 'bg-secondary/10 text-secondary'
                                                   : 'bg-primary/10 text-primary' }}">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-dark">
                                            {{ $customer->name }}
                                        </p>
                                        @if($customer->company_name)
                                            <p class="text-xs text-gray-400">
                                                🏢 {{ $customer->company_name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                <div class="space-y-0.5">
                                    @if($customer->phone)
                                        <p class="text-sm text-dark flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498
                                                         4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042
                                                         11.042 0 005.516 5.516l1.13-2.257a1 1 0
                                                         011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2
                                                         2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $customer->phone }}
                                        </p>
                                    @endif
                                    @if($customer->email)
                                        <p class="text-xs text-gray-400">
                                            {{ $customer->email }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-4 py-3 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $customer->type === 'company'
                                                ? 'bg-secondary/10 text-secondary'
                                                : 'bg-primary/10 text-primary' }}">
                                    {{ $customer->type === 'company' ? '🏢 Entreprise' : '👤 Particulier' }}
                                </span>
                            </td>

                            {{-- Nb achats --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7
                                             bg-accent/10 text-primary text-xs font-bold rounded-lg">
                                    {{ $customer->sales_count }}
                                </span>
                            </td>

                            {{-- Total achats --}}
                            <td class="px-4 py-3 text-right hidden md:table-cell">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($customer->total_purchases, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="viewCustomer({{ $customer->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-primary
                                                   hover:bg-primary/10 transition-colors"
                                            title="Voir détail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                                     0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                                     7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="openEditModal({{ $customer->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-primary
                                                   hover:bg-primary/10 transition-colors"
                                            title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                     0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                                     15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $customer->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-red-500
                                                   hover:bg-red-50 transition-colors"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                     2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                     1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center
                                                justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0
                                                     0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4
                                                     4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">
                                        Aucun client trouvé
                                    </p>
                                    <button wire:click="openCreateModal"
                                            class="text-sm text-primary hover:underline">
                                        Ajouter le premier client
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    {{-- ══ MODAL Détail client ══ --}}
    @if($showDetailModal && $viewingCustomer)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('showDetailModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl
                        max-h-[90vh] overflow-y-auto">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100
                            sticky top-0 bg-white rounded-t-2xl z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                    font-bold text-sm
                                    {{ $viewingCustomer->type === 'company'
                                       ? 'bg-secondary/10 text-secondary'
                                       : 'bg-primary/10 text-primary' }}">
                            {{ strtoupper(substr($viewingCustomer->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-dark">
                                {{ $viewingCustomer->name }}
                            </h3>
                            <p class="text-xs text-gray-400">
                                {{ $viewingCustomer->type === 'company' ? '🏢 Entreprise' : '👤 Particulier' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="$set('showDetailModal', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-6">

                    {{-- Infos + Stats --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Coordonnées --}}
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                Coordonnées
                            </p>
                            @if($viewingCustomer->phone)
                                <p class="text-sm text-dark flex items-center gap-2">
                                    <span class="text-gray-400">📞</span> {{ $viewingCustomer->phone }}
                                </p>
                            @endif
                            @if($viewingCustomer->whatsapp)
                                <p class="text-sm text-dark flex items-center gap-2">
                                    <span class="text-gray-400">💬</span> {{ $viewingCustomer->whatsapp }}
                                </p>
                            @endif
                            @if($viewingCustomer->email)
                                <p class="text-sm text-dark flex items-center gap-2">
                                    <span class="text-gray-400">✉️</span> {{ $viewingCustomer->email }}
                                </p>
                            @endif
                            @if($viewingCustomer->address)
                                <p class="text-sm text-dark flex items-center gap-2">
                                    <span class="text-gray-400">📍</span> {{ $viewingCustomer->address }}
                                </p>
                            @endif
                            @if($viewingCustomer->company_name)
                                <p class="text-sm text-dark flex items-center gap-2">
                                    <span class="text-gray-400">🏢</span> {{ $viewingCustomer->company_name }}
                                    @if($viewingCustomer->ninea)
                                        <span class="text-xs text-gray-400">({{ $viewingCustomer->ninea }})</span>
                                    @endif
                                </p>
                            @endif
                        </div>

                        {{-- Chiffres clés --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-primary/5 rounded-xl p-3 text-center">
                                <p class="text-2xl font-bold text-primary">
                                    {{ $viewingCustomer->sales_count }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Ventes</p>
                            </div>
                            <div class="bg-orange-50 rounded-xl p-3 text-center">
                                <p class="text-2xl font-bold text-orange-500">
                                    {{ $viewingCustomer->orders_count }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Commandes</p>
                            </div>
                            <div class="bg-green-50 rounded-xl p-3 col-span-2 text-center">
                                <p class="text-xl font-bold text-green-600">
                                    {{ number_format($viewingCustomer->total_purchases, 0, ',', ' ') }}
                                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Total achats</p>
                            </div>
                        </div>
                    </div>

                    @if($viewingCustomer->notes)
                        <div class="bg-yellow-50 rounded-xl p-3 text-sm text-gray-600">
                            📝 {{ $viewingCustomer->notes }}
                        </div>
                    @endif

                    {{-- Commandes en ligne --}}
                    @if($viewingCustomer->orders->count() > 0)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                                Dernières commandes en ligne
                            </p>
                            <div class="space-y-2">
                                @foreach($viewingCustomer->orders as $order)
                                    <div class="flex items-center justify-between p-3
                                                bg-gray-50 rounded-xl text-sm">
                                        <div>
                                            <p class="font-semibold text-dark">
                                                {{ $order->reference }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $order->created_at->format('d/m/Y') }}
                                                — {{ $order->city }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-dark">
                                                {{ number_format($order->total, 0, ',', ' ') }}
                                                <span class="text-xs font-normal text-gray-400">FCFA</span>
                                            </p>
                                            <span @class([
                                                'inline-flex px-2 py-0.5 rounded-full text-xs font-medium',
                                                'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                                'bg-blue-100 text-blue-700'     => $order->status === 'confirmed',
                                                'bg-purple-100 text-purple-700' => $order->status === 'processing',
                                                'bg-indigo-100 text-indigo-700' => $order->status === 'shipped',
                                                'bg-green-100 text-green-700'   => $order->status === 'delivered',
                                                'bg-red-100 text-red-700'       => $order->status === 'cancelled',
                                            ])>
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Ventes comptoir --}}
                    @if($viewingCustomer->sales->count() > 0)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                                Dernières ventes comptoir
                            </p>
                            <div class="space-y-2">
                                @foreach($viewingCustomer->sales as $sale)
                                    <div class="flex items-center justify-between p-3
                                                bg-gray-50 rounded-xl text-sm">
                                        <div>
                                            <p class="font-semibold text-dark">
                                                {{ $sale->reference }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $sale->sold_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-dark">
                                                {{ number_format($sale->total_ttc, 0, ',', ' ') }}
                                                <span class="text-xs font-normal text-gray-400">FCFA</span>
                                            </p>
                                            @if($sale->invoice)
                                                <a href="{{ route('factures.download', $sale) }}"
                                                   target="_blank"
                                                   class="w-7 h-7 flex items-center justify-center
                                                          rounded-lg text-gray-400 hover:text-primary
                                                          hover:bg-primary/10 transition-colors"
                                                   title="Télécharger facture">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                        <button wire:click="openEditModal({{ $viewingCustomer->id }}); $set('showDetailModal', false)"
                                class="flex-1 py-2.5 border-2 border-gray-200 text-gray-600
                                       text-sm font-semibold rounded-xl hover:border-primary
                                       hover:text-primary transition-all">
                            ✏️ Modifier
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- ══ MODAL Créer / Modifier ══ --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeModal"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg
                        max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100
                            sticky top-0 bg-white rounded-t-2xl z-10">
                    <h3 class="text-base font-bold text-dark">
                        {{ $isEditing ? 'Modifier le client' : 'Nouveau client' }}
                    </h3>
                    <button wire:click="closeModal"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="px-6 py-4 space-y-4">

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Type</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    wire:click="$set('type', 'individual')"
                                    @class([
                                        'py-2.5 rounded-xl text-sm font-semibold transition-all border-2',
                                        'border-primary bg-primary text-white' => $type === 'individual',
                                        'border-gray-200 bg-gray-50 text-gray-600' => $type !== 'individual',
                                    ])>
                                👤 Particulier
                            </button>
                            <button type="button"
                                    wire:click="$set('type', 'company')"
                                    @class([
                                        'py-2.5 rounded-xl text-sm font-semibold transition-all border-2',
                                        'border-secondary bg-secondary text-white' => $type === 'company',
                                        'border-gray-200 bg-gray-50 text-gray-600' => $type !== 'company',
                                    ])>
                                🏢 Entreprise
                            </button>
                        </div>
                    </div>

                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text"
                               placeholder="{{ $type === 'company' ? 'Nom du contact principal' : 'Prénom et Nom' }}"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('name')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Entreprise (si type company) --}}
                    @if($type === 'company')
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">
                                    Nom entreprise
                                </label>
                                <input wire:model="company_name" type="text"
                                       placeholder="Raison sociale"
                                       class="w-full px-3 py-2.5 border border-gray-200
                                              rounded-xl text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">
                                    NINEA
                                </label>
                                <input wire:model="ninea" type="text"
                                       placeholder="Numéro NINEA"
                                       class="w-full px-3 py-2.5 border border-gray-200
                                              rounded-xl text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                        </div>
                    @endif

                    {{-- Téléphone + WhatsApp --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Téléphone
                            </label>
                            <input wire:model="phone" type="text"
                                   placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          bg-gray-50 focus:outline-none transition-colors
                                          {{ $errors->has('phone')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 focus:border-primary focus:bg-white' }}"/>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                WhatsApp
                            </label>
                            <input wire:model="whatsapp" type="text"
                                   placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                    </div>

                    {{-- Email + Adresse --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Email
                            </label>
                            <input wire:model="email" type="email"
                                   placeholder="email@client.com"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('email')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Adresse
                            </label>
                            <input wire:model="address" type="text"
                                   placeholder="Quartier, ville"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">Notes</label>
                        <textarea wire:model="notes" rows="2"
                                  placeholder="Informations supplémentaires..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                         text-sm bg-gray-50 focus:outline-none
                                         focus:border-primary transition-colors resize-none">
                        </textarea>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-600
                                       hover:bg-gray-100 rounded-xl transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary hover:bg-primary/90 text-white
                                       text-sm font-medium rounded-xl transition-all
                                       shadow-lg shadow-primary/25 flex items-center gap-2">
                            <svg wire:loading class="animate-spin w-4 h-4" fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ $isEditing ? 'Enregistrer' : 'Créer' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>