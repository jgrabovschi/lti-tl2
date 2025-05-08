<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kubernetes Controller</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts AND CSS Files -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="font-sans antialiased">

<div class="bg-slate-200 dark:bg-slate-500 overflow-y-auto p-4">
    <div class="flex-col sm:flex-row flex  justify-center items-center min-h-screen">

        <!-- Left: Login Form -->
        <div class="m-4 w-full md:w-1/2 lg:w-1/3 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
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
            <form action="{{ route('login.submit') }}" method="POST" class="max-w-sm mx-auto">
                @csrf
            
                <!-- Flex container for IP and Port -->
                <div class="mb-5 flex gap-4">
                    <!-- IP Address -->
                    <div class="w-2/3">
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Your controller's IP address
                        </label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                                      dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                               placeholder="x.x.x.x" required />
                        @error('address')
                            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <!-- Port -->
                    <div class="w-1/3">
                        <label for="port" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Port</label>
                        <input type="text" id="port" name="port" value="{{ old('port') }}" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                                      dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                               placeholder="6443" required />
                        @error('port')
                            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            
                <!-- Token -->
                <div class="mb-5">
                    <label for="token" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your token</label>
                    <input type="text" id="token" name="token" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                                  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                           placeholder="Token" />
                    @error('token')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>
            
                <!-- Save checkbox -->
                <div class="flex items-start mb-5">
                    <div class="flex items-center h-5">
                        <input id="save-hidden" name="save" type="hidden" value="0" />
                        <input id="save" name="save" type="checkbox" value="1"
                               class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300 
                                      dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                    </div>
                    <label for="save" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Save profile</label>
                </div>
            
                <!-- Submit button -->
                <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm 
                               w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Submit
                </button>
            </form>
            
        </div>

        <!-- Right: Past Logins -->
        <div class="m-4 w-full md:w-1/2 lg:w-1/3 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Profiles</h5>
            <p class="font-normal text-gray-700 dark:text-gray-400">These are the profiles that were used in the past, click on a line to autocomplete the login parameters.</p>
            @if ($profiles->count() == 0)
                <p class="font-normal mt-4 text-center text-gray-700 dark:text-gray-600">No profiles to show.</p>
            @else
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">

                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">IP Address</th>
                                <th scope="col" class="px-6 py-3">Port</th>
                                <th scope="col" class="px-6 py-3"></th> 
                                <th scope="col" class="px-6 py-3"></th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profiles as $profile)
                            

                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <td class="px-6 py-4">{{ $profile->address }}</td>
                                    <td class="px-6 py-4">{{ $profile->port }}</td>
                                    <td class="px-6 py-4">
                                        
                                        <form action="{{ route('login.submit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="port" value="{{ $profile->port }}" />
                                            <input type="hidden" name="address" value="{{ $profile->address }}" />
                                            <input type="hidden" name="token" value="{{ $profile->token }}" />
                                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                Login 
                                            </button>
                                        </form>
                                        
                                    </td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('profile.delete', $profile->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to forget this login?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                        
                            
                            @endforeach                        
                        </tbody>
                    </table>
                    
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function autoFillLoginForm(username, address) {
        // Fill the login form with the profile data
        document.getElementById('username').value = username;
        document.getElementById('address').value = address;
    }
</script>

</body>
</html>

