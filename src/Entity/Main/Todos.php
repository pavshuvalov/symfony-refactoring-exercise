<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * Todos
 *
 * @ORM\Table(name="todos", indexes={@ORM\Index(name="GET_completed", columns={"completed"})})
 * @ORM\Entity(repositoryClass="App\Repository\TodosRepository")
 */
class Todos
{
    /**
     * @var int
     *
     * @ORM\Column(name="`id`", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`text`", type="string", length=255, nullable=false)
     */
    private $text;

    /**
     * @var int
     *
     * @ORM\Column(name="`completed`", type="integer", nullable=false)
     */
    private $completed;

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCompleted(): int
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed ? 1 : 0;
    }
}
