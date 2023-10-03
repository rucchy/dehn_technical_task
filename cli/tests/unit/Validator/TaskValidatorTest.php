<?php

namespace unit\Validator;

use Console\App\Validator\TaskValidator;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class TaskValidatorTest extends TestCase
{
    private TaskValidator $taskValidator;

    protected function setUp(): void
    {
        $this->taskValidator = new TaskValidator();
    }

    public function testValidateID(): void
    {
        $test = Uuid::v4()->__toString();

        $this->assertSame($test, $this->taskValidator->validateID($test));
    }

    public function testValidateIDEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The ID can not be empty.');
        $this->taskValidator->validateID(null);
    }

    public function testValidateIDInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Invalid UUID.');
        $this->taskValidator->validateID('INVALID');
    }

    public function testValidateTitle(): void
    {
        $test = "Title";

        $this->assertSame($test, $this->taskValidator->validateTitle($test));
    }

    public function testValidateTitleEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The title can not be empty.');
        $this->taskValidator->validateTitle(null);
    }

    public function testValidateDescription(): void
    {
        $test = "Description";

        $this->assertSame($test, $this->taskValidator->validateDescription($test));
    }

    public function testValidateDescriptionEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The description can not be empty.');
        $this->taskValidator->validateDescription(null);
    }

    public function testValidateDueDate(): void
    {
        $test = new DateTime("now");
        $test = $test->format("Y-m-d");

        $this->assertSame($test, $this->taskValidator->validateDueDate($test));
    }

    public function testValidateDueDateEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The due date can not be empty.');
        $this->taskValidator->validateDueDate(null);
    }

    public function testValidateDueDateInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The format of the due date is wrong. It should be Y-m-d.');
        $this->taskValidator->validateDueDate('INVALID');
    }

    public function testValidateDueDateInvalidFormat(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The format of the due date is wrong. It should be Y-m-d.');
        $this->taskValidator->validateDueDate('02-12-2023');
    }

    public function testValidateDueDateInvalidTime(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The due date can not be earlier than today.');
        $this->taskValidator->validateDueDate('2023-01-01');
    }
}