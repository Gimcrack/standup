<?php

namespace Tests;

use App\User;
use Exception;
use TestHelper;
use App\Exceptions\Handler;
use PHPUnit\Framework\Assert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $response = null;

    protected $headers = [];

    protected function setUp()
    {
        parent::setUp();

        Collection::macro('assertContains', function($value) {
            Assert::assertTrue($this->contains($value), "Failed to assert that the collection contains the expected value");
            return $this;
        });

        Collection::macro('assertEmpty', function() {
            Assert::assertCount(0, $this, "Failed to assert that the collection was empty");
            return $this;
        });

        Collection::macro('assertCount', function($count) {
            Assert::assertCount($count, $this, "Failed to assert that the collection had the expected count");
            return $this;
        });

        Collection::macro('assertNotEmpty', function() {
            Assert::assertTrue($this->count() > 0, "Failed to assert that the collection was not empty");
            return $this;
        });

        Collection::macro('assertMinCount', function($count) {
            Assert::assertTrue($this->count() >= $count, "Failed to assert that the collection had at least {$count} items");
            return $this;
        });
    }

    public function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(Exception $e) {}
            public function render($request, Exception $e) {
                throw $e;
            }
        });

        return $this;
    }

    /**
     * Create an admin and login
     * @method actingAsAdmin
     *
     * @return   $this
     */
    public function actingAsAdmin()
    {
        $user = factory(User::class)->states('admin')->create();

        $this
            ->actingAs($user)
            ->headers(['Authorization' => "Bearer {$user->api_token}"]);

        return $this;
    }

    /**
     * Create a user and login
     * @method actingAsUser
     *
     * @return   $this
     */
    public function actingAsUser()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($user)
            ->headers(['Authorization' => "Bearer {$user->api_token}"]);

        return $this;
    }

    /**
     * Assert that the event was dispatched and has the proper data
     * @method assertEvent
     *
     * @return      $this
     */
    public function assertEvent($event, $models = [])
    {
        Event::assertDispatched($event, function($e) use ($models) {
            // make sure the event has the expected models
            foreach($models as $model_type => $model) {
                if ( ! is_object($model) ) {
                    if ( $e->$model_type != $model ) return false;
                }
                else {
                    if ( ! $e->$model_type->is($model) ) return false;
                }
                    
            }
            return true;
        });
        return $this;
    }

    /**
     * Assert that the event was dispatched and has the proper data
     * @method assertEvent
     *
     * @return      $this
     */
    public function assertNotEvent($event, $models = [])
    {
        Event::assertNotDispatched($event, function($e) use ($models) {
            // make sure the event has the expected models
            foreach($models as $model_type => $model) {
                if ( ! $e->$model_type->is($model) ) return false;
            }
            return true;
        });
        return $this;
    }

    /**
     * Assert that the notification was sent and has the proper data
     * @method assertNotification
     *
     * @return   $this
     */
    public function assertNotification($notification, $user, $models = [])
    {
        Notification::assertSentTo( $user, $notification, function($n, $channels) use($models) {
            foreach($models as $model_type => $model) {
                if ( ! $n->$model_type->is($model) ) return false;
            }
            return true;
        });
    }

    /**
     * Assert that the notification was not sent
     * @method assertNotification
     *
     * @return   $this
     */
    public function assertNotificationNotSent($notification, $user, $models = [])
    {
        Notification::assertNotSentTo( $user, $notification, function($n, $channels) use($models) {
            foreach($models as $model_type => $model) {
                if ( ! $n->$model_type->is($model) ) return false;
            }
            return true;
        });
    }

    /**
     * Get the response
     *
     * @return     TestResponse | failure
     */
    public function response()
    {
        return $this->response ?? $this->fail("No response yet");
    }

    /**
     * Assert that the json data has the specified count
     * @method assertJsonCount
     *
     * @return   $this
     */
    public function assertJsonCount( $count )
    {
        $data = $this->response()->json();

        $this->assertCount($count, $data);

        return $this;
    }

    /**
     * Set some headers
     *
     * @param      <type>  $headers  The headers
     */
    public function headers($headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Set the referrer header
     * @method from
     *
     * @return   $this
     */
    public function from($from)
    {
        return $this->headers([ 'referer' => $from ]);
    }

    /**
     * Post to some endpoint with some data and save the response
     * @method post
     *
     * @return   $this
     */
    public function post($endpoint, array $data = [], array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        
        $this->response = parent::json('POST', $endpoint, $data, $this->headers);

        return $this;
    }

    /**
     * Get some endpoint with some data and save the response
     * @method get
     *
     * @return   $this
     */
    public function get($endpoint, array $data = [], array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        
        $this->response = parent::json('GET', $endpoint, $data, $this->headers);

        return $this;
    }

    /**
     * Patch some endpoint with some data and save the response
     * @method patch
     *
     * @return   $this
     */
    public function patch($endpoint, array $data = [], array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        
        $this->response = parent::json('PATCH', $endpoint, $data, $this->headers);

        return $this;
    }

    /**
     * Put some endpoint with some data and save the response
     * @method put
     *
     * @return   $this
     */
    public function put($endpoint, array $data = [], array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        
        $this->response = parent::json('PUT',$endpoint, $data, $this->headers);

        return $this;
    }

    /**
     * Delete some endpoint with some data and save the response
     * @method delete
     *
     * @return   $this
     */
    public function delete($endpoint, array $data = [], array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        
        $this->response = parent::json('DELETE', $endpoint, $data, $this->headers);

        return $this;
    }
}
