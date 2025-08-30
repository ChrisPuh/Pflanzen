<?php

declare(strict_types=1);

namespace App\Repositories\Shared;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template-covariant TModel of Model
 */
abstract class AbstractEloquentRepository
{
    /**
     * Get the model class associated with the repository.
     *
     * @return class-string<TModel>
     */
    abstract public function getModelClass(): string;

    /**
     * Get the base query builder for the model.
     *
     * @return Builder<TModel>
     */
    protected function baseQuery(): Builder
    {
        $modelClass = $this->getModelClass();

        return $modelClass::query();
    }

    /**
     * Get base query with user filtering applied via garden relationship.
     * Override this method if your model has different user relationship logic.
     *
     * @return Builder<TModel>
     */
    protected function queryForUserBase(int $user_id, bool $isAdmin): Builder
    {
        return $this->baseQuery()
            ->when(
                ! $isAdmin,
                fn (Builder $q) => $q->whereHas(
                    'garden',
                    fn (Builder $q2) => $q2->where('user_id', $user_id)
                )
            );
    }
}
