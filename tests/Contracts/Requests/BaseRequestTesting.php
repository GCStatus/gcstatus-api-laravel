<?php

namespace Tests\Contracts\Requests;

use LogicException;
use Tests\TestCase;
use Illuminate\Validation\Factory;

abstract class BaseRequestTesting extends TestCase implements ShouldTestRequests
{
    /**
     * The validation rules for the request.
     *
     * @var array<string, mixed>
     */
    protected array $rules = [];

    /**
     * The validator instance.
     *
     * @var \Illuminate\Validation\Factory
     */
    protected Factory $validator;

    /**
     * Get the request class name.
     *
     * @var class-string
     */
    protected $request;

    /**
     * Setup test environment for request validation.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->app['validator'];

        $requestClass = $this->request();

        /** @var \Illuminate\Foundation\Http\FormRequest $requestObject */
        $requestObject = new $requestClass();

        if (method_exists($requestObject, 'rules')) {
            $this->rules = $requestObject->rules();
        } else {
            throw new LogicException("The request class must implement a rules method.");
        }
    }

    /**
     * Validate multiple fields based on the request's rules.
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    protected function validate(array $data): bool
    {
        $validation = $this->validator->make($data, $this->rules);

        return $validation->passes();
    }

    /**
     * Get validation errors for the provided data.
     *
     * @param array<string, mixed> $data
     * @return array<string, array<int, string>>
     */
    protected function getValidationErrors(array $data): array
    {
        $validation = $this->validator->make($data, $this->rules);

        $validation->passes();

        return $validation->errors()->toArray();
    }
}
