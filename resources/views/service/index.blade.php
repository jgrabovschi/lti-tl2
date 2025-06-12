@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Service/Ingress Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the services/ingresses on this cluster.</p>
    
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

<div class="flex">
    <form method="POST" action="{{ route('downloadService') }}">
        @csrf
        <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
            Download JSON Service
        </button>
    </form>

    <form method="POST" action="{{ route('downloadIngress') }}">
    @csrf
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON Ingress
    </button>
</form>
</div>

<form method="GET" action="{{ route('createService') }}">
    <button type="submit" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
        Create Service/Ingress
    </button>
</form>

<div>
  <!-- New Tabs as UL -->
  <ul class="flex flex-wrap text-sm mt-4 font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400" id="tabs">
    <li class="me-2">
        <a href="#" data-tab="tab1" aria-current="page" class="inline-block p-4 text-blue-600 bg-gray-100 rounded-t-lg active dark:bg-gray-800 dark:text-blue-500 tab-link">Services</a>
    </li>
    <li class="me-2">
        <a href="#" data-tab="tab2" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 tab-link">Ingresses</a>
    </li>
   
  </ul>

  <!-- Conteúdo das tabs -->
  <div id="tab1" class="tab-content">
    @if (!empty($services))
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Namespace</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Cluster IP</th>
                        <th class="px-6 py-3">Ports</th>
                        <th class="px-6 py-3">Selector</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4">{{ $service['metadata']['name'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $service['metadata']['namespace'] ?? 'default' }}</td>
                            <td class="px-6 py-4">{{ $service['spec']['type'] }}</td>
                            <td class="px-6 py-4">{{ $service['spec']['clusterIP'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @foreach ($service['spec']['ports'] as $ports)
                                    @if (!empty($ports))
                                        <span>
                                        Name: {{ $ports['name'] ?? 'default' }} | Protocol: {{ $ports['protocol'] }} | Port: {{ $ports['port']}} --> Target Port: {{ $ports['targetPort'] }}                                
                                        </span><br>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                @if(array_key_exists('selector', $service['spec']))
                                    @foreach ($service['spec']['selector'] as $selectors)
                                        {{ $selectors }}
                                    @endforeach
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('deleteService', ['name' => $service['metadata']['name'], 'namespace' => $service['metadata']['namespace']]) }}" onsubmit="return confirm('Are you sure you want to delete this service?');">
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
  </div>

  <div id="tab2" class="tab-content hidden">
    @if (!empty($ingresses))
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Namespace</th>
                        <th class="px-6 py-3">Host</th>
                        <th class="px-6 py-3">Services</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ingresses as $ingress)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4">{{ $ingress['metadata']['name'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $ingress['metadata']['namespace'] ?? 'default' }}</td>
                            <td class="px-6 py-4">{{ $ingress['spec']['rules']['0']['host'] }}</td>
                            <td class="px-6 py-4">
                                @foreach ($ingress['spec']['rules']['0']['http']['paths'] as $path)
                                    @if (!empty($path))
                                        <span>
                                        Name: {{ $path['backend']['service']['name'] }} | Port: {{ $path['backend']['service']['port']['number'] }}                              
                                        </span><br>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('deleteIngress', ['name' => $ingress['metadata']['name'], 'namespace' => $ingress['metadata']['namespace']]) }}" onsubmit="return confirm('Are you sure you want to delete this ingress?');">
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
                <p class="text-gray-500">No Ingress found.</p>
            </div>
        @endif
  </div>

  <div id="tab3" class="tab-content p-4 hidden">
    <p>Settings content</p>
  </div>

  <div id="tab4" class="tab-content p-4 hidden">
    <p>Contacts content</p>
  </div>
</div>

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

  // Script para controle de tabs
  document.querySelectorAll('.tab-link').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      // Remover active de todos
      document.querySelectorAll('.tab-link').forEach(el => {
        el.classList.remove('active');
        el.classList.remove('text-blue-600');
        el.classList.add('text-gray-500');
        el.classList.add('hover:text-gray-600');
        el.classList.add('hover:bg-gray-50');
        el.classList.remove('bg-gray-100');
        el.classList.remove('dark:bg-gray-800');
        el.classList.remove('dark:text-blue-500');
      });

      // Adicionar active ao clicado
      link.classList.add('active');
      link.classList.add('text-blue-600');
      link.classList.remove('text-gray-500');
      link.classList.remove('hover:text-gray-600');
      link.classList.remove('hover:bg-gray-50');
      link.classList.add('bg-gray-100');
      link.classList.add('dark:bg-gray-800');
      link.classList.add('dark:text-blue-500');

      // Mostrar o conteúdo correspondente
      const tabId = link.getAttribute('data-tab');
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
      });
      document.getElementById(tabId).classList.remove('hidden');
    });
  });
</script>

@endsection
