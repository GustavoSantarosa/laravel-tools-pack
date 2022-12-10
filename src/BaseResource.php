<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    public function prepare($request, array $resource): array
    {
        $resource = $this->include($request, $resource);

        return $resource;
    }

    private function include($request, $resource)
    {
        if ($request->include) {
            foreach (explode(',', $request->include) as $include) {
                $resource = array_merge($resource, [
                    $include => $this->$include,
                ]);
            }
        }

        return $resource;
    }
}
