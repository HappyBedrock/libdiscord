<?php

declare(strict_types=1);

namespace happype\openapi\libdiscord\webhook;

use pocketmine\color\Color;
use function date;
use function hexdec;
use function sprintf;
use function strtotime;

class Embed {
	private array $embedData;

	private function __construct() {}

	public static function create(): Embed {
		return new Embed();
	}

	public function setTitle(string $title): self {
		return $this->set("title", $title);
	}

	public function setDescription(string $description): self {
		return $this->set("description", $description);
	}

	/**
	 * @param string $url Player will be redirected to this url if he clicks to the embed title
	 */
	public function setUrl(string $url): self {
		return $this->set("url", $url);
	}

	public function addTimestamp(): self {
		return $this->set("timestamp", date("c", strtotime("now")));
	}

	public function setColor(Color $color): self {
		return $this->set("color", hexdec(sprintf("%02x%02x%02x", $color->getR(), $color->getG(), $color->getB())));
	}

	public function setFooter(string $text, ?string $iconUrl = null, ?string $proxyIconUrl = null): self {
		$footer = [
			"text" => $text
		];

		if($iconUrl !== null) {
			$footer["icon_url"] = $iconUrl;
		}
		if($proxyIconUrl !== null) {
			$footer["proxy_icon_url"] = $proxyIconUrl;
		}

		$this->embedData["footer"] = $footer;
		return $this;
	}

	public function setImage(string $url, ?string $proxyUrl = null, ?int $height = null, ?int $width = null): self {
		$image = [
			"url" => $url
		];

		if($proxyUrl !== null) {
			$image["proxy_url"] = $proxyUrl;
		}
		if($height !== null) {
			$image["height"] = (string)$height;
		}
		if($width !== null) {
			$image["width"] = (string)$width;
		}

		$this->embedData["image"] = $image;
		return $this;
	}

	public function addField(string $name, string $value, bool $inline = false): self {
		$this->embedData["fields"][] = [
			"name" => $name,
			"value" => $value,
			"inline" => $inline
		];

		return $this;
	}

	private function set(string $key, mixed $value): self {
		$this->embedData[$key] = $value;
		return $this;
	}

	public function getEmbedData(): array {
		return $this->embedData;
	}
}