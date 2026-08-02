<?php

namespace Tests\Unit;

use App\Events\UserRegistered;
use App\Listeners\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use PHPUnit\Framework\Attributes\Test;

class SendWelcomeEmailTest extends \Tests\TestCase
{
    #[Test]
    public function it_is_not_queue_bound(): void
    {
        $this->assertFalse(in_array(ShouldQueue::class, class_implements(SendWelcomeEmail::class), true));
    }
}
