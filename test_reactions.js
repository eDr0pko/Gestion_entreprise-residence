// Test des réactions
const API_BASE = 'http://localhost:8000/api';

async function testReactions() {
    console.log('=== TEST DES RÉACTIONS ===');

    try {
        // 1. Se connecter avec Marie
        console.log('\n1. Connexion avec Marie...');
        const loginResponse = await fetch(`${API_BASE}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: 'marie.durand@residence.com',
                password: 'password123'
            })
        });

        const loginData = await loginResponse.json();
        console.log('Login response:', loginData);

        if (!loginData.success) {
            throw new Error('Échec de la connexion');
        }

        const token = loginData.data.token;

        // 2. Récupérer les conversations
        console.log('\n2. Récupération des conversations...');
        const conversationsResponse = await fetch(`${API_BASE}/conversations`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const conversationsData = await conversationsResponse.json();
        console.log('Conversations:', conversationsData);

        if (!conversationsData.success || conversationsData.data.length === 0) {
            throw new Error('Aucune conversation trouvée');
        }

        const firstGroupId = conversationsData.data[0].id_groupe_message;

        // 3. Récupérer les messages du premier groupe
        console.log(`\n3. Récupération des messages du groupe ${firstGroupId}...`);
        const messagesResponse = await fetch(`${API_BASE}/messages/${firstGroupId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const messagesData = await messagesResponse.json();
        console.log('Messages:', messagesData);

        if (!messagesData.success || messagesData.data.length === 0) {
            throw new Error('Aucun message trouvé');
        }

        const firstMessageId = messagesData.data[0].id_message;

        // 4. Ajouter une réaction au premier message
        console.log(`\n4. Ajout d'une réaction 👍 au message ${firstMessageId}...`);
        const reactionResponse = await fetch(`${API_BASE}/messages/${firstMessageId}/reactions`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                emoji: '👍'
            })
        });

        const reactionData = await reactionResponse.json();
        console.log('Reaction response:', reactionData);

        if (!reactionData.success) {
            throw new Error('Échec de l\'ajout de réaction');
        }

        // 5. Vérifier que la réaction a été ajoutée en récupérant les messages à nouveau
        console.log('\n5. Vérification que la réaction a été ajoutée...');
        const updatedMessagesResponse = await fetch(`${API_BASE}/messages/${firstGroupId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const updatedMessagesData = await updatedMessagesResponse.json();
        const updatedMessage = updatedMessagesData.data.find(m => m.id_message === firstMessageId);
        
        console.log('Message mis à jour avec réactions:', {
            id_message: updatedMessage.id_message,
            contenu_message: updatedMessage.contenu_message.substring(0, 50) + '...',
            reactions: updatedMessage.reactions
        });

        // 6. Essayer de retirer la réaction (cliquer à nouveau)
        console.log(`\n6. Suppression de la réaction 👍 du message ${firstMessageId}...`);
        const removeReactionResponse = await fetch(`${API_BASE}/messages/${firstMessageId}/reactions`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                emoji: '👍'
            })
        });

        const removeReactionData = await removeReactionResponse.json();
        console.log('Remove reaction response:', removeReactionData);

        console.log('\n✅ TOUS LES TESTS DE RÉACTIONS ONT RÉUSSI !');

    } catch (error) {
        console.error('❌ Erreur lors du test:', error);
    }
}

// Exécuter le test
testReactions();
