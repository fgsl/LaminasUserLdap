<?php
/**
 * Copyright (c) 2020 Flávio Gomes da Silva Lisboa (https://github.com/fgsl/LaminasUserLdap)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.txt that was distributed with this source code.
 *
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 *
 */
namespace LaminasUserLdap\Mapper;

use Laminas\Hydrator\ClassMethods;
use LaminasUser\Entity\UserInterface as UserEntityInterface;

class UserHydrator extends ClassMethods
{

    /**
     * Extract values from an object
     *
     * @param object $object
     * @return array
     * @throws \InvalidArgumentException
     */
    public function extract($object)
    {
        if (! $object instanceof UserEntityInterface) {
            throw new \InvalidArgumentException('$object must be an instance of LaminasUser\\Entity\UserInterface');
        }
        /* @var $object UserEntityInterface */
        $data = parent::extract($object);
        $data = $this->mapField('id', 'user_id', $data);
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param object $object
     * @return UserEntityInterface
     * @throws \InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (! $object instanceof UserEntityInterface) {
            throw new \InvalidArgumentException('$object must be an instance of LaminasUser\\Entity\UserInterface');
        }
        $data = $this->mapField('user_id', 'id', $data);
        return parent::hydrate($data, $object);
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}
