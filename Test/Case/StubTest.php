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
 * Class StubTest
 */
class StubTest extends CakeTestCase
{

    /**
     * mockを使わずに元のclassの挙動を確認するテスト
     */
    public function testWithoutMock()
    {
        $target = new Foo();
        $this->assertSame('hoge', $target->functionA());
        $this->assertSame('fuga', $target->functionB());
    }

    /**
     * getMockの第二引数にnullを渡すテスト
     */
    public function testMethodsNull()
    {
        $target = $this->getMock('Foo', null, array());
        $this->assertSame('hoge', $target->functionA(),
            'mockのmethodsがnullなので処理そのまま');
        $this->assertSame('fuga', $target->functionB(),
            'mockのmethodsがnullなので処理そのまま');
    }

    /**
     * getMockの第二引数に空配列を渡すテスト
     */
    public function testMethodsEmpty()
    {
        $target = $this->getMock('Foo', array(), array());
        $this->assertSame(null, $target->functionA(), 'mockのmethodsが空なので処理消える');
        $this->assertSame(null, $target->functionB(), 'mockのmethodsが空なので処理消える');
    }

    /**
     * ひとつだけ関数名を渡すテスト
     */
    public function testMethodsOneSide()
    {
        $target = $this->getMock('Foo', array('functionA'), array());
        $this->assertSame(null, $target->functionA(), 'method宣言するも関数の再定義なし');
        $this->assertSame('fuga', $target->functionB(),
            '一つでもmethodを定義していれば、ほかは元の関数のまま');

        $target2 = $this->getMock('Foo', array('functionB'), array());
        $this->assertSame('hoge', $target2->functionA(), '元関数が生き');
        $this->assertSame(null, $target2->functionB(), 'method宣言するも関数の再定義なし');
    }

    /**
     * ひとつだけ関数名を渡し、再定義
     */
    public function testWithMock()
    {
        $target = $this->getMock('Foo', array('functionA'), array());
        $target->expects($this->once())
               ->method('functionA')
               ->will($this->returnValue("HOGE"));

        $this->assertSame('HOGE', $target->functionA(),
            'functionAは再定義されたので返り値が変わる');
        $this->assertSame('fuga', $target->functionB(), 'functionBはそのまま');
    }

    /**
     * 関数を指定せずに、戻り値を再定義するテスト
     */
    public function testNoDeclare()
    {
        $target = $this->getMock('Foo', array(), array());
        $target->expects($this->once())
               ->method('functionA')
               ->will($this->returnValue("HOGE"));
        $this->assertSame('HOGE', $target->functionA(),
            '関数名を宣言していないが、functionAの戻り値は再定義されたので返り値が変わる');

        $target->expects($this->exactly(2))
               ->method('functionB');
        $target->functionB();
        $actual = $target->functionB();
        $this->assertSame(null, $actual,
            '関数名を宣言せず、戻り値も再定義せず');
    }

    /**
     * 戻り値に関数の引数を返すテスト
     */
    public function testReturnArgument()
    {
        $target = $this->getMock('Foo', array(), array());
        $target->expects($this->any())
               ->method('functionA')
               ->will($this->returnArgument(0));
        $this->assertSame('HOGE', $target->functionA('HOGE'), '戻り値に引数を返す');
        $this->assertSame('PIYO', $target->functionA('PIYO'), '戻り値に引数を返す');
    }

    /**
     * 戻り値に自身の返すテスト
     */
    public function testReturnSelf()
    {
        $target = $this->getMock('Foo', array(), array());
        $target->expects($this->any())
               ->method('functionA')
               ->will($this->returnSelf());
        $this->assertSame($target, $target->functionA(), '戻り値に自身のインスタンスを返す');
    }

    /**
     * 戻り値を可変にできるテスト
     */
    public function testReturnValueMapStub()
    {
        $stub = $this->getMock('Foo');

        $map = array(
            array('a', 'b'),
            array('a', 'b', 'c'),
            array('a', 'b', 'c', 'd'),
            array('e', 'f', 'g', 'h'),
        );

        $stub->expects($this->any())
             ->method('functionA')
             ->will($this->returnValueMap($map));

        $this->assertEquals('b', $stub->functionA('a'));
        $this->assertEquals('c', $stub->functionA('a', 'b'));
        $this->assertEquals('d', $stub->functionA('a', 'b', 'c'));
        $this->assertEquals('h', $stub->functionA('e', 'f', 'g'));
    }

    /**
     * 戻り値に関数を指定するテスト
     */
    public function testReturnCallbackStub()
    {
        $stub = $this->getMock('Foo');

        $stub->expects($this->any())
             ->method('functionA')
             ->will($this->returnCallback('ucwords'));

        $this->assertEquals('Abc Def', $stub->functionA('abc def'),
            'ucwordsで文言の先頭の文字が大文字に');

        $stub->expects($this->any())
             ->method('functionB')
             ->will($this->returnCallback('str_repeat'));

        $this->assertEquals('aaa', $stub->functionB('a', 3),
            'str_repeatでaが3回繰り返される');
    }

    /**
     * 戻り値を順番に指定できるテスト
     */
    public function testOnConsecutiveCallsStub()
    {
        $stub = $this->getMock('Foo');

        $stub->expects($this->any())
             ->method('functionA')
             ->will($this->onConsecutiveCalls(2, 3, 5, 'a'));

        $this->assertEquals(2, $stub->functionA());
        $this->assertEquals(3, $stub->functionA());
        $this->assertEquals(5, $stub->functionA());
        $this->assertEquals('a', $stub->functionA());
    }

    /**
     * Exceptionを返すテスト
     *
     * @expectedException Exception
     * @expectedExceptionMessage This is exception.
     */
    public function testThrowExceptionStub()
    {
        $stub = $this->getMock('Foo');

        $stub->expects($this->any())
             ->method('functionA')
             ->will($this->throwException(new Exception('This is exception.')));

        $stub->functionA();
    }

}
