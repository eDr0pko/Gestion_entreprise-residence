<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check existing user
$user = \App\Models\Personne::where('email', 'marie.durand@residence.com')->first();
if ($user) {
    echo "User found: " . $user->email . "\n";
    echo "Current hash: " . $user->mot_de_passe . "\n";
    
    // Test current password
    $testPasswords = ['password', 'Password', '123456', 'admin', 'marie'];
    
    foreach ($testPasswords as $testPassword) {
        if (\Illuminate\Support\Facades\Hash::check($testPassword, $user->mot_de_passe)) {
            echo "✅ Current password is: '$testPassword'\n";
            exit;
        }
    }
    
    echo "❌ None of the test passwords work. Setting new password...\n";
    
    // Set new password
    $user->mot_de_passe = \Illuminate\Support\Facades\Hash::make('password');
    $user->save();
    echo "✅ Password updated to 'password'\n";
    
    // Verify it works
    if (\Illuminate\Support\Facades\Hash::check('password', $user->mot_de_passe)) {
        echo "✅ Password verification successful!\n";
    } else {
        echo "❌ Password verification failed!\n";
    }
} else {
    echo "❌ User not found!\n";
}
