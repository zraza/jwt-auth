<?php

namespace Tymon\JWTAuth\Test\Providers\JWT;

use Mockery;
use Tymon\JWTAuth\Payload;
use Tymon\JWTAuth\PayloadFactory;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Claims\Issuer;
use Tymon\JWTAuth\Claims\IssuedAt;
use Tymon\JWTAuth\Claims\Expiration;
use Tymon\JWTAuth\Claims\NotBefore;
use Tymon\JWTAuth\Claims\Audience;
use Tymon\JWTAuth\Claims\Subject;
use Tymon\JWTAuth\Claims\JwtId;
use Tymon\JWTAuth\Claims\Custom;

class PayloadFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->claimFactory = Mockery::mock('Tymon\JWTAuth\Claims\Factory');
        $this->factory = new PayloadFactory($this->claimFactory, Request::create('/foo', 'GET'));
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_a_payload_when_passing_an_array_of_claims_to_make_method()
    {
        $this->claimFactory->shouldReceive('get')->once()->with('sub', 1)->andReturn(new Subject(1));
        $this->claimFactory->shouldReceive('get')->once()->with('iss', Mockery::any())->andReturn(new Issuer('/foo'));
        $this->claimFactory->shouldReceive('get')->once()->with('exp', time() + 3600)->andReturn(new Expiration(time() + 3600));
        $this->claimFactory->shouldReceive('get')->once()->with('iat', time())->andReturn(new IssuedAt(time()));
        $this->claimFactory->shouldReceive('get')->once()->with('jti', 'foo')->andReturn(new JwtId('foo'));
        $this->claimFactory->shouldReceive('get')->once()->with('nbf', time())->andReturn(new NotBefore(time()));

        $payload = $this->factory->make(['sub' => 1, 'jti' => 'foo']);

        $this->assertEquals($payload->get('sub'), 1);
        $this->assertInstanceOf('Tymon\JWTAuth\Payload', $payload);
    }

    /** @test */
    public function it_should_return_a_payload_when_chaining_claim_methods()
    {
        $this->claimFactory->shouldReceive('get')->once()->with('sub', 1)->andReturn(new Subject(1));
        $this->claimFactory->shouldReceive('get')->once()->with('iss', Mockery::any())->andReturn(new Issuer('/foo'));
        $this->claimFactory->shouldReceive('get')->once()->with('exp', time() + 3600)->andReturn(new Expiration(time() + 3600));
        $this->claimFactory->shouldReceive('get')->once()->with('iat', time())->andReturn(new IssuedAt(time()));
        $this->claimFactory->shouldReceive('get')->once()->with('jti', Mockery::any())->andReturn(new JwtId('foo'));
        $this->claimFactory->shouldReceive('get')->once()->with('nbf', time())->andReturn(new NotBefore(time()));
        $this->claimFactory->shouldReceive('get')->once()->with('foo', 'baz')->andReturn(new Custom('foo', 'baz'));

        $payload = $this->factory->sub(1)->foo('baz')->make();

        $this->assertEquals($payload->get('sub'), 1);
        $this->assertEquals($payload->get('jti'), 'foo');
        $this->assertEquals($payload->get('foo'), 'baz');

        $this->assertInstanceOf('Tymon\JWTAuth\Payload', $payload);
    }

    /** @test */
    public function it_should_set_the_ttl()
    {
        $this->factory->setTTL(12345);

        $this->assertEquals($this->factory->getTTL(), 12345);
    }
}
