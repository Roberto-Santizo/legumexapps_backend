<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('item.status', function () {
//     return true;
// });


Broadcast::channel('planification.change', function () {
    return true;
});
