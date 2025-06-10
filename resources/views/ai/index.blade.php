@extends('layout.main')

@section('main')

<!-- Chat Header -->
<div class="flex justify-center mt-6">
    <div class="block p-6 w-full max-w-3xl bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between">
            <div>
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Kube AI</h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">Your chat is saved in the session. It will persist until you log out or clear the chat.</p>
            </div>
            <div>
                <a href="{{ route('clearAIChat') }}" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Chat
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chat Area (fills remaining space) -->
<div class="flex flex-col justify-between h-[calc(100vh-280px)] max-w-3xl mx-auto mt-4 bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">

    <!-- Messages scrollable -->
    <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-700">
        <!-- Messages will be dynamically inserted here -->
    </div>

    <!-- Input fixed at bottom of chat -->
    <div class="border-t border-gray-200 dark:border-gray-600 p-4 bg-white dark:bg-gray-800">
        <div class="relative mx-auto">
            <input type="text" id="chat-input" placeholder="Write something..." class="rounded- block w-full p-4 pe-24 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            <button id="button-send" type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
        </div>
    </div>
    
</div>

<script type="module">
    // Initialize chat from session data
    var chat = @json(session('chat', []));
    var key = "{{ env('API_GEMINI_KEY') }}"

    if (chat.length === 0) {
        chat.push({
            "role": "model",
            "parts": [
                {
                    "text": "Hello! I am Kube AI, your assistant for K3s. How can I help you today?"
                }
            ]
        });
        saveMessageToSession(chat[0]); // Save initial message to session
        renderMessages(); // Render initial message
    }

    

    var spinner = false; // Spinner state
    var messagesContainer = document.getElementById('messages');
    
    function renderMessages() {
        messagesContainer.innerHTML = '';
        chat.forEach(function(message) {
            var messageDiv = document.createElement('div');
            messageDiv.innerHTML = `
            <div class="flex items-start gap-2.5 `+ (message.role == "model" ? '' : 'justify-end') +`">
                ` + (message.role == "model" ? '<div class="text-slate-600 dark:text-slate-300"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M480-40q-23 0-46-3t-46-8Q300 14 194.5-4.5T33-117q-45-74-29-159t77-143v-3Q19-479 4-562.5T32-720q37-63 102-95.5T271-838q32-57 87.5-89.5T480-960q66 0 121.5 32.5T689-838q72-10 137 22.5T928-720q43 74 28 157.5T879-422v3q61 58 77 143t-29 159Q871-23 765.5-4.5T572-51q-23 5-46 8t-46 3ZM288-90q-32-18-61-41.5T174-183q-24-28-42.5-60.5T101-311q-20 36-20 76.5t21 75.5q29 48 81.5 68.5T288-90Zm384 0q52 20 104.5-.5T858-159q21-35 21-75.5T859-311q-12 35-30.5 67.5T786-183q-24 28-52.5 51.5T672-90Zm-192-30q134 0 227-93t93-227q0-29-4.5-55.5T782-547q-29 20-64 31t-73 11q-102 0-173.5-71.5T400-750q-104 26-172 112t-68 198q0 134 93 227t227 93ZM360-350q-21 0-35.5-14.5T310-400q0-21 14.5-35.5T360-450q21 0 35.5 14.5T410-400q0 21-14.5 35.5T360-350Zm240 0q-21 0-35.5-14.5T550-400q0-21 14.5-35.5T600-450q21 0 35.5 14.5T650-400q0 21-14.5 35.5T600-350ZM94-544q9-33 23-63.5t33-57.5q19-27 41.5-51t48.5-44q-43 0-79.5 21T102-681q-20 32-22 67t14 70Zm772 0q16-35 14-70t-22-67q-22-37-58.5-58T720-760q26 20 48.5 44t41.5 51q19 27 33 57.5t23 63.5Zm-221-41q29 0 54-9t46-25q-21-32-50-57.5T632-721q-34-19-72-29t-80-10v10q0 69 48 117t117 48Zm-54-239q-20-26-49-41t-62-15q-33 0-62 15t-49 41q26-8 54-12t57-4q29 0 57 4t54 12ZM150-665Zm660 0Zm-330-85Zm0-90ZM174-183Zm612 0Z"/></svg></div>' : '') + `
                <div class="flex flex-col max-w-md leading-1.5 p-4 border-gray-200 bg-gray-100  dark:bg-gray-800 `+ (message.role == "model" ? 'rounded-e-xl rounded-es-xl ' : 'rounded-s-xl rounded-ee-xl text-end') + `">
                    <div class="flex items-center space-x-2 rtl:space-x-reverse `+ (message.role != "model" ? 'justify-end' : '')  +` ">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">` + (message.role == "model" ? "Kube AI" : "User") + `</span>
                    </div>
                    <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white">${message.parts[0].text}</p>
                </div>
                ` + (message.role == "model" ? '' : '<div class="text-slate-600 dark:text-slate-300"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M360-390q-21 0-35.5-14.5T310-440q0-21 14.5-35.5T360-490q21 0 35.5 14.5T410-440q0 21-14.5 35.5T360-390Zm240 0q-21 0-35.5-14.5T550-440q0-21 14.5-35.5T600-490q21 0 35.5 14.5T650-440q0 21-14.5 35.5T600-390ZM480-160q134 0 227-93t93-227q0-24-3-46.5T786-570q-21 5-42 7.5t-44 2.5q-91 0-172-39T390-708q-32 78-91.5 135.5T160-486v6q0 134 93 227t227 93Zm0 80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm-54-715q42 70 114 112.5T700-640q14 0 27-1.5t27-3.5q-42-70-114-112.5T480-800q-14 0-27 1.5t-27 3.5ZM177-581q51-29 89-75t57-103q-51 29-89 75t-57 103Zm249-214Zm-103 36Z"/></svg></div>') + `
            </div>
            `;
            messagesContainer.appendChild(messageDiv);
        });
        if (spinner) {
            var spinnerDiv = document.createElement('div');
            spinnerDiv.className = 'flex justify-center mt-2';
            spinnerDiv.innerHTML = '<div id="spinner" role="status"><svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg></div>';
            messagesContainer.appendChild(spinnerDiv);
        }
        else {
            var spinnerDiv = document.getElementById('spinner');
            if (spinnerDiv) {
                spinnerDiv.remove(); // Remove spinner if it exists
            }
        }
        messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to bottom
    }
    renderMessages();

    // Save message to session
    function saveMessageToSession(message) {
        return axios.post("{{ route('storeAIMessage') }}", {
            message: message
        });
    }

    function gemini() {
        spinner = true
        var input = document.getElementById('chat-input');
        if (!input) return; // Ensure input exists
        if (!axios) {
            console.error('Axios is not loaded. Please ensure you have included the Axios library.');
            return;
        }
        if (!key) {
            console.error('Google API key is not set. Please check your environment configuration.');
            return;
        }
        // Get user input and clear the input field
        if (!input.value) {
            console.warn('Input is empty. Please enter a message.');
            return;
        }
        var userMessage = input.value.trim();
        if (userMessage) {
            // Create user message
            var userMessageObj = {
                "role": "user",
                "parts": [
                    {
                        "text": userMessage
                    }
                ]
            };
            
            // Add to local chat and save to session
            chat.push(userMessageObj);
            saveMessageToSession(userMessageObj);
            
            input.value = ''; // Clear input
            renderMessages();
            
            axios.post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' + key, {
                "systemInstruction": {
                "parts": [
                    {"text": "You are a helpful and enthusiastic chatbot that is implemented inside a kubernetes controller app, K3s especifically. You are responsible for explain topics about the app itself and kubernetes in general. The app supports: listing resources in the Dashboard section; listing nodes in the Nodes section; listing, creating (a green button) and deleting namespaces in the Namespaces section; listing, creating (a green button) and deleting pods in the Pods section; listing, creating (a green button) and deleting deployments in the Deployments section; listing, creating (a green button) and deleting services (internal or external), with ingresses if needed, in the Services/Ingress section. All the 'creates' use simple forms, not yaml. Try to use very short and friendly messages. Use <strong>text</strong> tags to highlight words instead of **text** as you usually do."}
                ]
                },
                "contents": chat,
            }).then(function(response) {
                var modelMessage = response.data.candidates[0].content;
                chat.push(modelMessage);
                saveMessageToSession(modelMessage);
                spinner = false; // Stop spinner
                renderMessages();
            }).catch(function(error) {
                console.error("Error calling Gemini API:", error);
                spinner = false;
                
                // Add an error message to the chat
                var errorMessage = {
                    "role": "model",
                    "parts": [
                        {
                            "text": "Sorry, I encountered an error processing your request. Please try again later."
                        }
                    ]
                };
                chat.push(errorMessage);
                saveMessageToSession(errorMessage);
                renderMessages();
            });
        }
    }

    document.getElementById('button-send').addEventListener('click', gemini);
    document.getElementById('chat-input').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            gemini();
        }
    });
</script>

@endsection
