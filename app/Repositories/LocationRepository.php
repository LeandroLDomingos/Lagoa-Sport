<?php 

namespace App\Repositories;

use App\DTO\Locations\CreateLocationDTO;
use App\DTO\Locations\EditLocationDTO;
use App\Models\Api\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LocationRepository
{
    public function __construct(protected Location $location)
    {
    }

    public function getPaginate(int $totalPerPage = 15, int $page = 1, string $filter = ''): LengthAwarePaginator
    {
        return $this->location->where(function ($query) use ($filter) {
            if ($filter !== '') {
                $query->where('name', 'LIKE', "%{$filter}%");
            }
        })->paginate($totalPerPage, ['*'], 'page', $page);
    }

    public function createNew(CreateLocationDTO $dto): Location
    {
        return $this->location->create((array)$dto);
    }

    public function findById(string $id, array $relations = []): ?Location
    {
        return $this->location->with($relations)->find($id);
    }

    public function update(EditLocationDTO $dto): bool
    {
        if (!$location = $this->findById($dto->id)) {
            return false;
        }
        return $location->update((array)$dto);
    }

    public function delete(string $id): bool
    {
        if (!$location = $this->findById($id)) {
            return false;
        }
        return $location->delete();
    }
}