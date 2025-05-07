@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Bridge Interfaces Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the wireless interfaces on this router.</p>
    
    </div>
</div>
<form method="GET" action="{{ route('showInterfacesBridge') }}">
    
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Back
    </button>
</form>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <form method="POST" action="{{ route('storeBridge') }}" class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        
        <div class="mb-4">
            <label for="arp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ARP</label>
            <select id="arp" name="arp" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="disabled">Disabled</option>
                <option value="enabled">Enabled</option>
                <option value="local-proxy-arp">Local Proxy ARP</option>
                <option value="proxy-arp">Proxy ARP</option>
            </select>
        </div>
        
        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
            Submit
        </button>
    </form>
</div>



@endsection