@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Interfaces Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the interfaces on this router.</p>
    
    </div>
</div>
<form method="POST" action="{{ route('downloadInterfaces') }}">
    @csrf
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON
    </button>
</form>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    MAC Address
                </th>
                <th scope="col" class="px-6 py-3">
                    Type
                </th>
                <th scope="col" class="px-6 py-3">
                    MTU
                </th>
                <th scope="col" class="px-6 py-3">
                    Running
                </th>
                <th scope="col" class="px-6 py-3">
                    Rx Bytes
                </th>
                <th scope="col" class="px-6 py-3">
                    Ty Bytes
                </th>
                {{-- <th scope="col" class="px-6 py-3">
                    Action
                </th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach (json_decode($data) as $interface)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $interface->{'.id'} }}
                </th>
                <td class="px-6 py-4">
                    {{ $interface->name }}
                </td>
                <td class="px-6 py-4">
                    {{ $interface->{'mac-address'} ?? 'N/A' }}
                </td>
                <td class="px-6 py-4">
                    {{ $interface->{'type'} }}
                </td>
                <td class="px-6 py-4">
                    {{ $interface->{'mtu'} }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold {{ $interface->running == 'true' ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200' }} rounded">
                        {{ $interface->running == 'true' ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    {{ $interface->{'rx-byte'} }}
                </td>
                <td class="px-6 py-4">
                    {{ $interface->{'tx-byte'} }}
                </td>
                {{-- <td class="px-6 py-4">
                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection