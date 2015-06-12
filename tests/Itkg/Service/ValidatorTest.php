<?php
namespace Itkg\Service;
use Itkg\Service\Validator;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-11-05 at 15:08:49.
 */
abstract class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Validator;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Service\Validator::validate
     */
    public abstract function testValidate();
}