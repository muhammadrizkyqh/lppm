<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::create([
        'name' => 'Prof. Test Profesor',
        'email' => 'profesor.test@stai-ali.ac.id',
        'nidn' => '3333333333',
        'phone' => '081234567003',
        'user_type' => 'dosen',
        'employment_status' => 'tetap',
        'functional_position' => 'profesor',
        'field_of_study' => 'Test Field',
        'address' => 'Test Address',
        'is_active' => true,
        'password' => Hash::make('password123')
    ]);

    echo "✅ SUCCESS: User dengan functional_position 'profesor' berhasil dibuat - ID: " . $user->id . "\n";
    echo "   Name: " . $user->name . "\n";
    echo "   Email: " . $user->email . "\n";
    echo "   Functional Position: " . $user->functional_position . "\n";
    echo "   Employment Status: " . $user->employment_status . "\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
