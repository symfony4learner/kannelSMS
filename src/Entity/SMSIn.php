<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SMSInRepository")
 */
class SMSIn
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $received_on;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $sms_origin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $whole_sms;


    public function getId()
    {
        return $this->id;
    }

    public function getReceivedOn(): ?\DateTimeInterface
    {
        return $this->received_on;
    }

    public function setReceivedOn(\DateTimeInterface $received_on): self
    {
        $this->received_on = $received_on;

        return $this;
    }

    public function getSmsOrigin(): ?string
    {
        return $this->sms_origin;
    }

    public function setSmsOrigin(string $sms_origin): self
    {
        $this->sms_origin = $sms_origin;

        return $this;
    }

    public function getWholeSms(): ?string
    {
        return $this->whole_sms;
    }

    public function setWholeSms(string $whole_sms): self
    {
        $this->whole_sms = $whole_sms;

        return $this;
    }

}
