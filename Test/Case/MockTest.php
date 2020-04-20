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
        $this->notify('do something');
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

    /**
     *
     */
    public function doSomethingBad()
    {
        foreach ($this->observers as $observer) {
            /* @var Observer $observer */
            $observer->reportError(42, 'Something bad happened', $this);
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
    /**
     * Subject::doSomethig() が Observer::update() を呼び出す引数が正しいかテスト
     */
    public function testObserversAreUpdated()
    {
        $observer = $this->getMock('Observer', array('update'));

        $observer->expects($this->once())
                 ->method('update')
                 ->with($this->equalTo('do something'));

        $subject = new Subject('My subject');
        $subject->attach($observer);

        $subject->doSomething();
    }

    /**
     * Subject::doSomethigBad() が Observer::reportError() を呼び出す引数が正しいかそれぞれの引数についてテスト
     */
    public function testErrorReported()
    {
        $observer = $this->getMock('Observer', array('reportError'));
        $observer->expects($this->once())
                 ->method('reportError')
                 ->with($this->greaterThan(0),
                     $this->stringContains('Something'),
                     $this->anything());

        $observer2 = $this->getMock('Observer', array('reportError'));
        $observer2->expects($this->once())
                  ->method('reportError')
                  ->with($this->equalTo(42),
                      $this->stringStartsWith('Something'),
                      $this->isInstanceOf('Subject'));

        $subject = new Subject('My subject');
        $subject->attach($observer);
        $subject->attach($observer2);

        $subject->doSomethingBad();
    }

    /**
     * Subject::doSomethigBad() が Observer::reportError() を呼び出す引数が正しいかコールバック関数を用いてそれぞれの引数についてテスト
     */
    public function testErrorReportedCallback()
    {
        $observer = $this->getMock('Observer', array('reportError'));
        $observer->expects($this->any())
                 ->method('reportError')
                 ->with($this->greaterThan(0),
                     $this->stringContains('Something'),
                     $this->callback(function ($subject) {
                         return is_callable(array($subject, 'getName'))
                                && get_class($subject) == 'Subject'
                                && $subject->getName() == 'My own subject';
                     }));

        $observer2 = $this->getMock('Observer', array('reportError'));
        $observer2->expects($this->any())
                  ->method('reportError')
                  ->with(
                      $this->callback(function ($int) {
                          return $int > 0
                                 && $int < 43;
                      }),
                      $this->callback(function ($name) {
                          return substr($name, 0, 1) == 'S'
                                 && substr($name, -1, 1) == 'd';
                      }),
                      $this->callback(function ($subject) {
                          return is_callable(array($subject, 'getName'))
                                 && get_class($subject) == 'Subject'
                                 && $subject->getName() == 'My own subject';
                      }));

        $subject = new Subject('My own subject');
        $subject->attach($observer);

        $subject->doSomethingBad();
    }


}
