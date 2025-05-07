@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Bridge Interfaces Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the wireless interfaces on this router.</p>
    
    </div>
</div>
<form method="GET" action="{{ route('showInterfacesBridge') }}">
    
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Back
    </button>
</form>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <form method="POST" action="{{ route('updateBridge', ['id' => $id]) }}" class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', json_decode($data)->name)}}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        
        <div class="mb-4">
            <label for="arp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ARP</label>
            <select id="arp" name="arp"  required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="disabled" {{ old('arp', json_decode($data)->arp) == 'disabled' ? 'selected' : '' }}>Disabled</option>
                <option value="enabled" {{ old('arp', json_decode($data)->arp) == 'enabled' ? 'selected' : '' }}>Enabled</option>
                <option value="local-proxy-arp" {{old('arp', json_decode($data)->arp) == 'local-proxy-arp' ? 'selected' : '' }} >Local Proxy ARP</option>
                <option value="proxy-arp" {{old('arp', json_decode($data)->arp) == 'proxy-arp' ? 'selected' : '' }}>Proxy ARP</option>
            </select>
        </div>
        
        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
            Submit
        </button>
    </form>
</div>

<br>

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
                    Interface Bridge
                </th>
                <th scope="col" class="px-6 py-3">
                    Add
                </th>
                <th scope="col" class="px-6 py-3">
                    Delete
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($interfaces as $interface)
                @if (($interface->{'type'} == 'wlan' || $interface->{'type'} == 'ether') && $interface->name !='ether1' )
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
                        {{ $interface->bridge }}
                    </td>
                    @if (json_decode($data)->name != $interface->bridge && $interface->bridge != '' )
                        <td class="px-6 py-4">
                            Must Delete associação com outra bridge
                        </td>
                    @elseif($interface->bridge == '')
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('addPortBridge') }}">
                                @csrf
                                @method('Put')
                                <input type="hidden" name="interface" value="{{ $interface->name }}">
                                <input type="hidden" name="bridge" value="{{ $id }}">
                                <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
                                    Add
                                </button>
                            </form>
                        </td>
                    @else
                        <td class="px-6 py-4">
                            cannot add to the same interface
                        </td>
                    @endif
                    @if (json_decode($data)->name == $interface->bridge)
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('destroyPortBridge', ['id' => $interface->bridgeId]) }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="bridge" value="{{$id}}">
                                <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
                                    Delete
                                </button>
                            </form>
                        </td>
                    @else
                        <td class="px-6 py-4">
                            Can only delete if in the same interface bridge
                        </td>
                    @endif
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>



@endsection