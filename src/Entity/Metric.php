<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Metric
 *
 * @ORM\Table(name="metric")
 * @ORM\Entity
 */
class Metric
{
    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="string", length=255, nullable=false)
     * @ORM\Id
     */
    private $key;

    /**
     * @var int
     *
     * @ORM\Column(name="`value`", type="integer", nullable=false)
     */
    private $value;

    /**
     * @var json
     *
     * @ORM\Column(name="`extra`", type="json", nullable=false)
     */
    private $extra;

    public function incValue(): void
    {
        $this->value++;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }
}
