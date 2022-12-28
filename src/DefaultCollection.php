<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class DefaultCollection extends ResourceCollection
{
    public function __construct(public $resourceClass, $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $content = [
            'success' => true,
            'data'    => $this->resourceClass::Collection($this->collection),
        ];

        if ($this->resource instanceof LengthAwarePaginator) {
            $content['pagination'] = [
                'total'        => $this->total(),
                'current_page' => $this->currentPage(),
                'next_page'    => $this->hasMorePages() ? $this->currentPage() + 1 : null,
                'last_page'    => $this->lastPage(),
                'per_page'     => $this->perPage(),
                'is_last_page' => !$this->hasMorePages(),
            ];
        }

        return $content;
    }

    public function withResponse($request, $response): void
    {
        $jsonResponse = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        unset($jsonResponse['links'], $jsonResponse['meta']);
        $response->setContent(json_encode($jsonResponse, \JSON_THROW_ON_ERROR));
    }
}
