<?php

/**
 * Class Foo
 */
class Foo
{
    /**
     * @return string
     */
    public function functionA()
    {
        return 'hoge';
    }

    /**
     * @return string
     */
    public function functionB()
    {
        return 'fuga';
    }
}

/**
 * Class MockTest
 */
class MockTest extends CakeTestCase
{

    /**
     *
     */
    public function testWithoutMock()
    {
        $target = new Foo();
        $this->assertSame('hoge', $target->functionA());
        $this->assertSame('fuga', $target->functionB());
    }

    /**
     *
     */
    public function testMethodsEmpty()
    {
        $target = $this->getMock('Foo', array(), array());
        $this->assertSame(null, $target->functionA(), 'mockのmethodsが空なので処理消える');
        $this->assertSame(null, $target->functionB(), 'mockのmethodsが空なので処理消える');
    }

    /**
     *
     */
    public function testMethodsOneSide()
    {
        $target = $this->getMock('Foo', array('functionA'), array());
        $this->assertSame(null, $target->functionA(), '宣言したmethodを再定義していない');
        $this->assertSame('fuga', $target->functionB(),
            '一つでもmethodを定義していれば、ほかは元の関数が生きている');

        $target2 = $this->getMock('Foo', array('functionB'), array());
        $this->assertSame('hoge', $target2->functionA(), '元関数が生き');
        $this->assertSame(null, $target2->functionB(), 'method宣言するも関数');
    }

    /**
     *
     */
    public function testWithMock()
    {
        $target = $this->getMock('Foo', array(), array());
        $this->assertSame(null, $target->functionA(), 'mockのmethodsが空なので処理消える');
        $this->assertSame(null, $target->functionB(), 'mockのmethodsが空なので処理消える');

        /*
        $target->expects($this->once())
            ->method('functionA')
            ->will($this->returnValue("HOGE"));

        $this->assertSame('HOGE', $target->functionA());
        $this->assertSame(null, $target->functionB());
        */
    }

    /**
     *
     */
    public function testWithMockSideA()
    {
        $target = $this->getMock('Foo', array('functionA'), array());
        $this->assertSame(null, $target->functionA());
        $this->assertSame('fuga', $target->functionB());

        $target->expects($this->once())
               ->method('functionA')
               ->will($this->returnValue("HOGE"));

        $this->assertSame('HOGE', $target->functionA());
        $this->assertSame('fuga', $target->functionB());
    }

}
