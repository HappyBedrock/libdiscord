<?php

declare(strict_types=1);

namespace happype\openapi\libdiscord\webhook;

use happype\openapi\libdiscord\utils\AccessDeniedException;
use function count;
use function strlen;

final class Content {

	private function __construct(
		private string $contentType,
		private mixed $content
	) {}

	public function getContentType(): string {
		return $this->contentType;
	}

	public function getContent(): mixed {
		return $this->content;
	}

	public static function message(string $message): Content {
		if(strlen($message) > 2000) {
			throw new AccessDeniedException("Discord only allows messages with length up to 2000 chars.");
		}

		return new Content("message", $message);
	}

	public static function file(string $content): Content {
		return new Content("file", $content);
	}

	public static function embeds(Embed ...$embed): Content {
		if (count($embed) > 10) {
			throw new AccessDeniedException("Discord only allows maximum of 10 embeds");
		}

		$embeds = [];
		foreach ($embed as $part) {
			$embeds[] = $part->getEmbedData();
		}

		return new Content("embeds", $embeds);
	}
}