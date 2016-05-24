<?php
execAllTest();

function execAllTest() {
    test1();
    test2();
    test3();
    test_exception1();
    test_exception2();
    test_exception3();
    echo 'Succeeded!!';
}

function test1() {
    // ARRANGE
    $salt = 40;
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
    $salt = 100;
    $probabilities = array(
        10,
        20,
        20,
        50
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
        assertEquals('不正な引数。$probabilitiesの総和は99。', $e->getMessage());
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
        assertEquals('不正な引数。$probabilitiesの総和は99。', $e->getMessage());
    }
}

class Util
{

    /**
     * ランダムな数字を返す。
     */
    public static function get_random($salt, array $probabilities) {
        if (!(0 <= $salt && $salt <= 99)) {
            throw new Exception('不正な引数。$saltの値は0～99。');
        }
        if (array_sum($probabilities) != 99) {
            throw new Exception('不正な引数。$probabilitiesの総和は99。');
        }
        $prevP = 0;
        foreach ($probabilities as $idx => $nextP) {
            if ($prevP <= $salt && $salt < $prevP + $nextP) {
                return $idx;
            }
        }
    }
}
