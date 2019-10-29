<?php


namespace Potelo\GuPayment\Iugu;

use Exception;
use Iugu_Factory;
use Iugu_SearchResult;
use Iugu_Subscription;

class IuguSubscriptionDecorator
{

    protected $subscription; //The object to decorate

    /**
     * IuguSubscriptionDecorator constructor.
     * @param Iugu_Subscription $subscription
     */
    function __construct(Iugu_Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->subscription, $method], $args);
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->subscription->$property;
    }

    /**
     * @param $property
     * @param $value
     * @return $this
     */
    public function __set($property, $value)
    {
        $this->subscription->$property = $value;
        return $this;
    }

    /**
     * @param $response
     * @return Iugu_SearchResult|null
     */
    public function createFromResponse($response) {
        return Iugu_Factory::createFromResponse(
            self::convertClassToObjectType(),
            $response
        );
    }

    /**
     * @param null $identifier
     * @return bool|Iugu_SearchResult
     */
    public function change_plan_simulation($identifier = null)
    {
        if ($this->is_new()) {
            return false;
        }

        if ($identifier == null) {
            return false;
        }
        try {
            $response = self::API()->request(
                'GET',
                static::url($this->subscription).'/change_plan_simulation/'.$identifier
            );
            if (isset($response->errors)) {
                return false;
            }

            return $response;
        } catch (Exception $e) {
            return false;
        }
    }
}
