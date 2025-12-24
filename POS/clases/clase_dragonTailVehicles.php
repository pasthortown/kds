<?php

class DragonTailVehicles  {
    private $employeeID;
    private $lastName;
    private $firstName;
    private $displayName;
    private $active;
    private $storeNo;
    private $role;
    private $date;
    private $clockIn;
    private $clockOut;
    private $mobilePhone;

    function __construct($data) {
        $this->employeeID  = $data['employeeID'];
        $this->lastName    = $data['lastName'];
        $this->firstName   = $data['firstName'];
        $this->displayName = $data['displayName'];
        $this->active      = $data['active'];
        $this->storeNo     = $data['storeNo'];
        $this->role        = $data['role'];
        $this->date        = $data['date'];
        $this->clockIn     = $data['clockIn'];
        $this->clockOut    = $data['clockOut'];
        $this->mobilePhone = $data['mobilePhone'];
    }

    public static function from($data, $motoroloId, $restaurantId, $accion) {
        $data=[
            'employeeID'  => $data['documento'],
            'lastName'    => $data['apellidos'],
            'firstName'   => $data['nombres'],
            'displayName' => self::getDisplayName($data['nombres'], $data['apellidos']),
            'active'      => (int)$accion,
            'storeNo'     => self::getstoreNo($restaurantId),
            'role'        => 1,
            'date'        => date("Y-m-d"),
            'clockIn'     => $data['clockIn'],
            'clockOut'    => $data['clockOut'],
            'mobilePhone' => $data['phone'],
        ];
        $new = new DragonTailVehicles($data);
        return $new;
    }

    private static function getDisplayName($firstName, $lastName) {
        return $firstName . " " . $lastName;
    }

    static function getClockIn($motoroloID) {
        $ClockInDate = DragonTailConfig::getClockIn($motoroloID);
        if ($ClockInDate != null) {
            return $ClockInDate->format('H:i:s');
        }
    }

    static function getClockOut($motoroloID) {
        $ClockOutDate = DragonTailConfig::getClockOut($motoroloID);
        if ($ClockOutDate != null) {
            return $ClockOutDate->format('H:i:s');
        }
    }

    static function getstoreNo($restaurantId) {
        return (int)$restaurantId;
    }

    function getPayload() {
        return get_object_vars($this);
    }
}
