<?php

namespace App\Entity\Stat;

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

    function __construct(string $key, int $value, array $extra)
    {
        $this->key = $key;
        $this->value = $value;
        $this->extra = $extra;
    }

    public function incValue(): void
    {
        $this->value++;
    }
}
