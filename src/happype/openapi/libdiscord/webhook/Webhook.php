<?php

declare(strict_types=1);

namespace happype\openapi\libdiscord\webhook;

use happype\openapi\libdiscord\utils\AccessDeniedException;
use function count;
use function curl_close;
use function curl_errno;
use function curl_error;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function json_encode;
use function var_dump;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_URL;

class Webhook {

	private array $data;

	public function __construct() {}

	public function setContent(Content $content): self {
		$this->data[$content->getContentType()] = $content->getContent();
		return $this;
	}

	public function addEmbed(Embed $embed): self {
		if(count($this->data["embeds"] ?? []) == 10) {
			throw new AccessDeniedException("Discord allows sending only up to 10 embeds per web hook.");
		}

		$this->data["embeds"][] = $embed->getEmbedData();
		return $this;
	}

	public function send(string $url): void {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->data));

		curl_exec($curl);
		curl_close($curl);
	}
}