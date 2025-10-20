<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values;

use Symfony\Component\Yaml\Yaml;

use function array_merge_recursive;
use function is_array;
use function is_string;
use function str_replace;

final readonly class ValuesFile
{
    /** @var array{find: array<string>, replace: array<string>} */
    private array $replacementPairs;

    /** @param array<string|int, mixed> $values */
    private function __construct(array $values)
    {
        $this->replacementPairs = $this->splitIntoStrReplaceArguments($this->decorateReplacementPairs($this->extractReplacementPairs($values)));
    }

    public static function createFromFile(string ...$valuesFiles): self
    {
        $values = [];

        foreach ($valuesFiles as $valuesFile) {
            $yaml = Yaml::parseFile($valuesFile);
            if (! is_array($yaml)) {
                continue;
            }

            $values = array_merge_recursive($values, $yaml);
        }

        return new self($values);
    }

    /**
     * @param array<string|int, mixed> $values
     *
     * @return array<string|int, mixed>
     */
    public function swapInValues(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_string($value)) {
                $values[$key] = str_replace($this->replacementPairs['find'], $this->replacementPairs['replace'], $value);
            }

            if (! is_array($value)) {
                continue;
            }

            $values[$key] = $this->swapInValues($value);
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $replacementPairs
     *
     * @return array{find: array<string>, replace: array<string>}
     */
    private function splitIntoStrReplaceArguments(array $replacementPairs): array
    {
        /** @var array<string> $find */
        $find = [];
        /** @var array<string> $replace */
        $replace = [];

        foreach ($replacementPairs as $key => $value) {
            $find[]    = $key;
            $replace[] = $value;
        }

        /** @phpstan-ignore return.type */
        return ['find' => $find, 'replace' => $replace];
    }

    /**
     * @param array<string, mixed> $replacementPairs
     *
     * @return array<string, mixed>
     */
    private function decorateReplacementPairs(array $replacementPairs): array
    {
        foreach ($replacementPairs as $key => $value) {
            $replacementPairs['${VALUES:' . $key . '}'] = $value;
            unset($replacementPairs[$key]);
        }

        return $replacementPairs;
    }

    /**
     * @param array<string|int, mixed> $values
     *
     * @return array<string, mixed>
     */
    private function extractReplacementPairs(array $values): array
    {
        $pairs = [];

        foreach ($values as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if (is_array($value)) {
                foreach ($this->extractReplacementPairs($value) as $k => $v) {
                    $pairs[$key . '.' . $k] = $v;
                }
            } else {
                $pairs[$key] = $value;
            }
        }

        return $pairs;
    }
}
