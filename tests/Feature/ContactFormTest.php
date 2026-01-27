<?php

namespace Tests\Feature;

use App\Enums\UnitType;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function contact_form_page_loads(): void
    {
        $response = $this->get('/kontak');

        $response->assertStatus(200);
        $response->assertSee('Kontak');
    }

    /** @test */
    public function contact_form_requires_all_fields(): void
    {
        $response = $this->post('/kontak', []);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    /** @test */
    public function contact_form_validates_email_format(): void
    {
        $response = $this->post('/kontak', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'subject' => 'Test Subject',
            'message' => 'Test message content here',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function contact_form_validates_minimum_lengths(): void
    {
        $response = $this->post('/kontak', [
            'name' => 'A', // too short
            'email' => 'test@test.com',
            'subject' => 'Hi', // too short
            'message' => 'Short', // too short
        ]);

        $response->assertSessionHasErrors(['name', 'subject', 'message']);
    }

    /** @test */
    public function contact_form_submission_creates_message(): void
    {
        $response = $this->post('/kontak', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'subject' => 'Test Subject Here',
            'message' => 'This is a test message with enough content.',
        ]);

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject Here',
            'status' => 'unread',
        ]);
    }

    /** @test */
    public function contact_form_sanitizes_input(): void
    {
        $response = $this->post('/kontak', [
            'name' => '<script>alert("xss")</script>John',
            'email' => 'john@example.com',
            'subject' => 'Test Subject Here',
            'message' => '<b>Bold</b> message content here',
        ]);

        $response->assertSessionHas('success');
        
        // Check that HTML tags are stripped
        $message = ContactMessage::latest()->first();
        $this->assertStringNotContainsString('<script>', $message->name);
        $this->assertStringNotContainsString('<b>', $message->message);
    }

    /** @test */
    public function honeypot_field_blocks_spam(): void
    {
        // Honeypot field should be empty - if filled, it's likely a bot
        $response = $this->post('/kontak', [
            'name' => 'Spammer',
            'email' => 'spam@example.com',
            'subject' => 'Spam Subject',
            'message' => 'Spam message content here',
            'website' => 'http://spam-site.com', // honeypot field
        ]);

        // Should still return success (to not alert spammer)
        $response->assertSessionHas('success');
        
        // But no message should be saved
        $this->assertDatabaseMissing('contact_messages', [
            'email' => 'spam@example.com',
        ]);
    }

    /** @test */
    public function contact_form_logs_ip_and_user_agent(): void
    {
        $response = $this->post('/kontak', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject Here',
            'message' => 'Test message content here',
        ]);

        $message = ContactMessage::latest()->first();
        
        $this->assertNotNull($message->ip_address);
        $this->assertNotNull($message->user_agent);
    }
}
