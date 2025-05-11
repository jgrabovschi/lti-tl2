@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Nodes Information</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can see the info about the nodes on this cluster.</p>
    
    </div>
</div>
<form method="POST" action="{{ route('downloadNodes') }}">
    @csrf
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Download JSON
    </button>
</form>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3">Node Name</th>
                <th class="px-6 py-3">OS</th>
                <th class="px-6 py-3">Kernel</th>
                <th class="px-6 py-3">Architecture</th>
                <th class="px-6 py-3">Kubelet</th>
                <th class="px-6 py-3">Containerd</th>
                <th class="px-6 py-3">CPU</th>
                <th class="px-6 py-3">Memory</th>
                <th class="px-6 py-3">Ready</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $node)
                @php
                    $conditions = collect($node['status']['conditions'] ?? []);
                    $readyCondition = $conditions->firstWhere('type', 'Ready');
                    $isReady = $readyCondition && $readyCondition['status'] === 'True';
                    $nodeName = $node['metadata']['name'] ?? 'N/A';
                @endphp
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <td class="px-6 py-4">{{ $nodeName }}</td>
                    <td class="px-6 py-4">{{ $node['status']['nodeInfo']['osImage'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $node['status']['nodeInfo']['kernelVersion'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $node['status']['nodeInfo']['architecture'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $node['status']['nodeInfo']['kubeletVersion'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $node['status']['nodeInfo']['containerRuntimeVersion'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $node['status']['capacity']['cpu'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $node['status']['capacity']['memory'] ?? 'N/A' }}B</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold {{ $isReady ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200' }} rounded">
                            {{ $isReady ? 'Yes' : 'No' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        
    </table>
</div>



@endsection