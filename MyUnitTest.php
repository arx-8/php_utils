<?php
///////////////////////////////////////////////////////////////////////////////
// （使い方）
// 1, UnitTest基底クラスを継承して、UnitTest実行クラスを定義する。
// 2, テストメソッドを「test_」を接頭辞にして、定義する。
// 3, このファイルをphpで実行する。（$ php MicroPHPUnit.php）
///////////////////////////////////////////////////////////////////////////////
namespace MicroPHPUnit;

/** @var string UnitTest基底クラス */
define('CLASS_NAME_OF_BASE_TEST_CASE', 'TestCase');

/**
 * ビルトイン関数time()のOverride。
 * この名前空間内の全てのtime()は、この関数が実行される。
 *
 * @return number
 */
function time() {
    return strtotime('2016/06/01 12:00:00');
}

class ExampleClass
{

    private function example() {
        $ret = array(
            'start' => time(),
            'end' => \time()
        );
        return $ret;
    }
}

/**
 * UnitTest実行クラス
 */
class ExamleClassTest extends TestCase
{

    private $target;

    public function __construct() {
        $this->target = new \ReflectionClass(new ExampleClass());
    }

    public function test_example() {
        // ## Arrange ##
        // ## Act ##
        /** @var \ReflectionMethod $refMtd */
        $refMtd = $this->target->getMethod('example');
        $refMtd->setAccessible(true);
        $result = $refMtd->invoke(new ExampleClass());

        // ## Assert ##
        $this->assertSame(2, count($result));
        $this->assertSame(strtotime('2016/06/01 12:00:00'), $result['start']);
        $this->assertSame(strtotime('2016/06/01 12:00:00'), $result['end']);
    }
}

///////////////////////////////////////////////////////////////////////////////
// Framework
///////////////////////////////////////////////////////////////////////////////
class TestCase
{

    protected function assertSame($expect, $actual) {
        if ($expect !== $actual) {
            $msg = 'expect : ' . var_export($expect, true) . PHP_EOL;
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
 * UnitTest実行制御クラス
 */
class UnitTestController
{

    public function run() {
        $utClsNames = $this->getDeclaredClassesOfUT(__NAMESPACE__);
        foreach ($utClsNames as $utClsName) {
            $utMethodNames = $this->getClassMethodsOfUT($utClsName);
            $this->executeUT($utClsName, $utMethodNames);
        }
        echo '*** All UnitTest passed! ***' . PHP_EOL;
    }

    /**
     * 引数名前空間で、UnitTest実行用のクラス名のリストを返す。
     *
     * @param string $namespaceName
     * @return array
     */
    private function getDeclaredClassesOfUT($namespaceName) {
        $baseTestClsName = $namespaceName . '\\' . CLASS_NAME_OF_BASE_TEST_CASE;
        $utClsNames = array_filter(get_declared_classes(),
            function ($clsName) use ($namespaceName, $baseTestClsName) {
                return
                    // この名前空間のクラス
                    substr($clsName, 0, strlen($namespaceName)) === $namespaceName
                    // UT実行クラス（＝UT基底クラスを親に持つクラス）
                    && strpos(get_parent_class($clsName), $baseTestClsName) !== false;
            });
        return $utClsNames;
    }

    /**
     * 引数クラス内で、UnitTest用のメソッド名のリストを返す。
     *
     * @param string $clsName
     * @throws \Exception
     * @return array
     */
    private function getClassMethodsOfUT($clsName) {
        $methodNames = get_class_methods($clsName);
        if (count($methodNames) === 0) {
            throw new \Exception("*** Error. Method not found in $clsName. ***");
        }

        // 接頭辞が'test_'のメソッドのみ、UnitTest用メソッドとする。
        $utMethodNames = array_filter($methodNames,
            function ($mtd) {
                $prefix = 'test_';
                return substr($mtd, 0, strlen($prefix)) === $prefix;
            });
        if (count($utMethodNames) === 0) {
            throw new \Exception("*** Error. TestMethod not found in $clsName. ***");
        }

        return $utMethodNames;
    }

    /**
     * @param string $utClsName
     * @param string $utMethodNames
     */
    private function executeUT($utClsName, $utMethodNames) {
        echo PHP_EOL;
        echo "*** Execute '$utClsName' ***" . PHP_EOL;
        echo PHP_EOL;
        $utObj = new $utClsName();
        foreach ($utMethodNames as $mtd) {
            echo "* running '$mtd()' ... ";
            $utObj->$mtd();
            echo 'passed.' . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

// execute
$ut = new UnitTestController();
$ut->run();
