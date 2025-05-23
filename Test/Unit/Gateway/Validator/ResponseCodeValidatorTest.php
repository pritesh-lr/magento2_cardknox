<?php
/**
 * Copyright © 2024 Cardknox Development Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace CardknoxDevelopment\Cardknox\Test\Unit\Gateway\Validator;

use CardknoxDevelopment\Cardknox\Gateway\Validator\ResponseCodeValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Model\Method\Logger;
use CardknoxDevelopment\Cardknox\Gateway\Config\Config;
use PHPUnit\Framework\MockObject\MockObject;

class ResponseCodeValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResultInterfaceFactory|MockObject
     */
    private $resultFactory;

    /**
     * @var ResultInterface|MockObject
     */
    private $resultMock;

    /**
     * @var Logger
     */
    private $mockLogger;

    /**
     * @var Payment
     */
    private $paymentModel;

    /**
     * @var ResponseCodeValidator
     */
    private $validator;

    /**
     * @var Config
     */
    private $systemConfig;

    public function setUp(): void
    {
        $this->resultFactory = $this->createMock(ResultInterfaceFactory::class);
        $this->resultMock = $this->createMock(ResultInterface::class);
        $this->mockLogger = $this->createMock(Logger::class);
        $this->systemConfig = $this->createMock(Config::class);

        $this->resultFactory->method('create')
            ->willReturn($this->resultMock);

        $this->validator = new ResponseCodeValidator(
            $this->resultFactory,
            $this->systemConfig,
            $this->mockLogger
        );
    }

    /**
     * @param array $response
     * @param array $expectationToResultCreation
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $response, array $expectationToResultCreation)
    {
        $this->resultFactory->expects($this->once())
            ->method('create')
            ->with($expectationToResultCreation)
            ->willReturn($this->resultMock);

        $this->assertInstanceOf(
            ResultInterface::class,
            $this->validator->validate(['response' => $response])
        );
    }

    public static function validateDataProvider(): array
    {
        return [
            'fail_1' => [
                'response' => [],
                'expectationToResultCreation' => [
                    'isValid' => false,
                    'failsDescription' => [new \Magento\Framework\Phrase(' ', [])],
                    'errorCodes' => [new \Magento\Framework\Phrase('', [])]
                ],
            ],
            'fail_2' => [
                'response' => [ResponseCodeValidator::RESULT_CODE => ResponseCodeValidator::DECLINE],
                'expectationToResultCreation' => [
                    'isValid' => false,
                    'failsDescription' => [new \Magento\Framework\Phrase(' ', [])],
                    'errorCodes' => [new \Magento\Framework\Phrase('', [])]
                ],
            ],
            'success' => [
                'response' => [ResponseCodeValidator::RESULT_CODE => ResponseCodeValidator::SUCCESS],
                'expectationToResultCreation' => [
                    'isValid' => true,
                    'failsDescription' => [],
                    'errorCodes' => []
                ],
            ]
        ];
    }

    public function testValidateException()
    {
        $buildSubject = [
            'response' => null,
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Response does not exist');
        $this->validator->validate($buildSubject);
    }
}
