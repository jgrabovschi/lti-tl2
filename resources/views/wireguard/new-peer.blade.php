@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6 w-md bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">New WireGuard Peer</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Fill in the details to create a new peer for the WireGuard interface.</p>
    </div>
</div>

<form method="GET" action="{{ route('showWireguardPeers') }}">
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-4">
        Back
    </button>
</form>

<div class="w-full md:w-1/2 lg:w-1/3 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    @error('global')
    <div id="toast-danger" class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg dark:text-gray-400 dark:bg-gray-800" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
            </svg>
            <span class="sr-only">Error icon</span>
        </div>
        <div class="ms-3 text-sm font-normal">{{ $message }}</div>
    </div>
    @enderror

    <form method="POST" class="max-w-sm mx-auto" action="{{ route('storePeerWireguard') }}">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label for="interface" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Interface</label>
            <select name="interface" id="interface" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @foreach ($interfaces as $interface)
                    <option value="{{ $interface->name }}" {{ old('interface') === $interface ? 'selected' : '' }}>
                        {{ $interface->name }}
                    </option>
                @endforeach
            </select>
        
            @error('interface')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-5">
            
            <label for="allowed-address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Allowed Address</label>
            <div class="flex items-center space-x-1">
                <input type="text" name="allowed-address" id="allowed-address" value="{{ old('allowed-address', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" /><span class="mx-2 text-gray-900 dark:text-white"> /32</span>
                @error('allowed-address')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-5">
            
            <label for="client-dns" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client DNS</label>
            <input type="text" name="client-dns" id="client-dns" value="{{ old('client-dns', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            @error('client-dns')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-5">
            <label for="client-endpoint" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client Endpoint<div class="text-xs text-slate-500 dark:text-slate-300">Usually the IP address of the interface that is exposed to the internet</div></label>
            <input type="text" name="client-endpoint" id="client-endpoint" value="{{ old('client-endpoint', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            @error('client-endpoint')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
            @enderror
        </div>

        <input type="hidden" name="private-key" value="auto" />

        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Save Peer
        </button>
    </form>
</div>

@endsection
