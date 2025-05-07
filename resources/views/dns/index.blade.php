@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">DNS Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the DNS on this router.</p>
    
    </div>
</div>
<form method="POST" action="{{ route('downloadDns') }}">
    @csrf
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON
    </button>
</form>
<div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    <form method="GET" action="{{ route('AddServersDns') }}">
        <div class="mb-4">
            <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of servers to add:</label>
            <input type="number" id="number" name="numberOfServers" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
            Add DNS SERVER
        </button>
    </form>
</div>
@if($data->servers != '')
    <form method="GET" action="{{ route('removeServerDns') }}">
        <button type="submit" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 mt-4">
            Remove DNS Server
        </button>
    </form>
@endif
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Active
                </th>
                <th scope="col" class="px-6 py-3">
                    Dynamic Servers
                </th>
                <th scope="col" class="px-6 py-3">
                    Servers
                </th>
                <th scope="col" class="px-6 py-3">

                </th>
                {{-- <th scope="col" class="px-6 py-3">
                    Action
                </th> --}}
            </tr>
        </thead>
        <tbody>
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <span class="px-2 py-1 text-xs font-semibold {{ $data->{'allow-remote-requests'} == 'true' ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200' }} rounded">
                        {{ $data->{'allow-remote-requests'} == 'true' ? 'Enabled' : 'Disabled' }}
                    </span>
                </th>
                <td class="px-6 py-4">
                    {{ $data->{'dynamic-servers'} ?? 'N/A' }}   
                    
                </td>
                <td class="px-6 py-4">
                    {{ $data->{'servers'} ?? 'N/A' }}
                </td>
                <td class="px-6 py-4">
                <form method="POST" action="{{ route('toggleDns') }}">
                        @csrf
                        @if($data->{'allow-remote-requests'} == 'true')
                            <input type="hidden" name="toggle" value="false">
                            <button type="sumbit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800 ">
                                Disable
                            </button>
                        @else
                            <input type="hidden" name="toggle" value="true">
                            <button type="sumbit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                Enable
                            </button>
                        @endif
                    </form>
                </td>  
                {{-- <td class="px-6 py-4">
                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                </td> --}}
            </tr>
        </tbody>
    </table>
</div>


@endsection