<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js + Tailwind Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.js'])
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <!-- Main Container -->
    <div class="max-w-2xl mx-auto space-y-8">
        
        <!-- Basic Text Demo -->
        <div x-data="{ message: 'Alpine.js + Tailwind Test' }" 
             class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-blue-600 mb-4" x-text="message"></h2>
        </div>

        <!-- Toggle Button Demo -->
        <div x-data="{ open: false }" 
             class="bg-white p-6 rounded-lg shadow-md">
            <button @click="open = !open"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-md transition-colors">
                Toggle Panel
            </button>
            
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90"
                 class="mt-4 p-4 bg-green-100 rounded-md">
                <p class="text-green-700">Alpine.js animations bekerja! 🎉</p>
            </div>
        </div>

        <!-- Counter Demo -->
        <div x-data="{ count: 0 }" 
             class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Counter Demo</h3>
            <div class="flex space-x-4">
                <button @click="count--" 
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                    -
                </button>
                <span class="text-2xl font-bold w-16 text-center" x-text="count"></span>
                <button @click="count++" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                    +
                </button>
            </div>
        </div>

        <!-- Form Input Demo -->
        <div x-data="{ name: '', email: '' }" 
             class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Form Input Demo</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2">Name:</label>
                    <input type="text" 
                           x-model="name" 
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Email:</label>
                    <input type="email" 
                           x-model="email" 
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="p-4 bg-gray-100 rounded-md">
                    <p>Name: <span x-text="name || 'Not set'" class="font-semibold"></span></p>
                    <p>Email: <span x-text="email || 'Not set'" class="font-semibold"></span></p>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu Demo -->
        <div x-data="{ isOpen: false }" 
             class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Dropdown Demo</h3>
            <div class="relative">
                <button @click="isOpen = !isOpen" 
                        class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md">
                    Menu
                </button>
                <div x-show="isOpen" 
                     @click.away="isOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="absolute mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Option 1</a>
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Option 2</a>
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Option 3</a>
                </div>
            </div>
        </div>

        <!-- Todo List Demo -->
        <div x-data="{ 
                todos: [], 
                newTodo: '',
                addTodo() {
                    if (this.newTodo.trim()) {
                        this.todos.push({ text: this.newTodo, done: false });
                        this.newTodo = '';
                    }
                }
            }" 
            class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Todo List Demo</h3>
            <div class="space-y-4">
                <div class="flex space-x-2">
                    <input type="text" 
                           x-model="newTodo" 
                           @keydown.enter="addTodo"
                           placeholder="Add new todo..."
                           class="flex-1 px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <button @click="addTodo" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        Add
                    </button>
                </div>
                <ul class="space-y-2">
                    <template x-for="(todo, index) in todos" :key="index">
                        <li class="flex items-center space-x-2">
                            <input type="checkbox" 
                                   x-model="todo.done"
                                   class="h-5 w-5 text-blue-500">
                            <span x-text="todo.text"
                                  :class="{ 'line-through': todo.done }"
                                  class="flex-1"></span>
                            <button @click="todos.splice(index, 1)" 
                                    class="text-red-500 hover:text-red-600">
                                Delete
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

    </div>
</body>
</html>