<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Datatable
{
    public function scopeDatatable(Builder $query)
    {
        $model = $query->getModel();
        $columns = $model->dataTableColumns ?? [];
        $request = request();

        $search = $request->query('search');
        $query->when($search, function ($query) use ($columns, $search) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col => $flags) {
                    if (Str::contains($flags, 'searchable')) {
                        if (Str::contains($col, '.')) {
                            [$relation, $relCol] = explode('.', $col, 2);
                            $q->orWhereHas($relation, function ($qr) use ($relCol, $search) {
                                $qr->where($relCol, 'like', "%$search%");
                            });
                        } else {
                            $q->orWhere($col, 'like', "%$search%");
                        }
                    }
                }
            });
        });

        $sort = $request->query('sort');
        $direction = 'asc';
        if ($sort && Str::contains($sort, ':')) {
            [$sort, $direction] = explode(':', $sort, 2);
        }

        $query->when($sort && isset($columns[$sort]), function ($query) use ($columns, $sort, $direction) {
            if (isset($columns[$sort]) && Str::contains($columns[$sort], 'sortable')) {
                if (!Str::contains($sort, '.')) {
                    $query->orderBy($sort, $direction);
                } else {
                    [$relation, $relationColumn] = explode('.', $sort, 2);
                    $query->withAggregate($relation, $relationColumn);
                    $query->orderBy(Str::snake($relation . '_' . $relationColumn), $direction);
                }
            }
        }, function ($query) {
            $query->latest();
        });


        $limit = (int) $request->query('limit', 10);
        $result = $query->paginate($limit)->withQueryString();

        return $result;
    }
}
