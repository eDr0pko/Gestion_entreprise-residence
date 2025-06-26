<template>
    <div class="flex items-center justify-between p-4 bg-gray-800 text-white">
        <h1 class="text-xl font-bold">Mon Application</h1>
        <nav>
            <NuxtLink to="/" class="mr-4">Accueil</NuxtLink>
            <NuxtLink to="/tournament">Test</NuxtLink>
        </nav>
        <div>
            <NuxtLink to="/login" class="text-blue-400 hover:text-blue-300">Connexion</NuxtLink>
            <NuxtLink to="/register" class="ml-4 text-blue-400 hover:text-blue-300">Inscription</NuxtLink>
        </div>

    </div>
</template>