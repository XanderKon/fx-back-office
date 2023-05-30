<?php

namespace App\Service\Import\Provider\Trait;

use App\Service\DTO\RatesDTO;
use App\Service\Import\Exception\DTOValidationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait HasParserToDTO
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    #[Required]
    public function setSerializerAndValidator(SerializerInterface $serializer, ValidatorInterface $validator): void
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    private function createDTO(string $response): RatesDTO
    {
        $dto = $this->serializer->deserialize($response, RatesDTO::class, 'json');
        $violations = $this->validator->validate($dto);

        if (0 !== count($violations)) {
            $messages = [];
            foreach ($violations as $error) {
                $messages[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new DTOValidationException((string) json_encode($messages));
        }

        return $dto;
    }
}
