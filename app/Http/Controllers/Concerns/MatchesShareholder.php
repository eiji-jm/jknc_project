<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait MatchesShareholder
{
    protected function applyNameTokens(Builder $query, ?string $name, array $columns): void
    {
        $trimmed = trim((string) $name);
        if ($trimmed === '' || empty($columns)) {
            return;
        }

        $tokens = preg_split('/\s+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            return;
        }

        $query->orWhere(function (Builder $sub) use ($tokens, $columns) {
            foreach ($tokens as $token) {
                $sub->orWhere(function (Builder $tokenQuery) use ($token, $columns) {
                    foreach ($columns as $column) {
                        $tokenQuery->orWhere($column, 'like', '%' . $token . '%');
                    }
                });
            }
        });
    }
}
