@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">DNS Static Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the DNS Static on this router.</p>
    
    </div>
</div>
<form method="POST" action="{{ route('downloadDnsStatic') }}">
    @csrf
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON
    </button>
</form>

<form method="GET" action="{{ route('createDnsStatic') }}">
    <button type="submit" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
        Create DNS Static
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
                    Address
                </th>
                <th scope="col" class="px-6 py-3">
                    Disabled
                </th>
                <th scope="col" class="px-6 py-3">

                </th>
                <th scope="col" class="px-6 py-3">

                </th>
                <th scope="col" class="px-6 py-3">

                </th>
                {{-- <th scope="col" class="px-6 py-3">
                    Action
                </th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $dnsStatic)
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $dnsStatic->{'.id'} }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $dnsStatic->{'name'} }}   
                        
                    </td>
                    <td class="px-6 py-4">
                        {{ $dnsStatic->{'address'} }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold {{ $dnsStatic->{'disabled'} == 'false' ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200' }} rounded">
                            {{ $dnsStatic->{'disabled'} == 'true' ? 'Disabled' : 'Enabled' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('toggleDnsStatic', ['id' => $dnsStatic->{'.id'}]) }}">
                            @csrf
                            @if($dnsStatic->{'disabled'} == 'true')
                                <input type="hidden" name="toggle" value="false">
                                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    Enable
                                </button>
                            @else
                                <input type="hidden" name="toggle" value="true">
                                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    Disable
                                </button>
                            @endif
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('editDnsStatic', ['id' => $dnsStatic->{'.id'}]) }}" 
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Edit
                        </a>
                       
                    </td>
                    <td class="px-6 py-4">
                    <form method="POST" action="{{ route('destroyDnsStatic', ['id' => $dnsStatic->{'.id'}]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Delete</button>

                        </form>
                    </td>
                @endforeach   
            </tr>
        </tbody>
    </table>
</div>


@endsection