<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-lg">Your Balance: <strong>${{ number_format(Auth::user()->balance, 2) }}</strong></p>

                    {{-- Flash Messages --}}
                    @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if($errors->any())
                    <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    {{-- Top Up --}}
                    <div class="mb-6">
                        <h3 class="font-bold mb-2">Top Up Account</h3>
                        <form action="{{ route('topup') }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="amount" step="0.01" placeholder="Amount" required class="border p-1 rounded">
                            <button class="bg-blue-500 text-white px-4 py-1 rounded">Top Up</button>
                        </form>
                    </div>

                    {{-- Pay Bill --}}
                    <div class="mb-6">
                        <h3 class="font-bold mb-2">Pay a Bill</h3>
                        <form action="{{ route('paybill') }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                            @csrf
                            <select name="biller" required class="border p-1 rounded">
                                <option value="">Select Biller</option>
                                <option value="electricity">Electricity (+10%)</option>
                                <option value="water">Water (+5%)</option>
                                <option value="internet">Internet</option>
                            </select>
                            <input type="number" name="amount" step="0.01" placeholder="Amount" required class="border p-1 rounded">
                            <button class="bg-yellow-500 text-white px-4 py-1 rounded">Pay</button>
                        </form>
                    </div>

                    {{-- Transfer Funds --}}
                    <div class="mb-6">
                        <h3 class="font-bold mb-2">Transfer Funds</h3>
                        <form action="{{ route('transfer') }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                            @csrf
                            <input type="email" name="recipient_email" placeholder="Recipient Email" required class="border p-1 rounded">
                            <input type="number" name="amount" step="0.01" placeholder="Amount" required class="border p-1 rounded">
                            <button class="bg-purple-500 text-white px-4 py-1 rounded">Transfer</button>
                        </form>
                    </div>

                    {{-- Transaction History --}}
                    <div class="mb-6">
                        <h3 class="font-bold mb-2">Transaction History</h3>
                        <div class="border rounded p-3 bg-gray-50">
                            @forelse ($transactions as $tx)
                            <div class="mb-2 border-b pb-1">
                                <strong>{{ ucfirst($tx->type) }}</strong> -
                                ${{ number_format($tx->amount, 2) }} -
                                {{ $tx->description }} <br>
                                <small class="text-gray-600">{{ $tx->created_at->format('d M Y h:i A') }}</small>
                            </div>
                            @empty
                            <p>No transactions yet.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
