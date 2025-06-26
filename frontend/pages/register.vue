<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Inscription</h2>
        <form>
            <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="name">Nom</label>
            <input
                id="name"
                type="text"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Votre nom"
                required
            />
            </div>
            <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="email">Email</label>
            <input
                id="email"
                type="email"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Votre email"
                required
            />
            </div>
            <div class="mb-6">
            <label class="block text-gray-700 mb-2" for="password">Mot de passe</label>
            <input
                id="password"
                type="password"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Votre mot de passe"
                required
            />
            </div>
            <button
            type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition"
            >
            S'inscrire
            </button>
        </form>
        </div>
    </div>
</template>