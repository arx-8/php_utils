<?php
execAllTest();

function execAllTest() {
    test1();
    test2();
    test3();
    test4();
    test5();
    test6();
    test_exception1();
    test_exception2();
    test_exception3();
    echo 'Succeeded!!';
}

function test1() {
    // ARRANGE
    $salt = 9;
    $probabilities = array(
        10,
        20,
        20,
        50
    );
    
    // ACT
    $result = Util::get_random($salt, $probabilities);
    
    // ASSERT
    assertEquals(0, $result);
}

function test2() {
    // ARRANGE
    $salt = 30;
    $probabilities = array(
        10,
        20,
        20,
        50
    );
    
    // ACT
    $result = Util::get_random($salt, $probabilities);
    
    // ASSERT
    assertEquals(2, $result);
}

function test3() {
    // ARRANGE
    $salt = 99;
    $probabilities = array(
        10,
        20,
        20,
        50
    );
    
    // ACT
    $result = Util::get_random($salt, $probabilities);
    
    // ASSERT
    assertEquals(3, $result);
}

function test4() {
    // ARRANGE
    $salt = 99;
    $probabilities = array(
        30,
        70
    );
    
    // ACT
    $result = Util::get_random($salt, $probabilities);
    
    // ASSERT
    assertEquals(1, $result);
}

function test5() {
    // ARRANGE
    $salt = 0;
    $probabilities = array(
        30,
        70
    );
    
    // ACT
    $result = Util::get_random($salt, $probabilities);
    
    // ASSERT
    assertEquals(0, $result);
}

function test6() {
    // ARRANGE
    $salt = 91;
    $probabilities = array(
        30,
        10,
        20,
        30,
        5,
        5
    );
    
    // ACT
    $result = Util::get_random($salt, $probabilities);
    
    // ASSERT
    assertEquals(4, $result);
}

function test_exception1() {
    // ARRANGE
    $salt = 100;
    $probabilities = array(
        10,
        20,
        20,
        50
    );
    
    // ACT
    try {
        $result = Util::get_random($salt, $probabilities);
        fail();
    } catch (Exception $e) {
        // ASSERT
        assertEquals('不正な引数。$saltの値は0～99。', $e->getMessage());
    }
}

function test_exception2() {
    // ARRANGE
    $salt = 99;
    $probabilities = array(
        10,
        20,
        20,
        50,
        100
    );
    
    // ACT
    try {
        $result = Util::get_random($salt, $probabilities);
        fail();
    } catch (Exception $e) {
        // ASSERT
        assertEquals('不正な引数。$probabilitiesの総和は100。', $e->getMessage());
    }
}

function test_exception3() {
    // ARRANGE
    $salt = 0;
    $probabilities = array(
        10,
        20,
        'str',
        20
    );
    
    // ACT
    try {
        $result = Util::get_random($salt, $probabilities);
        fail();
    } catch (Exception $e) {
        // ASSERT
        assertEquals('不正な引数。$probabilitiesの総和は100。', $e->getMessage());
    }
}

function assertEquals($expect, $actual) {
    if ($expect !== $actual) {
        throw new Exception('assert fail.');
    }
}

function fail() {
    throw new Exception('Dont reach here.');
}

class Util
{

    /**
     * ランダムな数字を返す。
     *
     * （例）
     * 10%の確率で0、20%の確率で1、20%の確率で2、50%の確率で3を返したい場合、
     * $probabilities = array(10,20,30,40);
     * $random_salt = ランダムな0～99の整数。
     * この時、$salt = 30なら、2が返る。
     */
    public static function get_random($random_salt, array $probabilities) {
        if (!(0 <= $random_salt && $random_salt <= 99)) {
            throw new Exception('不正な引数。$saltの値は0～99。');
        }
        if (array_sum($probabilities) != 100) {
            throw new Exception('不正な引数。$probabilitiesの総和は100。');
        }
        $prevP = 0;
        foreach ($probabilities as $idx => $nextP) {
            if ($prevP <= $random_salt && $random_salt < ($prevP + $nextP)) {
                return $idx;
            }
            $prevP += $nextP;
        }
    }
}
