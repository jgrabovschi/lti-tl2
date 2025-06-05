@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Deployment Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the deployments on this cluster.</p>
    
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

<form method="POST" action="{{ route('downloadDeployment') }}">
    @csrf
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON
    </button>
</form>

<form method="GET" action="{{ route('createDeployment') }}">
    <button type="submit" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
        Create Deployment
    </button>
</form>


@if (!empty($deploys))
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Namespace</th>
                <th class="px-6 py-3">Replicas</th>
                <th class="px-6 py-3">Ready Replicas</th>
                <th class="px-6 py-3">Selectors</th>
                <th class="px-6 py-3">Containers</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deploys as $index => $deploy)
            @php
                $cardId = 'containerCard' . $index;
            @endphp    
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <td class="px-6 py-4">{{ $deploy['metadata']['name'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $deploy['metadata']['namespace'] ?? 'default' }}</td>
                    <td class="px-6 py-4">{{ $deploy['spec']['replicas'] }}</td>
                    <td class="px-6 py-4">{{ $deploy['status']['readyReplicas'] ?? '0' }}</td>
                    <td class="px-6 py-4">
                        @if(array_key_exists('matchLabels', $deploy['spec']['selector']))
                            @foreach ($deploy['spec']['selector']['matchLabels'] as $selectors)
                                {{ $selectors }}
                            @endforeach
                           
                        @endif

                    </td>
                     <td class="px-6 py-4">
                        <button
                            onclick="document.getElementById('{{ $cardId }}').classList.remove('hidden')"
                            class="p-1 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition"
                            title="Ver containers" >
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                                <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/>
                            </svg>
                        </button>


                        <!-- Modal / Floating Card -->
                        <div id="{{ $cardId }}" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/30 backdrop-blur-sm">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Containers inside the deployment "{{ $deploy['metadata']['name'] }}"</h2>
                                    <button onclick="document.getElementById('{{ $cardId }}').classList.add('hidden')" class="text-red-500 cursor-pointer font-semibold hover:underline">
                                       <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="currentColor">
                                        <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
                                    </button>
                                </div>

                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                        <tr>
                                            <th class="px-4 py-2">Name</th>
                                            <th class="px-4 py-2">Image</th>
                                            <th class="px-4 py-2">Ports</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">
                         
                                        @foreach ($deploy['spec']['template']['spec']['containers'] as $container)
                                            <tr class="border-b dark:border-gray-700">
                                                <td class="px-4 py-2">{{ $container['name'] }}</td>
                                                <td class="px-4 py-2">{{ $container['image'] }}</td>
                                                <td class="px-4 py-2">
                                                    @if (!empty($container['ports']))
                                                        @foreach ($container['ports'] as $port)
                                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                                {{ $port['containerPort'] }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-gray-400 italic">None</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('deleteDeployment', ['name' => $deploy['metadata']['name'], 'namespace' => $deploy['metadata']['namespace']]) }}" onsubmit="return confirm('Are you sure you want to delete this deployment?');">
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
        <p class="text-gray-500">No Deployments found.</p>
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


    document.addEventListener('DOMContentLoaded', () => {
        // Fecha ao clicar fora do card
        document.querySelectorAll('[id^="containerCard"]').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        // Fecha com tecla ESC
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="containerCard"]').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    });
</script>

@endsection