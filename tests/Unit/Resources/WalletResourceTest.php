<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{User, Wallet};
use App\Http\Resources\WalletResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class WalletResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for WalletResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'balance' => 'int',
        'user' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<WalletResource>
     */
    public function resource(): string
    {
        return WalletResource::class;
    }

    /**
     * Provide a mock instance of Wallet for testing.
     *
     * @return \App\Models\Wallet
     */
    public function modelInstance(): Wallet
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('getAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $userMock->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn('testuser');
        $userMock->shouldReceive('getAttribute')->with('birthdate')->andReturn('2000-01-01');
        $userMock->shouldReceive('getAttribute')->with('experience')->andReturn(1000);

        $walletMock = Mockery::mock(Wallet::class)->makePartial();
        $walletMock->shouldAllowMockingMethod('getAttribute');
        $walletMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $walletMock->shouldReceive('getAttribute')->with('balance')->andReturn(100);

        $walletMock->shouldReceive('relationLoaded')
            ->with('user')
            ->andReturn(true);

        $walletMock->shouldReceive('getAttribute')
            ->with('user')
            ->andReturn($userMock);

        /** @var \App\Models\Wallet $walletMock */
        return $walletMock;
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
