<?php

namespace App\Services;

use App\Models\Consignment;
use Exception;

class ConsignmentService
{
    public function getAllConsignments($filters)
    {
        $query = Consignment::query();

        // Apply filters if available
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['shipment_status'])) {
            $query->where('shipment_status', $filters['shipment_status']);
        }

        if (!empty($filters['received_date'])) {
            $query->whereDate('received_date', $filters['received_date']);
        }

        if (!empty($filters['sort_by']) && !empty($filters['sort_order'])) {
            $sortOrder = $filters['sort_order'] === 'desc' ? 'desc' : 'asc';
            $sortBy = $filters['sort_by'];

            if ($sortBy === 'received_date') {
                $query->orderBy('received_date', $sortOrder);
            } elseif ($sortBy === 'size') {
                $query->orderByRaw("CAST(REGEXP_REPLACE(size, '[^0-9]', '') AS UNSIGNED) $sortOrder");
            } elseif ($sortBy === 'name') {
                $query->orderBy('consignment_id', $sortOrder);
            }
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where('consignment_id', 'LIKE', "%{$search}%")
                    ->orWhere('type', 'LIKE', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function getConsignmentById($id)
    {
        return Consignment::findOrFail($id);
    }

    public function createConsignment($data)
    {
        $consignment = new Consignment();
        $consignment->fill($data);
        $consignment->save();

        return $consignment;
    }

    public function updateConsignment($consignment, $data)
    {
        $consignment->fill($data);
        $consignment->save();

        return $consignment;
    }

    public function deleteConsignment($consignment)
    {
        $consignment->delete();
    }
}
