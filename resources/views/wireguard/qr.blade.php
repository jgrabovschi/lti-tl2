@extends('layout.main')

@section('main')

<div class="flex justify-center">
    <div class="block p-6 w-md bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">WireGuard Peer</h5>
        <p class="font-normal text-gray-700 dark:text-gray-400">Read the following QR code to configure a client automatically.</p>
    </div>
</div>

<form method="GET" action="{{ route('showWireguardPeers') }}">
    <button type="submit" class="mt-4 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-4">
        Back
    </button>
</form>
<div class="flex">
    <div class="w-min p-6 bg-white border border-gray-200 rounded-lg shadow-s m-4">
        {!! $qr !!}
    </div>
    <div class="w-full max-w-xl m-4">
        <div class="relative bg-gray-50 rounded-lg dark:bg-gray-700 p-4 h-88">
            <div class="max-h-full">
                <pre>
                    <code id="code-block" class="text-sm text-gray-900 dark:text-white">
                        {{ $peer[0]->conf }}
                    </code>
                </pre>
            </div>
            <div class="absolute top-2 end-2 bg-gray-50 dark:bg-gray-700">
                <button data-copy-to-clipboard-target="code-block" data-copy-to-clipboard-content-type="innerHTML" data-copy-to-clipboard-html-entities="true" class="text-gray-900 dark:text-gray-400 m-0.5 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 rounded-lg py-2 px-2.5 inline-flex items-center justify-center bg-white border-gray-200 border h-8">
                    <span id="default-message">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                            </svg>
                            <span class="text-xs font-semibold">Copy configuration</span>
                        </span>
                    </span>
                    <span id="success-message" class="hidden">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 text-blue-700 dark:text-blue-500 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                            </svg>
                            <span class="text-xs font-semibold text-blue-700 dark:text-blue-500">Copied</span>
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('[data-copy-to-clipboard-target]');
    
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-copy-to-clipboard-target');
                const contentType = button.getAttribute('data-copy-to-clipboard-content-type') || 'innerText';
                const htmlEntities = button.getAttribute('data-copy-to-clipboard-html-entities') === 'true';
    
                const codeBlock = document.getElementById(targetId);
                let text = codeBlock?.[contentType]?.trim();
    
                if (htmlEntities) {
                    const txt = document.createElement("textarea");
                    txt.innerHTML = text;
                    text = txt.value;
                }
    
                if (text) {
                    navigator.clipboard.writeText(text).then(() => {
                        const defaultMsg = button.querySelector('#default-message');
                        const successMsg = button.querySelector('#success-message');
    
                        defaultMsg.classList.add('hidden');
                        successMsg.classList.remove('hidden');
    
                        setTimeout(() => {
                            successMsg.classList.add('hidden');
                            defaultMsg.classList.remove('hidden');
                        }, 2000);
                    }).catch(err => {
                        console.error('Copy failed:', err);
                    });
                }
            });
        });
    });
</script>
    

@endsection
