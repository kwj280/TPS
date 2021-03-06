<?php

/*
 * The MIT License
 *
 * Copyright 2016 J.oliver.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

$app->group("/environment", array($authenticate($app,[1,2])),
        function() use ($app,$authenticate){

    $app->get("/station", $authenticate($app, [1,2]), function () use ($app){
        standardResult::ok($app, $_SESSION['CALLSIGN'], "callsign");
    });
    $app->put("/station", $authenticate($app, [1,2]),
            function () use ($app){
        $callsign = $app->request->put("callsign");
        $tps = new \TPS\TPS();
        $stations = $tps->getStations();
        if(!$callsign){
            $result = standardResult::badRequest($app,"Callsign was not provided", null, 400, true);
            $app->halt(400, $result);
        }
        if(!array_key_exists($callsign, $stations)){
            $x = standardResult::badRequest($app, "Callsign is not valid");
            $app->halt(400, $x);
        }
        $_SESSION["CALLSIGN"] = $callsign;
        standardResult::accepted($app, $callsign, "callsign");
    });
});
