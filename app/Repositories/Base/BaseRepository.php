<?php 
namespace App\Repositories\Base;

use Rinvex\Repository\Repositories\EloquentRepository;

class BaseRepository extends EloquentRepository
{
	public function forceDelete($id)
    {
        $deleted = false;

        // Find the given instance
        $entity = $id instanceof Model ? $id : $this->find($id);

        if ($entity) {
            // Fire the deleted event
            $this->getContainer('events')->dispatch($this->getRepositoryId().'.entity.deleting', [$this, $entity]);

            // Delete the instance
            $deleted = $entity->forceDelete();

            // Fire the deleted event
            $this->getContainer('events')->dispatch($this->getRepositoryId().'.entity.deleted', [$this, $entity]);
        }

        return $deleted ? $entity : $deleted;
    }

    public function max($column){
        return $this->createModel()->max($column);
    }
 
}
