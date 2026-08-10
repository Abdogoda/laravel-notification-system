<?php

namespace NotificationSystem\Tests\Feature;

use Illuminate\Support\Collection;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Resolvers\RecipientResolver;
use NotificationSystem\Tests\TestCase;

class RecipientResolverTest extends TestCase
{
    protected RecipientResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new RecipientResolver();
    }

    public function test_resolves_recipient_data_instance_directly()
    {
        $data = new RecipientData(id: 10, email: 'test@example.com');
        $resolved = $this->resolver->resolve($data);

        $this->assertInstanceOf(Collection::class, $resolved);
        $this->assertCount(1, $resolved);
        $this->assertEquals('test@example.com', $resolved->first()->email);
    }

    public function test_resolves_closure_target()
    {
        $resolved = $this->resolver->resolve(fn () => [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ]);

        $this->assertCount(2, $resolved);
        $this->assertEquals('Alice', $resolved->first()->name);
        $this->assertEquals('Bob', $resolved->last()->name);
    }

    public function test_resolves_array_of_arrays()
    {
        $resolved = $this->resolver->resolve([
            ['id' => 101, 'email' => 'user1@test.com'],
            ['id' => 102, 'email' => 'user2@test.com'],
        ]);

        $this->assertCount(2, $resolved);
        $this->assertEquals('user1@test.com', $resolved->first()->email);
    }

    public function test_resolves_mixed_array_with_recipient_data_instances()
    {
        $rd = new RecipientData(id: 200, email: 'rd@test.com', type: 'custom');
        $resolved = $this->resolver->resolve([
            $rd,
            ['id' => 201, 'email' => 'raw@test.com', 'type' => 'array'],
        ]);

        $this->assertCount(2, $resolved);
        $this->assertEquals('rd@test.com', $resolved->first()->email);
        $this->assertEquals('raw@test.com', $resolved->last()->email);
    }

    public function test_returns_empty_collection_for_unsupported_target()
    {
        $resolved = $this->resolver->resolve(null);
        $this->assertTrue($resolved->isEmpty());

        $resolvedInt = $this->resolver->resolve(12345);
        $this->assertTrue($resolvedInt->isEmpty());
    }
}
