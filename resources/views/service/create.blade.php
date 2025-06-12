@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">New Service/Ingress</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you will create a new service/ingress in the cluster.</p>
    
    </div>
</div>
<form method="GET" action="{{ route('showService') }}">
    
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Back
    </button>

</form>

<div class="max-w-xl">
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">

        {{-- Toast de erro global --}}
        @error('global')
        <div id="toast-danger" class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg dark:text-gray-400 dark:bg-gray-800" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                </svg>
                <span class="sr-only">Error icon</span>
            </div>
            <div class="ms-3 text-sm font-normal">{{ $message }}</div>
        </div>
        @enderror

        {{-- Formulário --}}
        <form method="POST" action="{{ route('storeService') }}" class="grid grid-cols-1 gap-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('name') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="namespace" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Namespace</label>
                <select name="namespace" id="namespace" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($namespaces as $namespace)
                        <option value="{{ $namespace['metadata']['name'] }}" {{ old('namespace') === $namespace['metadata']['name'] ? 'selected' : '' }}>
                            {{ $namespace['metadata']['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('namespace') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="selector" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selector</label>
                <select name="selector" id="selector" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select a namespace first</option>
                </select>
                @error('selector') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <!-- ✅ No Ingress Checkbox -->
            <div class="mt-4 flex items-center">
                <input type="checkbox" id="no_ingress" name="no_ingress" value="1" {{ old('no_ingress') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="no_ingress" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">No Ingress</label>
            </div>

            {{-- Ports --}}
            <div>
                <h2 class="text-lg font-semibold mt-6 text-gray-800 dark:text-white">Ports</h2>
            </div>

            <div>
                <label for="namePort_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                <input type="text" name="namePort_1" id="namePort_1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('namePort_1') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="protocolPort_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Protocol</label>
                <select name="protocolPort_1" id="protocolPort_1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="SCTP">SCTP</option>
                    <option value="TCP">TCP</option>
                    <option value="UDP">UDP</option>
                </select>
                @error('protocolPort_1') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="port_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Port</label>
                <input type="number" name="port_1" id="port_1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('port_1') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="targetPort_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Target Port</label>
                <input type="number" name="targetPort_1" id="targetPort_1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('targetPort_1') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-2 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Make the PHP array available to JS
    const selectorsMap = @json($selectoresByNamespaces);
    const selectorSelect = document.getElementById('selector');
    const namespaceSelect = document.getElementById('namespace');
    const oldSelector = @json(old('selector'));

    function updateSelectorOptions(namespace) {
        const options = selectorsMap[namespace] || [];
        selectorSelect.innerHTML = '';

        if (options.length === 0) {
            selectorSelect.innerHTML = '<option value="">No selectors available</option>';
            return;
        }

        options.forEach(selector => {
            const option = document.createElement('option');
            option.value = selector;
            option.textContent = selector;
            if (selector === oldSelector) {
                option.selected = true;
            }
            selectorSelect.appendChild(option);
        });
    }

    // Initial population if namespace was selected previously
    const initialNamespace = namespaceSelect.value;
    if (initialNamespace) {
        updateSelectorOptions(initialNamespace);
    }

    // Handle changes in the namespace dropdown
    namespaceSelect.addEventListener('change', function () {
        updateSelectorOptions(this.value);
    });
</script>



@endsection