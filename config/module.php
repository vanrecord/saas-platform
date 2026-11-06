<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Permission Module Type
    |--------------------------------------------------------------------------
    |
    | This configuration set permission of the HR && Agency 
    | 
    |
    */

    // 'patient' => [
    //     'patient.index'     => 'Patient',
    // ],
    // 'employee' => [
    //     'employee.index'    => 'Employee',
    // ],
    // 'appointment' => [
    //     'appointment.index' => 'Appointment',
    // ],

    'hr' => [
        'employee.index'    => 'Employee',
        'shift.index'       => 'Shift',
    ],
    'agency' => [
        'requester.index'   => 'Requester',
        'appointment.index' => 'Appointment',
    ]
];