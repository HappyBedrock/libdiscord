<?php

declare(strict_types=1);

namespace happype\openapi\libdiscord\webhook;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function json_encode;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_URL;

class Webhook {
	/** @var mixed[] */
	private array $data;
	private int $contentType;

	public function __construct() {}

	public function setContent(Content $content): self {
		$this->data[$content->getContentType()] = $content->getContent();
		$this->contentType = $content->getContentApplicationType();
		return $this;
	}

	public function send(string $url): void {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHttpHeaders());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getData());

		curl_exec($curl);
		curl_close($curl);
	}

	private function getHttpHeaders(): array {
		return match($this->contentType) {
			Content::CONTENT_APPLICATION_JSON => ["Content-Type: application/json"],
			Content::CONTENT_APPLICATION_FORM_DATA => ["Content-Type: multipart/form-data"]
		};
	}

	private function getData(): string|array|bool {
		return match ($this->contentType) {
			Content::CONTENT_APPLICATION_JSON => json_encode($this->data),
			Content::CONTENT_APPLICATION_FORM_DATA => $this->data
		};
	}
}