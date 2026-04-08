<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RonasIT\AutoDoc\Services\SwaggerService as BaseSwaggerService;

class SwaggerService extends BaseSwaggerService
{
    protected ?Request $currentRequest = null;
    protected int $currentResponseStatus = 0;
    protected array $currentIgnoredParameters = [];

    public function addData(Request $request, $response)
    {
        $this->currentRequest = $request;
        $this->currentResponseStatus = (int) $response->getStatusCode();

        parent::addData($request, $response);
    }

    protected function saveDefinitions($objectName, $rules, $attributes, array $annotations): void
    {
        $this->currentIgnoredParameters = $this->getIgnoredParameters($rules, $annotations);

        $data = [
            'type' => 'object',
            'properties' => [],
        ];

        foreach ($rules as $parameter => $rule) {
            $rulesArray = is_array($rule) ? $rule : explode('|', $rule);

            if ($this->shouldIgnoreParameterForSwagger($parameter, $rulesArray, $annotations)) {
                continue;
            }

            $parameterType = $this->getParameterType($rulesArray);
            $this->saveParameterType($data, $parameter, $parameterType);

            $uselessRules = $this->ruleToTypeMap;
            $uselessRules['required'] = 'required';
            $uselessRules['swagger_ignore'] = 'swagger_ignore';

            if (in_array('required', $rulesArray, true)) {
                $data['required'][] = $parameter;
            }

            $rulesArray = array_flip(array_diff_key(array_flip($rulesArray), $uselessRules));

            $this->saveParameterDescription($data, $parameter, $rulesArray, $attributes, $annotations);
        }

        $data['example'] = $this->generateExample($data['properties']);
        $this->data['components']['schemas']["{$objectName}Object"] = $data;
    }

    protected function saveGetRequestParameters($rules, array $attributes, array $annotations): void
    {
        $ignoredParameters = $this->getIgnoredParameters($rules, $annotations);
        $parameters = &$this->getCurrentOperationParameters();

        foreach ($rules as $parameter => $rule) {
            $validation = is_array($rule) ? $rule : explode('|', $rule);

            if (in_array($parameter, $ignoredParameters, true)) {
                continue;
            }

            $description = Arr::get($annotations, $parameter);

            if (empty($description)) {
                $description = Arr::get($attributes, $parameter, implode(', ', $validation));
            }

            $existedParameter = Arr::first($parameters, function ($existedParameter) use ($parameter) {
                return $existedParameter['name'] === $parameter;
            });

            if (empty($existedParameter)) {
                $parameterDefinition = [
                    'in' => 'query',
                    'name' => $parameter,
                    'description' => $description,
                    'schema' => [
                        'type' => $this->getParameterType($validation),
                    ],
                ];

                if (in_array('required', $validation, true)) {
                    $parameterDefinition['required'] = true;
                }

                $parameters[] = $parameterDefinition;
            }
        }
    }

    protected function requestHasMoreProperties($actionName): bool
    {
        if ($this->shouldRefreshRequestExample()) {
            return true;
        }

        return parent::requestHasMoreProperties($actionName);
    }

    protected function shouldIgnoreParameterForSwagger(string $parameter, array $rules, array $annotations): bool
    {
        $ignoredParameters = $this->getIgnoredParameters($rules, $annotations);

        if (in_array($parameter, $ignoredParameters, true)) {
            return true;
        }

        return in_array('swagger_ignore', $rules, true);
    }

    /**
     * @param array<string, mixed> $rules
     * @param array<string, mixed> $annotations
     * @return list<string>
     */
    protected function getIgnoredParameters(array $rules, array $annotations): array
    {
        $ignoredParameters = $this->getIgnoredParametersFromAnnotations($annotations);

        foreach ($rules as $parameter => $rule) {
            $rulesArray = is_array($rule) ? $rule : explode('|', $rule);

            if (in_array('swagger_ignore', $rulesArray, true)) {
                $ignoredParameters[] = $parameter;
            }
        }

        return array_values(array_unique($ignoredParameters));
    }

    /**
     * @return list<string>
     */
    protected function getIgnoredParametersFromAnnotations(array $annotations): array
    {
        $rawValue = Arr::get($annotations, 'swaggerIgnore', '');

        if (!is_string($rawValue) || $rawValue === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $parameter): string => trim(Str::before($parameter, ' ')),
            explode(',', $rawValue)
        )));
    }

    protected function shouldRefreshRequestExample(): bool
    {
        if (!$this->currentRequest) {
            return false;
        }

        if ($this->currentResponseStatus < 200 || $this->currentResponseStatus >= 300) {
            return false;
        }

        return !empty($this->currentRequest->all());
    }

    protected function generateExample($properties): array
    {
        if (!$this->currentRequest) {
            return [];
        }

        $parameters = $this->replaceObjectValues($this->currentRequest->all());

        foreach ($this->currentIgnoredParameters as $parameter) {
            Arr::forget($parameters, $parameter);
        }

        $example = [];

        $this->replaceNullValues($parameters, $properties, $example);

        return $example;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function &getCurrentOperationParameters(): array
    {
        $method = strtolower($this->currentRequest?->getMethod() ?? 'get');
        $uri = $this->getCurrentOperationUri();

        if (!isset($this->data['paths'][$uri][$method]['parameters'])) {
            $this->data['paths'][$uri][$method]['parameters'] = [];
        }

        return $this->data['paths'][$uri][$method]['parameters'];
    }

    protected function getCurrentOperationUri(): string
    {
        if (!$this->currentRequest || !$this->currentRequest->route()) {
            return '/';
        }

        $uri = '/' . ltrim($this->currentRequest->route()->uri(), '/');
        $basePath = preg_replace("/^\//", '', (string) $this->config['basePath']);

        $preparedUri = preg_replace("/^{$basePath}/", '', ltrim($uri, '/'));

        return '/' . ltrim((string) $preparedUri, '/');
    }
}
