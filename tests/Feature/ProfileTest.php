<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => 1]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        Member::create([
            'user_id' => $user->id, 
            'member_id' => 'T2', 
            'employee_id' => 'E2', 
            'department' => 'D', 
            'position' => 'P', 
            'join_date' => now(), 
            'status' => 'active', 
            'credit_limit' => 0, 
            'address' => 'A', 
            'id_card_number' => '2', 
            'birth_date' => '1990-01-01', 
            'gender' => 'male', 
            'points' => 0, 
            'phone' => '081',
            'photo' => 'test.jpg'
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();
        Member::create([
            'user_id' => $user->id, 
            'member_id' => 'T3', 
            'employee_id' => 'E3', 
            'department' => 'D', 
            'position' => 'P', 
            'join_date' => now(), 
            'status' => 'active', 
            'credit_limit' => 0, 
            'address' => 'A', 
            'id_card_number' => '3', 
            'birth_date' => '1990-01-01', 
            'gender' => 'male', 
            'points' => 0, 
            'phone' => '081',
            'photo' => 'test.jpg'
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
        Member::create([
            'user_id' => $user->id, 
            'member_id' => 'T4', 
            'employee_id' => 'E4', 
            'department' => 'D', 
            'position' => 'P', 
            'join_date' => now(), 
            'status' => 'active', 
            'credit_limit' => 0, 
            'address' => 'A', 
            'id_card_number' => '4', 
            'birth_date' => '1990-01-01', 
            'gender' => 'male', 
            'points' => 0, 
            'phone' => '081',
            'photo' => 'test.jpg'
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();
        Member::create([
            'user_id' => $user->id, 
            'member_id' => 'T5', 
            'employee_id' => 'E5', 
            'department' => 'D', 
            'position' => 'P', 
            'join_date' => now(), 
            'status' => 'active', 
            'credit_limit' => 0, 
            'address' => 'A', 
            'id_card_number' => '5', 
            'birth_date' => '1990-01-01', 
            'gender' => 'male', 
            'points' => 0, 
            'phone' => '081',
            'photo' => 'test.jpg'
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
