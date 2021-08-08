<?php

namespace AkeneoEtl\Infrastructure\Command;

class ResourceNormaliser
{
    public function normalise(\AkeneoEtl\Domain\Resource $resource): array
    {
        $data = [];
        $array = $resource->toArray();

        foreach ($array as $key => $value) {
            if (in_array($key, ['values'])) {
                continue;
            }

            $data[] = $key . ': ' . (is_array($value) ? implode(', ', $value) : $value);
        }

        foreach ($array['values'] as $key => $attribute) {
            foreach ($attribute as $value) {
                $keyFormatted = sprintf('%s (%s,%s)', $key, $value['scope'] ?? 'null', $value['locale'] ?? 'null');
                $data[] = $keyFormatted . ': ' . $value['data'];
            }
        }

        return $data;
    }
}
