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
    const cpuData = @json($cpuUsage);
    const memData = @json($memoryUsage);

    google.charts.load('current', { packages: ['corechart'] });
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        const cpuArray = [['Node', 'CPU %', { role: 'tooltip', type: 'string' }]];
        const memArray = [['Node', 'Memory %', { role: 'tooltip', type: 'string' }]];

        let cpuMax = 0, memMax = 0;

        cpuData.forEach(node => {
            const tooltip = `${node.name}\n${node.percentage}%\n${node.used} millicores`;
            cpuArray.push([node.name, node.percentage, tooltip]);
            if (node.percentage > cpuMax) cpuMax = node.percentage;
        });

        memData.forEach(node => {
            const tooltip = `${node.name}\n${node.percentage}%\n${node.used} MiB`;
            memArray.push([node.name, node.percentage, tooltip]);
            if (node.percentage > memMax) memMax = node.percentage;
        });

        const dark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        const baseOptions = (maxVal, color) => ({
            backgroundColor: dark ? '#1F2937' : '#fff',
            titleTextStyle: { color: dark ? '#fff' : '#000' },
            hAxis: { textStyle: { color: dark ? '#ccc' : '#000' } },
            vAxis: {
                minValue: 0,
                maxValue: maxVal < 20 ? 20 : 100,
                textStyle: { color: dark ? '#ccc' : '#000' },
            },
            legend: 'none',
            chartArea: { width: '80%', height: '70%' },
            colors: [color],
            
        });

        const cpuOptions = {
            ...baseOptions(cpuMax, '#3B82F6'),
        };
        const memOptions = baseOptions(memMax, '#10B981');

        const cpuChart = google.visualization.arrayToDataTable(cpuArray);
        const memChart = google.visualization.arrayToDataTable(memArray);

        new google.visualization.ColumnChart(document.getElementById('cpu_chart')).draw(cpuChart, cpuOptions);
        new google.visualization.ColumnChart(document.getElementById('memory_chart')).draw(memChart, memOptions);
    }

    window.addEventListener('resize', () => {
        clearTimeout(window.resizing);
        window.resizing = setTimeout(drawCharts, 100);
    });
</script>






@endsection
