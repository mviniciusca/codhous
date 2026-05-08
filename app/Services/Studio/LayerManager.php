<?php

namespace App\Services\Studio;

class LayerManager {
    public function __construct(
        public array $layers = []
    ) {}

    public function addLayer(string $type, mixed $content, int $zIndex, array $style = [], array $position = []): self {
        $this->layers[] = [
            'type' => $type,
            'content' => $content,
            'z_index' => $zIndex,
            'style' => $style,
            'position' => $position,
        ];
        return $this;
    }

    public function fromJson(string $json): self {
        $data = json_decode($json, true);
        if (isset($data['layers'])) {
            $this->layers = $data['layers'];
        }
        return $this;
    }

    public function render(): array {
        return collect($this->layers)->sortBy('z_index')->toArray();
    }
}
