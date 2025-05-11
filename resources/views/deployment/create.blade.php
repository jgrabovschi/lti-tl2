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
    <form method="POST" action="{{ route('storeDeployment') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="numberContainer" value="{{$numberContainer}}">
        {{-- Campos gerais --}}
        <div class="col-span-full">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Informações Gerais</h2>
        </div>

        <div>
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            @error('name') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="namespace" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Namespace</label>
            <select name="namespace" id="namespace" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
            <input type="text" name="labelName" id="labelName" value="{{ old('labelName', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            @error('labelName') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="replicas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Replicas</label>
            <input type="number" name="replicas" id="replicas" value="{{ old('replicas', '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            @error('replicas') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Containers --}}
        <div class="col-span-full">
            <h2 class="text-lg font-semibold mb-4 mt-6 text-gray-800 dark:text-white">Containers</h2>
        </div>

        @for($i = 1; $i <= $numberContainer; $i++)
            <div class="p-4 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                <h4 class="text-md font-semibold mb-4 text-gray-800 dark:text-white">Container {{ $i }}</h4>

                <div class="mb-4">
                    <label for="{{ 'nameContainer_' . $i }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                    <input type="text" name="{{ 'nameContainer_' . $i }}" id="{{ 'nameContainer_' . $i }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    @error('nameContainer_' . $i) <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="{{ 'imageContainer_' . $i }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image</label>
                    <input type="text" name="{{ 'imageContainer_' . $i }}" id="{{ 'imageContainer_' . $i }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    @error('imageContainer_' . $i) <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="{{ 'port_' . $i }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ports <span class="text-xs text-slate-500 block dark:text-slate-300">Specify more separated by , (Example: 80,443)</span></label>
                    <input type="text" name="{{ 'port_' . $i }}" id="{{ 'port_' . $i }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    @error('port_' . $i) <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
                </div>
            </div>
        @endfor

        <div class="col-span-full">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-2 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Save
            </button>
        </div>
    </form>
</div>




@endsection