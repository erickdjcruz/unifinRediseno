<?php

namespace Sugarcrm\IdentityProvider\Authentication\UserMapping;

trait FieldMapper
{
    protected $fieldDelimiter = '.';

    protected $customAttributesName = 'custom_attributes';


    /**
     * Map IdP's data to App User attributes.
     * @param array $entry
     * @param array $mapping
     * @return array
     */
    protected function mapEntry(array $entry, array $mapping): array
    {
        $result = [];

        foreach ($mapping as $idpKey => $appKey) {
            if (!array_key_exists($idpKey, $entry)) {
                continue;
            }
            $value = $entry[$idpKey];

            if ($this->isCustomAttribute($appKey)) {
                $result[$this->customAttributesName][] = $this->mapCustomAttribute($appKey, $value);
            } else {
                $result = $this->mapField($appKey, $value, $result);
            }
        }
        return $result;
    }

    /**
     * Map custom attribute.
     * @param string $name
     * @param string $value
     * @return array
     */
    private function mapCustomAttribute(string $name, string $value): array
    {
        $appKeyParts = explode($this->fieldDelimiter, $name);
        return [
            'name' => implode($this->fieldDelimiter, array_slice($appKeyParts, 1)),
            'value' => $value,
        ];
    }

    /**
     * @param string $name
     * @return bool
     */
    private function isCustomAttribute(string $name): bool
    {
        return explode($this->fieldDelimiter, $name)[0] === $this->customAttributesName;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function isComplexAttribute(string $name): bool
    {
        return !$this->isCustomAttribute($name) && count(explode($this->fieldDelimiter, $name)) > 1;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getComplexAttributeKey(string $name): string
    {
        if ($this->isComplexAttribute($name)) {
            return explode($this->fieldDelimiter, $name)[0];
        }
        return '';
    }

    /**
     * @param string $name
     * @return string
     */
    private function getComplexAttributeName(string $name): string
    {
        if ($this->isComplexAttribute($name)) {
            $attr = explode($this->fieldDelimiter, $name);
            return array_pop($attr);
        }

        return '';
    }

    /**
     * @param string $key
     * @param string $value
     * @param array $currentValue
     * @return array
     */
    private function mapField(string $key, string $value, array $currentValue): array
    {
        if (strstr($key, $this->fieldDelimiter)) {
            $pos = strpos($key, $this->fieldDelimiter);
            $parentKey = substr($key, 0, $pos);
            $childKey = substr($key, $pos + 1);
            $currentValue[$parentKey] = $currentValue[$parentKey] ?? [];
            $currentValue[$parentKey] = $this->mapField($childKey, $value, $currentValue[$parentKey]);
        } else {
            $currentValue[$key] = $value;
        }
        return $currentValue;
    }
}
