<?php
/**
 * @package  Divante\VsbridgeIndexerCore
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgeIndexerCore\Index;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Divante\VsbridgeIndexerCore\Api\MappingInterface;
use Divante\VsbridgeIndexerCore\Api\ConvertValueInterface;

/**
 * Responsible for converting field value to: int, bool, float; depends on field type.
 */
class ConvertValue implements ConvertValueInterface
{
    /**
     * @var array
     */
    private $castMapping = [
        FieldInterface::TYPE_LONG => 'int',
        FieldInterface::TYPE_INTEGER => 'int',
        FieldInterface::TYPE_BOOLEAN => 'bool',
        FieldInterface::TYPE_DOUBLE => 'float',
    ];

    /**
     * @inheritdoc
     */
    public function execute(MappingInterface $mapping, string $field, $value)
    {
        $properties = $mapping->getMappingProperties()['properties'];
        $type = $this->getFieldTypeByCode($properties, $field);

        if (null === $type) {
            return $value;
        }

        if (null === $value) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                settype($v, $type);
            }
        } else {
            settype($value, $type);
        }

        return $value;
    }

    /**
     * @param array $mapping
     * @param string $field
     *
     * @return string|null
     */
    private function getFieldTypeByCode(array $mapping, string $field)
    {
        if (isset($mapping[$field]['type'])) {
            $type = $mapping[$field]['type'];

            if (isset($this->castMapping[$type])) {
                return $this->castMapping[$type];
            }

            if (strstr($field, 'is_') || strstr($field, 'has_')) {
                return 'bool';
            }

            return null;
        }

        return null;
    }
}
