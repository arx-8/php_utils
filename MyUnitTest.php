<?php

class Target
{

    /**
     * 確率を配列で指定して、そのキーを返す。
     *
     * （例）
     * 10%の確率で0、20%の確率で1、20%の確率で2、50%の確率で3を返したい場合、
     * $randomSalt = ランダムな0～99の整数。
     * $rateList = array(10,20,20,50);
     * この時、$randomSalt = 30なら、2が返る。
     */
    public static function getRandomSpecifyRate($randomSalt, array $rateList) {
        if (!(0 <= $randomSalt && $randomSalt <= 99)) {
            throw new InvalidArgumentException('$randomSalt range in 0-99');
        }
        if (array_sum($rateList) != 100) {
            throw new InvalidArgumentException('$rateList sum = 100');
        }
        $range = 0;
        foreach ($rateList as $key => $rate) {
            $range += $rate;
            if ($randomSalt < $range) {
                return $key;
            }
        }
    }
}

class TargetTest extends BaseUnitTest
{

    public static function test1() {
        // ## Arrange ##
        $randomSalt = 0;
        $rateList = array(
            'patternA' => 10,
            'patternB' => 60,
            'patternC' => 20,
            'patternD' => 10
        );

        // ## Act ##
        $result = Target::getRandomSpecifyRate($randomSalt, $rateList);

        // ## Assert ##
        self::assertEquals('patternA', $result);
    }

    public static function test_exception1() {
        // ## Arrange ##
        $randomSalt = 100;
        $rateList = array(
            10,
            20,
            20,
            50
        );

        // ## Act ##
        try {
            $result = Target::getRandomSpecifyRate($randomSalt, $rateList);
            self::fail();
        } catch (InvalidArgumentException $e) {
            // ## Assert ##
            self::assertEquals('$randomSalt range in 0-99', $e->getMessage());
        }
    }
}

class BaseUnitTest
{

    protected static function assertEquals($expect, $actual) {
        if ($expect != $actual) {
            $msg = PHP_EOL;
            $msg .= '*** Assert fail. ***' . PHP_EOL;
            $msg .= '* expect : ' . $expect . PHP_EOL;
            $msg .= '* actual : ' . $actual . PHP_EOL;
            $msg .= '***' . PHP_EOL;
            throw new Exception($msg);
        }
    }

    protected static function fail() {
        $msg = PHP_EOL;
        $msg .= '*** Assert fail. ***' . PHP_EOL;
        $msg .= '* Don\'t reach here.' . PHP_EOL;
        $msg .= '***' . PHP_EOL;
        throw new Exception($msg);
    }
}

function execute() {
    $testClsName = 'TargetTest';
    $methods = get_class_methods($testClsName);
    foreach ($methods as $mtd) {
        $testClsName::$mtd();
    }
    echo PHP_EOL;
    echo 'All test passed!' . PHP_EOL;
}
execute();
