<?php

namespace AkeneoE3\Infrastructure;

use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use LogicException;

class WriteResultFactory
{
    public static function createFromResponse(iterable $response, ResourceCollection $resources): iterable
    {
        $codeFieldName = $resources->getResourceType()->getCodeFieldName();

        foreach ($response as $line) {
            // In some cases when a PATCH body is invalid,
            // Akeneo PHP client returns a response with null
            // instead of throwing an HTTP exception.
            if ($line === null) {
                throw new LogicException('Akeneo API error by PATCH: please check your rules.');
            }

            $code = $line[$codeFieldName];
            $resource = $resources->get($code);

            yield self::createFromResponseLine($line, $resource);
        }
    }

    public static function createFromResponseLine(array $line, Resource $resource): WriteResult
    {
        if ($line['status_code'] !== 422) {
            return Loaded::create($resource);
        }

        $error = self::getErrorMessage($line);

        return Failed::create($resource, $error);
    }

    private static function getErrorMessage(array $response): string
    {
        $messages = [$response['message'] ?? ''];

        foreach ($response['errors'] ?? [] as $error) {
            $messages[] = sprintf(
                ' - property: %s, attribute: %s, %s',
                $error['property'] ?? '?',
                $error['attribute'] ?? '?',
                $error['message'] ?? '?',
            );
        }

        return implode(PHP_EOL, $messages);
    }
}
