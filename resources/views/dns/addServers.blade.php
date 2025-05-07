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

<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <form method="POST" action="{{ route('addServersRDns') }}" class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        @csrf
        
        <input type="hidden" name="numberOfServers" value="{{$numberOfServers}}">
        @for($i = 1; $i <= $numberOfServers; $i++)
            <div class="mb-4">
                <label for="{{'server_' . $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Server Address</label>
                <input type="text" id="{{'server_' . $i }}" name="{{'server_' . $i }}" placeholder="Ex: 192.168.1.1" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            
        @endfor
        
        
        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
            Submit
        </button>
        
    </form>
</div>



@endsection