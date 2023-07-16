<?php

namespace App\Service\Artwork;

class ArtworkTransform
{
    /**
     * @param array $data
     * @return array
     */
    public function transformArtworks(array $data): array
    {
        $artworks = [];
        foreach ($data as $originalArtworkData) {
            $originalArtworkData = (array)$originalArtworkData;
            $artworks[] = $this->transfomArtwork($originalArtworkData);
        }
        return $artworks;
    }


    /**
     * @param array $originalArtworkData
     * @return array
     */
    public function transfomArtwork(array $originalArtworkData): array
    {
        $artworkData = [
            "ID"     => $originalArtworkData["id"],
            "title"  => $originalArtworkData["title"],
            "author" => $originalArtworkData["artist_title"],
        ];
        $thumbData = (array)$originalArtworkData["thumbnail"];
        if (!empty($thumbData)) {
            $artworkData["thumbnail"] = $thumbData;
        }
        return $artworkData;
    }
}
