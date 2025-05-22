@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">New Deployment</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you will create a new deployment in the cluster.</p>
    
    </div>
</div>
<form method="GET" action="{{ route('showDeployment') }}">
    
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Back
    </button>

</form>

<div class="max-w-xl ml-4">
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">

        {{-- Toast de erro global --}}
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

        {{-- Formulário --}}
        <form method="POST" action="{{ route('storeDeployment') }}" class="grid grid-cols-1 gap-6">
            @csrf
            @method('PUT')

            {{-- Campos gerais --}}
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Informações Gerais</h2>
            </div>

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('name') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="namespace" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Namespace</label>
                <select name="namespace" id="namespace" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($namespaces as $namespace)
                        <option value="{{ $namespace['metadata']['name'] }}" {{ old('namespace') === $namespace['metadata']['name'] ? 'selected' : '' }}>
                            {{ $namespace['metadata']['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('namespace') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="labelName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Label Name</label>
                <input type="text" name="labelName" id="labelName" value="{{ old('labelName', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('labelName') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="replicas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Replicas</label>
                <input type="number" name="replicas" id="replicas" value="{{ old('replicas', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('replicas') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            {{-- Containers --}}
            <div>
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Add Containers</h5>
                @error('containers') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="container-name" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" id="container-name" class="container-input w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>

                    <div>
                        <label for="container-image" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Image</label>
                        <input type="text" id="container-image" class="container-input w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>

                    <div>
                        <label for="container-ports" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Ports <span class="text-xs text-slate-500 dark:text-slate-300">(Ex: 80,443)</span></label>
                        <input type="text" id="container-ports" class="container-input w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                </div>

                <button type="button" onclick="addContainer()" class="mt-3 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-700">
                    + Add Container
                </button>
            </div>

            <div class="overflow-x-auto mt-6">
                <table id="container-list" class="w-full mb-5 text-sm text-left text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Image</th>
                            <th class="px-4 py-2">Ports</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="container-table-body" class="bg-white dark:bg-gray-800">
                        <!-- preenchido dinamicamente -->
                    </tbody>
                </table>
            </div>

            <input type="hidden" name="containers" id="containers-json" />

            <div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-2 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>





<script>
    const containers = [];
    updateContainerTable();

    function addContainer() {
        const name = document.getElementById('container-name').value.trim();
        const image = document.getElementById('container-image').value.trim();
        const portsRaw = document.getElementById('container-ports').value.trim();

        if (!name || !image) {
            alert('Name and Image are required.');
            return;
        }

        const ports = portsRaw ? portsRaw.split(',').map(p => p.trim()) : [];

        containers.push({ name, image, ports });
        updateContainerTable();
        updateHiddenInput();

        // Limpar campos
        document.querySelectorAll('.pod-input').forEach(input => input.value = '');
    }

    function removeContainer(index) {
        containers.splice(index, 1);
        updateContainerTable();
        updateHiddenInput();
    }

    function updateContainerTable() {
        const tbody = document.getElementById('container-table-body');
        if (containers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">No containers added</td></tr>';
            return;
        }
        tbody.innerHTML = containers.map((c, i) => `
            <tr class="border-t border-gray-200 dark:border-gray-600">
                <td class="px-4 py-2">${c.name}</td>
                <td class="px-4 py-2">${c.image}</td>
                <td class="px-4 py-2">${c.ports.join(', ')}</td>
                <td class="px-4 py-2">
                    <button type="button" onclick="removeContainer(${i})" class="text-red-600 hover:underline dark:text-red-400">Remove</button>
                </td>
            </tr>
        `).join('');
    }

    function updateHiddenInput() {
        document.getElementById('containers-json').value = JSON.stringify(containers);
    }
</script>


@endsection