<?php
namespace Tests\Feature;
use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_page_is_available(): void{$this->get('/login')->assertOk();}
    public function test_inactive_user_cannot_remain_authenticated(): void{$user=User::factory()->create(['status'=>'inactive']);$this->actingAs($user)->get('/dashboard')->assertRedirect('/login');}
}
