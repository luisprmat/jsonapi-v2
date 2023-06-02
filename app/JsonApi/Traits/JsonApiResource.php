<?php

namespace App\JsonApi\Traits;

use App\JsonApi\Document;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait JsonApiResource
{
    abstract public function toJsonApi(): array;

    public static function identifier(Model $resource)
    {
        return Document::type($resource->getResourceType())
            ->id($resource->getRouteKey());
    }

    public static function identifiers(Collection $resource)
    {
        return $resource->isEmpty()
            ? Document::empty()
            : Document::type($resource->first()->getResourceType())
                ->ids($resource);
    }

    public function toArray($request): array
    {
        if ($request->filled('include')) {
            foreach ($this->getIncludes() as $include) {
                if ($include->resource instanceof MissingValue) {
                    continue;
                }

                $this->with['included'][] = $include;
            }
        }

        return Document::type($this->resource->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipLinks($this->getRelationshipLinks())
            ->links([
                'self' => route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource),
            ])->get('data');
    }

    public function getIncludes(): array
    {
        return [];
    }

    public function getRelationshipLinks(): array
    {
        return [];
    }

    public function withResponse($request, $response)
    {
        $response->header(
            'Location',
            route('api.v1.'.$this->getResourceType().'.show', $this->resource)
        );
    }

    public function filterAttributes(array $attributes): array
    {
        return array_filter($attributes, function ($value) {
            if (request()->isNotFilled('fields')) {
                return true;
            }

            $fields = explode(',', request('fields.'.$this->getResourceType()));

            if ($value === $this->getRouteKey()) {
                return in_array($this->getRouteKeyName(), $fields);
            }

            return $value;
        });
    }

    public static function collection($resources): AnonymousResourceCollection
    {
        $collection = parent::collection($resources);

        if (request()->filled('include')) {
            foreach ($resources as $resource) {
                foreach ($resource->getIncludes() as $include) {
                    if ($include->resource instanceof MissingValue) {
                        continue;
                    }

                    $collection->with['included'][] = $include;
                }
            }
        }

        $collection->with['links'] = ['self' => $resources->path()];

        return $collection;
    }
}
