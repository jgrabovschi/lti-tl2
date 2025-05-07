@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6  w-md bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
    
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Configure Wireless Network</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Here you can configure the wireless network.</p>
    
    </div>
</div>
<form method="GET" action="{{ route('showInterfacesWireless') }}">
    
    <button type="sumbit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">
        Back
    </button>

</form>

<div class="w-full md:w-1/2 lg:w-1/3 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
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
    <form method="POST" class="max-w-sm mx-auto">
        @csrf
        @method('PATCH')
        <div class="mb-5">
        <label for="ssid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SSID</label>
        <input type="text" name="ssid" id="ssid" value="{{ $wlan->ssid }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="" />
        @error('ssid')
            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
        @enderror
        </div>

        <div class="mb-5">
            <label for="security-profiles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a security profile</label>
            <select name="security-profile" id="security-profiles" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach ($securityProfiles as $securityProfile)
                    <option {{ $securityProfile->name == $wlan->{'security-profile'} ? 'selected' : ''}} value="{{ $securityProfile->name }}">{{ $securityProfile->name }}</option>
                @endforeach              
            </select>
        </div>
        <div class="mb-5">
            <label for="band" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a band</label>
            <select name="band" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach ($bands as $band)
                <option {{ $band == $wlan->{'band'} ? 'selected' : ''}} value="{{ $band }}">
                    {{ $band }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="mb-5">
            <label for="mode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a mode</label>
            <select name="mode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach ($modes as $mode)
                <option {{ $mode == $wlan->{'mode'} ? 'selected' : ''}} value="{{ $mode }}">
                    {{ $mode }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="mb-5">
            <label for="frequency" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a frequency</label>
            <select name="frequency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach ($frequencies as $frequency)
                <option {{ $frequency == $wlan->{'frequency'} ? 'selected' : ''}} value="{{ $frequency }}">
                    {{ $frequency }}
                </option>
                @endforeach
            </select>
        </div>
        {{-- <div class="mb-5">
        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
        <input type="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
        </div> --}}
        
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
    </form>
    
</div>



@endsection