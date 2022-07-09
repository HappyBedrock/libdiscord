<?php

declare(strict_types=1);

namespace happype\openapi\libdiscord\webhook;

use happype\openapi\libdiscord\utils\AccessDeniedException;
use function basename;
use function count;
use function curl_file_create;
use function file_exists;
use function mime_content_type;
use function strlen;

final class Content {
	public const CONTENT_APPLICATION_JSON = 0;
	public const CONTENT_APPLICATION_FORM_DATA = 1;

	private function __construct(
		private string $contentType,
		private mixed $content,
		private int $contentApplicationType = self::CONTENT_APPLICATION_JSON
	) {}

	public function getContentType(): string {
		return $this->contentType;
	}

	public function getContent(): mixed {
		return $this->content;
	}

	public function getContentApplicationType(): int {
		return $this->contentApplicationType;
	}

	public static function message(string $message): Content {
		if(strlen($message) > 2000) {
			throw new AccessDeniedException("Discord only allows messages with length up to 2000 chars.");
		}

		return new Content("content", $message);
	}

	public static function file(string $fileName): Content {
		if(!file_exists($fileName)) {
			throw new AccessDeniedException("File $fileName not found");
		}

		return new Content("file", curl_file_create($fileName, mime_content_type($fileName),  basename($fileName)), self::CONTENT_APPLICATION_FORM_DATA);
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