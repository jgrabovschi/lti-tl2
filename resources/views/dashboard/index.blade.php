@extends('layout.main')

@section('main')

<div class="flex justify-center mb-8">
    <div class="block p-6 w-full max-w-2xl bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Dashboard</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">
            Here you can see the resources used by each node and pod in the cluster.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="w-full p-4 bg-white rounded-lg shadow dark:bg-gray-800">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">CPU Usage per Node</h3>
            <div id="cpu_chart" class="w-full" style="height: 400px;"></div>
        </div>

        <div class="w-full p-4 bg-white rounded-lg shadow dark:bg-gray-800">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Memory Usage per Node</h3>
            <div id="memory_chart" class="w-full" style="height: 400px;"></div>
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-8">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Pod</th>
                    <th scope="col" class="px-6 py-3">Namespace</th>
                    <th scope="col" class="px-6 py-3">Container</th>
                    <th scope="col" class="px-6 py-3">CPU (microcores)</th>
                    <th scope="col" class="px-6 py-3">Memory (MiB)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($metricsPods['items'] as $item)
                    @foreach ($item['containers'] as $container)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item['metadata']['name'] }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $item['metadata']['namespace'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $container['name'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ round(intval($container['usage']['cpu']) / 1000, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ round(intval(str_replace('Ki', '', $container['usage']['memory'])) / 1024, 2) }}
                            </td>                            
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    const $data = @json($metricsNodes);

    google.charts.load('current', { packages: ['corechart'] });
    google.charts.setOnLoadCallback(drawCharts);

    function parseCPU(nanoStr) {
        return parseInt(nanoStr) / 1_000_000;
    }

    function parseMemory(kiStr) {
        return parseInt(kiStr.replace('Ki', '')) / 1024;
    }

    function drawCharts() {
        const cpuDataArray = [['Node', 'CPU (millicores)']];
        const memDataArray = [['Node', 'Memory (MiB)']];

        $data.items.forEach(node => {
            const name = node.metadata.name;
            const cpu = parseCPU(node.usage.cpu);
            const mem = parseMemory(node.usage.memory);

            cpuDataArray.push([name, cpu]);
            memDataArray.push([name, mem]);
        });

        const darkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        const baseOptions = {
            backgroundColor: darkMode ? '#1F2937' : '#fff',
            titleTextStyle: { color: darkMode ? '#fff' : '#000' },
            hAxis: {
                titleTextStyle: { color: darkMode ? '#ccc' : '#000' },
                textStyle: { color: darkMode ? '#ccc' : '#000' }
            },
            vAxis: {
                titleTextStyle: { color: darkMode ? '#ccc' : '#000' },
                textStyle: { color: darkMode ? '#ccc' : '#000' }
            },
            legend: 'none',
            chartArea: {
                width: '80%',
                height: '70%'
            }
        };

        const cpuOptions = {
            ...baseOptions,
            colors: ['#3B82F6'],
        };

        const memOptions = {
            ...baseOptions,
            colors: ['#10B981'],
        };

        const cpuData = google.visualization.arrayToDataTable(cpuDataArray);
        const memData = google.visualization.arrayToDataTable(memDataArray);

        new google.visualization.ColumnChart(document.getElementById('cpu_chart')).draw(cpuData, cpuOptions);
        new google.visualization.ColumnChart(document.getElementById('memory_chart')).draw(memData, memOptions);
    }

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            drawCharts();
        }, 100);
    });

</script>

@endsection
