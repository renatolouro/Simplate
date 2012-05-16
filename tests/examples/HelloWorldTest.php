<?php
require_once 'CSimplate.php';

class HellowWordTest extends \PHPUnit_Framework_TestCase
{
    const TEMPLATE_FILE = '/helloworld.spl.html';
    const GENERATED_FILE = '/tmp/simplate-tests-helloworld.php';

    public function tearDown()
    {
        @unlink(self::GENERATED_FILE);
    }

    public function assertPreConditions()
    {
        $this->assertFileExists(PATH_TEMPLATES.self::TEMPLATE_FILE);
        $this->assertTrue(class_exists('CSimplate'));
    }

    public function testTemplate()
    {
        $object = new CSimplate(PATH_TEMPLATES.self::TEMPLATE_FILE, self::GENERATED_FILE);
        $object->record();
        ob_start();
        $hello = 'Foo';
        require self::GENERATED_FILE;
        $response = ob_get_clean();
        $this->assertContains('Foo', $response);
    }
}