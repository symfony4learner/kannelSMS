<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SMSOutRepository")
 */
class SMSOut
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $send_to;

    /**
     * @ORM\Column(type="datetime")
     */
    private $received_on;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    public function getId()
    {
        return $this->id;
    }

    public function getSendTo(): ?string
    {
        return $this->send_to;
    }

    public function setSendTo(string $send_to): self
    {
        $this->send_to = $send_to;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
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


}
