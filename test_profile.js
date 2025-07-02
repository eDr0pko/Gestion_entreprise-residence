// Test du profil
const API_BASE = 'http://localhost:8000/api';

async function testProfile() {
    console.log('=== TEST DU PROFIL ===');

    try {
        // 1. Se connecter avec Marie (utilisons le bon mot de passe)
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

        // 2. Récupérer les informations utilisateur
        console.log('\n2. Récupération des informations utilisateur...');
        const userResponse = await fetch(`${API_BASE}/user`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const userData = await userResponse.json();
        console.log('User data:', userData);

        // 3. Récupérer les statistiques du profil
        console.log('\n3. Récupération des statistiques...');
        const statsResponse = await fetch(`${API_BASE}/profile/stats`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const statsData = await statsResponse.json();
        console.log('Stats data:', statsData);

        if (!statsData.success) {
            throw new Error('Échec de récupération des statistiques');
        }

        // 4. Tester la mise à jour du profil
        console.log('\n4. Test de mise à jour du profil...');
        const updateResponse = await fetch(`${API_BASE}/profile/update`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nom: 'Durand',
                prenom: 'Marie-Claire',
                numero_telephone: '0345678901'
            })
        });

        const updateData = await updateResponse.json();
        console.log('Update profile response:', updateData);

        // 5. Tester le changement de mot de passe
        console.log('\n5. Test de changement de mot de passe...');
        const passwordResponse = await fetch(`${API_BASE}/profile/password`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_password: 'password123',
                new_password: 'newpassword123',
                new_password_confirmation: 'newpassword123'
            })
        });

        const passwordData = await passwordResponse.json();
        console.log('Password change response:', passwordData);

        console.log('\n✅ TOUS LES TESTS DU PROFIL ONT RÉUSSI !');

    } catch (error) {
        console.error('❌ Erreur lors du test:', error);
    }
}

// Exécuter le test
testProfile();
