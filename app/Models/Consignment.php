<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Consignment extends Model
{
    private static $consignment;

    private static function saveBasicInfo($consignment, $request)
    {
        $consignment->consignment_id            = $request->consignment_id;
        $consignment->size                      = $request->size;
        $consignment->type                      = $request->type;
        $consignment->received_date             = $request->received_date;
        $consignment->release_date              = $request->release_date;
        $consignment->shipment_status           = $request->shipment_status;
        $consignment->save();
    }

    public static function newConsignment($request)
    {
        self::$consignment = new Consignment();
        self::saveBasicInfo(self::$consignment, $request);
    }


    public static function updateConsignment($request, $consignment)
    {

        self::saveBasicInfo($consignment, $request);
    }

    public static function deleteConsignment($consignment)
    {
        $consignment->delete();
    }

}
