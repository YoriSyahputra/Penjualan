<?php

namespace App\Constants;

class DeliveryStatus
{
    // Status untuk DeliveryHistory
    const PICKED_UP = 'picked_up';
    const SEDANG_DIANTAR = 'sedang_diantar';
    const MENUJU_ALAMAT = 'menuju_alamat';
    const TIBA_DI_TUJUAN = 'tiba_di_tujuan';
    const GAGAL = 'gagal';
    
    // Status untuk Order
    const ORDER_PENDING = 'pending';
    const ORDER_PROCESSING = 'processing';
    const ORDER_ON_DELIVERY = 'on_delivery';
    const ORDER_ARRIVED = 'arrived'; // Status baru
    const ORDER_DELIVERED = 'delivered';
    const ORDER_FAILED = 'delivery_failed';
    
    // Mapping DeliveryHistory status ke Order status
    public static function mapToOrderStatus($deliveryStatus)
    {
        return [
            self::PICKED_UP => self::ORDER_ON_DELIVERY,
            self::SEDANG_DIANTAR => self::ORDER_ON_DELIVERY,
            self::MENUJU_ALAMAT => self::ORDER_ON_DELIVERY,
            self::TIBA_DI_TUJUAN => self::ORDER_ARRIVED,
            self::GAGAL => self::ORDER_FAILED
        ][$deliveryStatus] ?? self::ORDER_ON_DELIVERY;
    }
}