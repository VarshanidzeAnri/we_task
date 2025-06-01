<?php

namespace App\Service;

class FileUploader
{
    public function __construct(
        protected readonly RandomStringGenerator $stringGenerator
    ){}

    public function uploader($pictureFile): string
    {
        $randomString = $this->stringGenerator->generate(10);
        $originalExtension = $pictureFile->guessExtension();
        $filename = $randomString . '.' . $originalExtension;

        return $filename;
    }
}
