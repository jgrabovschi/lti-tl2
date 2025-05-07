@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">DNS Information</h5>
    <p class="font-normal text-gray-700 dark:text-gray-400">Edit DNS Server</p>
    
    </div>
</div>
<form method="GET" action="{{ route('showDns') }}">
    
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Back
    </button>
</form>

<div class="relative overflow-x-auto mt-6">
    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
            Unselect the IPs you want to remove from the servers
        </h2>

        <form method="POST" action="{{ route('removeServerRDns') }}">
            @csrf

            <div class="space-y-3 mb-6">
                @foreach($servers as $index => $server)
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="server_{{ $index + 1 }}" 
                            name="servers[]" 
                            value="{{ $server }}" 
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                            checked
                        >
                        <label for="server_{{ $index + 1 }}" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ $server }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button 
                type="submit" 
                class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800 transition-colors duration-200"
            >
                Submit
            </button>
        </form>
    </div>
</div>



@endsection