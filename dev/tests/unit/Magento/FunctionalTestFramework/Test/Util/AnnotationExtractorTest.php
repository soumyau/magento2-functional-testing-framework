<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Proxy\Verifier;
use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\Test\Util\AnnotationExtractor;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestLoggingUtil;

class AnnotationExtractorTest extends TestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Annotation extractor takes in raw array and condenses it to expected format
     *
     * @throws \Exception
     */
    public function testExtractAnnotations()
    {
        // Test Data
        $testAnnotations = [
            "nodeName" => "annotations",
            "features" => [
                [
                    "nodeName" => "features",
                    "value" => "TestFeatures"
                ]
            ],
            "stories" => [
                [
                    "nodeName" => "stories",
                    "value" => "TestStories"
                ]
            ],
            "description" => [
                [
                    "nodeName" => "description",
                    "value" => "TestDescription"
                ]
            ],
            "severity" => [
                [
                    "nodeName" => "severity",
                    "value" => "CRITICAL"
                ]
            ],
            "group" => [
                [
                    "nodeName" => "group",
                    "value" => "TestGroup"
                ]
            ],
        ];
        // Perform Test
        $extractor = new AnnotationExtractor();
        $returnedAnnotations = $extractor->extractAnnotations($testAnnotations, "testFileName");

        // Asserts

        $this->assertEquals("TestFeatures", $returnedAnnotations['features'][0]);
        $this->assertEquals("TestStories", $returnedAnnotations['stories'][0]);
        $this->assertEquals("TestDescription", $returnedAnnotations['description'][0]);
        $this->assertEquals("CRITICAL", $returnedAnnotations['severity'][0]);
        $this->assertEquals("TestGroup", $returnedAnnotations['group'][0]);
    }

    /**
     * Annotation extractor should throw warning when required annotations are missing
     *
     * @throws \Exception
     */
    public function testMissingAnnotations()
    {
        // Test Data, missing title, description, and severity
        $testAnnotations = [
            "nodeName" => "annotations",
            "features" => [
                [
                    "nodeName" => "features",
                    "value" => "TestFeatures"
                ]
            ],
            "stories" => [
                [
                    "nodeName" => "stories",
                    "value" => "TestStories"
                ]
            ],
            "group" => [
                [
                    "nodeName" => "group",
                    "value" => "TestGroup"
                ]
            ],
        ];
        // Perform Test
        $extractor = new AnnotationExtractor();
        $returnedAnnotations = $extractor->extractAnnotations($testAnnotations, "testFileName");

        // Asserts
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'Test testFileName is missing required annotations.',
            [
                'testName' => 'testFileName',
                'missingAnnotations' => "title, description, severity"
            ]
        );
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
