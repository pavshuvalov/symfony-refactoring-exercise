<?php

namespace App\Entity\Service;

use Doctrine\ORM\Mapping as ORM;

/**
 * AntispamIp
 *
 * @ORM\Table(name="antispam_ip")
 * @ORM\Entity
 */
class AntispamIp
{
    /**
     * @var string
     *
     * @ORM\Column(name="`ipv4`", type="string", length=15, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ipv4;

    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $key;

    /**
     * @var int
     *
     * @ORM\Column(name="`expire`", type="integer", nullable=false)
     */
    private $expire;

    /**
     * @var int
     *
     * @ORM\Column(name="`count`", type="integer", nullable=false)
     */
    private $count;

    /**
     * @var bool
     *
     * @ORM\Column(name="`is_stat_sent`", type="boolean", nullable=false)
     */
    private $isStatSent;

    /**
     * @var json
     *
     * @ORM\Column(name="`extra`", type="json", nullable=false)
     */
    private $extra;

    function __construct(string $ipv4, string $key, int $expire, int $count, bool $isStatSent, array $extra)
    {
        $this->ipv4 = $ipv4;
        $this->key = $key;
        $this->expire = $expire;
        $this->count = $count;
        $this->isStatSent = $isStatSent ? 1 : 0;
        $this->extra = $extra;
    }

    public function incCount(): void
    {
        $this->count++;
    }

    public function getIpv4(): string
    {
        return $this->ipv4;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getExpire(): int
    {
        return $this->expire;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getIsStatSent(): bool
    {
        return $this->isStatSent == 1;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function setExpire(int $expire): void
    {
        $this->expire = $expire;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function setIsStatSent(bool $isStatSent): void
    {
        $this->isStatSent = $isStatSent ? 1 : 0;
    }
}
