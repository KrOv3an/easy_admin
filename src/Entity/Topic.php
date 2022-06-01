<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(TopicRepository::class)]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column()]
    private ?string $name;

    #[ORM\OneToMany('topic', Question::class)]
    private Collection $questions;

    /**
     *
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @param Question $question
     * @return $this
     */
    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setTopic($this);
        }

        return $this;
    }

    /**
     * @param Question $question
     * @return $this
     */
    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getTopic() === $this) {
                $question->setTopic(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getName();
    }
}
