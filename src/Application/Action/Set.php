<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\ActtionTrace;
use Closure;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Set implements Action
{
    private ExpressionLanguage $expressionLanguage;

    /**
     * @var array
     */
    private array $options;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->options = $options;
    }

    public function getType(): string
    {
        return 'expression';
    }

    public function execute(array $item, Closure $traceCallback = null): ?array
    {
        $beforeValue = TransformerUtils::getFieldValue(
            $item,
            $this->options['field'],
            $this->options['scope'],
            $this->options['locale']
        )['data'] ?? '';

        $expression = $this->options['value'];
        $resultValue = $this->expressionLanguage->evaluate($expression, $item);

        if ($resultValue === $beforeValue) {
            return null;
        }

        if ($traceCallback !== null) {
            $this->notify($traceCallback, $item['identifier'] ?? '', $beforeValue, $resultValue);
        }

        return TransformerUtils::createFieldArray(
            $this->options['field'],
            $resultValue,
            $this->options['scope'],
            $this->options['locale']
        );
    }

    /**
     * @param mixed $beforeValue
     * @param mixed $resultValue
     */
    protected function notify(
        Closure $traceCallBack,
        string $itemIdentifier,
        $beforeValue,
        $resultValue
    ): void {
        $traceCallBack(
            new ActtionTrace(
                $itemIdentifier,
                $beforeValue,
                $resultValue
            )
        );
    }
}
