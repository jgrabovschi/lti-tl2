@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Pods Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the pods on this cluster.</p>
    
    </div>
</div>
@if (session('success'))
<div id="flash-message" class="fixed top-20 right-4 z-50 flex items-center p-6 text-base text-green-800 border-4 border-green-300 rounded-xl bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800 shadow-lg transition-opacity duration-1000">
    <svg class="shrink-0 inline w-5 h-5 me-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <span class="sr-only">Info</span>
    <div>
        <span class="font-medium">{{ session('success') }}</span> 
    </div>
</div>
@endif
<form method="POST" action="{{ route('downloadPods') }}">
    @csrf
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON
    </button>
</form>
<form method="GET" action="{{ route('createPod') }}">
    <button type="submit" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
        Create Pod
    </button>
</form>
@if (!empty($pods))
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Namespace</th>
                <th class="px-6 py-3">Node</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Images</th>
                <th class="px-6 py-3">Ports</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pods as $pod)
                @php
                    $status = $pod['status']['phase'] ?? 'Unknown';
                    $statusColor = match($status) {
                        'Running' => 'text-green-700 bg-green-200',
                        'Pending' => 'text-yellow-700 bg-yellow-200',
                        'Failed' => 'text-red-700 bg-red-200',
                        default => 'text-gray-700 bg-gray-200',
                    };
                @endphp
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <td class="px-6 py-4">{{ $pod['metadata']['name'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $pod['metadata']['namespace'] ?? 'default' }}</td>
                    <td class="px-6 py-4">{{ $pod['spec']['nodeName'] }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @foreach ($pod['spec']['containers'] as $container)
                            <div>{{ $container['image'] }}</div>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        @foreach ($pod['spec']['containers'] as $container)
                            @if (!empty($container['ports']))
                                @foreach ($container['ports'] as $port)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                        {{ $port['containerPort'] }}
                                    </span>
                                @endforeach
                            @endif
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('deletePod', ['name' => $pod['metadata']['name'], 'namespace' => $pod['metadata']['namespace']]) }}" onsubmit="return confirm('Are you sure you want to delete this pod?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>    
</div>
@else
    <div class="text-center mt-4">
        <p class="text-gray-500">No pods found.</p>
    </div>
@endif
<script>

    document.addEventListener('DOMContentLoaded', () => {
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => flash.remove(), 1000); 
            }, 3000); // wait 5 seconds before starting fade
        }
    });
</script>

@endsection