<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Http\Resources\Admin\CrackResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Models\{Game, Crack, Cracker, Protection, Status};

class CrackResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for CrackResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'cracked_at' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
        'game' => 'object',
        'status' => 'object',
        'cracker' => 'object',
        'protection' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<CrackResource>
     */
    public function resource(): string
    {
        return CrackResource::class;
    }

    /**
     * Provide a mock instance of Crack for testing.
     *
     * @return \App\Models\Crack
     */
    public function modelInstance(): Crack
    {
        $gameMock = Mockery::mock(Game::class);
        $statusMock = Mockery::mock(Status::class);
        $crackerMock = Mockery::mock(Cracker::class);
        $protectionMock = Mockery::mock(Protection::class);

        $crackMock = Mockery::mock(Crack::class)->makePartial();
        $crackMock->shouldAllowMockingMethod('getAttribute');

        $crackMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $crackMock->shouldReceive('getAttribute')->with('cracked_at')->andReturn(fake()->date());
        $crackMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $crackMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        $crackMock->shouldReceive('getAttribute')->with('game')->andReturn($gameMock);
        $crackMock->shouldReceive('getAttribute')->with('status')->andReturn($statusMock);
        $crackMock->shouldReceive('getAttribute')->with('cracker')->andReturn($crackerMock);
        $crackMock->shouldReceive('getAttribute')->with('protection')->andReturn($protectionMock);

        /** @var \App\Models\Crack $crackMock */
        return $crackMock;
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
