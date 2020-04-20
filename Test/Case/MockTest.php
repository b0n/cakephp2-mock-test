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

}
