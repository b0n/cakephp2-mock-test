<?php

/**
 * Class Subject
 */
class Subject
{
    /**
     * @var array
     */
    protected $observers = array();
    /**
     * @var
     */
    protected $name;

    /**
     * Subject constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  Observer  $observer
     */
    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     *
     */
    public function doSomething()
    {
        $this->notify('something');
    }

    /**
     *
     */
    public function doSomethingBad()
    {
        foreach ($this->observers as $observer) {
            $observer->reportError(42, 'Something bad happened', $this);
        }
    }

    /**
     * @param $argument
     */
    protected function notify($argument)
    {
        foreach ($this->observers as $observer) {
            $observer->update($argument);
        }
    }
}

/**
 * Class Observer
 */
class Observer
{
    /**
     * @param $argument
     */
    public function update($argument)
    {
    }

    /**
     * @param $errorCode
     * @param $errorMessage
     * @param  Subject  $subject
     */
    public function reportError($errorCode, $errorMessage, Subject $subject)
    {
    }
}

/**
 * Class MockTest
 */
class MockTest extends CakeTestCase
{

}
