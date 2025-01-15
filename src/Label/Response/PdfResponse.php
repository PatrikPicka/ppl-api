<?php

declare(strict_types=1);

namespace PTB\PPLApi\Label\Response;

use PTB\PPLApi\Common\ResponseInterface;

class PdfResponse implements ResponseInterface
{
	public function __construct(
		private string $pdfContent,
	) {
	}

	public static function fromArrayResponse(array $data): self
	{
		return new self($data['pdfContent']);
	}

	public function getContent(): string
	{
		return $this->pdfContent;
	}
}