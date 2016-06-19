<?php
namespace MicroPHPUnit;

/**
 * ビルトイン関数time()のOverride。
 * この名前空間内の全てのtime()は、この関数が実行される。
 *
 * @return number
 */
function time() {
    return strtotime('2016/06/01 12:00:00');
}

class AnyClass
{

    private function run() {
        $ret = array(
            'start' => time(),
            'end' => \time(),
        );
        return $ret;
    }
}

/**
 * UnitTest実行クラス
 */
class UnitTest extends BaseUnitTest
{

    private $target;

    public function __construct() {
        $this->target = new \ReflectionClass(new AnyClass());
    }

    public function test_example() {
        // ## Arrange ##
        // ## Act ##
        /** @var \ReflectionMethod $refMtd */
        $refMtd = $this->target->getMethod('run');
        $refMtd->setAccessible(true);
        $result = $refMtd->invoke(new AnyClass());

        // ## Assert ##
        $this->assertSame(2, count($result));
        $this->assertSame(strtotime('2016/06/01 12:00:00'), $result['start']);
        $this->assertSame(strtotime('2016/06/01 12:00:00'), $result['end']);
    }
}

/**
 * Framework ********************************************************************************************************
 */
class BaseUnitTest
{

    protected function assertSame($expect, $actual) {
        if ($expect !== $actual) {
            $msg = '';
            $msg .= 'expect : ' . var_export($expect, true) . PHP_EOL;
            $msg .= 'actual : ' . var_export($actual, true);
            throw new AssertionFailedError($msg);
        }
    }

    protected function fail() {
        throw new AssertionFailedError("Don't reach here.");
    }
}

class AssertionFailedError extends \Exception
{

    public function __construct($message) {
        $this->message = PHP_EOL;
        $this->message .= PHP_EOL;
        $this->message .= '*** Assert fail. ***' . PHP_EOL;
        $this->message .= $message . PHP_EOL;
        $this->message .= '***' . PHP_EOL;
        $this->message .= PHP_EOL;
    }
}

/**
 * UnitTestクラスを実行する。
 * 接頭辞が'test_'のメソッドのみ、テスト対象とする。
 *
 * @throws \Exception
 */
function execute() {
    $testClsName = 'MicroPHPUnit\UnitTest';
    $methods = get_class_methods($testClsName);
    if (count($methods) === 0) {
        throw new \Exception('*** Error. Method Not Found. ***');
    }

    // 接頭辞が'test_'のメソッドのみ、テスト対象とする
    $testMethods = array_filter($methods,
        function ($mtd) {
            $prefix = 'test_';
            return substr($mtd, 0, strlen($prefix)) === $prefix;
        });
    if (count($testMethods) === 0) {
        throw new \Exception('*** Error. TestMethod Not Found. ***');
    }

    echo PHP_EOL;
    echo '*** Execute UnitTest. ***' . PHP_EOL;
    echo PHP_EOL;
    $testCls = new $testClsName();
    foreach ($testMethods as $mtd) {
        echo "* running '$mtd()' ... ";
        $testCls->$mtd();
        echo 'passed.' . PHP_EOL;
    }
    echo PHP_EOL;
    echo '*** All UnitTest passed! ***' . PHP_EOL;
}
execute();
