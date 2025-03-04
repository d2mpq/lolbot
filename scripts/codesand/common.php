<?php

namespace scripts\codesand;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use knivey\cmdr\attributes\CallWrap;
use knivey\cmdr\attributes\Cmd;
use knivey\cmdr\attributes\Syntax;
use Symfony\Component\Yaml\Yaml;

function getRun($ep, $code) {
    $csConfig = Yaml::parseFile(__DIR__.'/config.yaml');

    try {
        $client = HttpClientBuilder::buildDefault();
        /** @var Response $response */
        $request = new Request("{$csConfig['server']}$ep", 'POST');
        $request->setInactivityTimeout(15000);
        $request->setHeader("key", $csConfig["key"]);
        $request->setBody($code); // html chars and such seem to not be a problem
        $response = yield $client->request($request);
        $body = yield $response->getBody()->buffer();
        if ($response->getStatus() != 200) {
            var_dump($body);
            // Just in case its huge or some garbage
            $body = str_replace(["\n", "\r"], '', substr($body, 0, 200));
            return ["Error (" . $response->getStatus() . ") $body"];
        }
    } catch (\Exception $error) {
        echo $error;
        return ["\2Server Error:\2 " . substr($error, 0, strpos($error, "\n"))];
    }

    $output = json_decode($body, true);
    if(!is_array($output)) {
        echo "codesand $ep returned non array:\n";
        var_dump($output);
        return ["something went badly"];
    }
    return $output;
}

#[Cmd("php")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runPHP($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/php?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("bash")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runBash($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/bash?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("py3", "py", "python", "python3")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runPy3($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/python3?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("py2", "python2")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runPy2($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/python2?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("perl")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runPerl($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/perl?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("java")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runJava($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/java?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("fish")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runFish($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/fish?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("c", "tcc")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runTcc($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/tcc?maxlines=$maxlines&flags=-Wno-implicit-function-declaration", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("gcc")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runGcc($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $code = "#include <assert.h>
#include <complex.h>
#include <ctype.h>
#include <errno.h>
#include <fenv.h>
#include <float.h>
#include <inttypes.h>
#include <iso646.h>
#include <limits.h>
#include <locale.h>
#include <math.h>
#include <setjmp.h>
#include <signal.h>
#include <stdalign.h>
#include <stdarg.h>
#include <stdatomic.h>
#include <stdbool.h>
#include <stddef.h>
#include <stdint.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdnoreturn.h>
#include <string.h>
#include <tgmath.h>
#include <threads.h>
#include <time.h>
#include <uchar.h>
#include <wchar.h>
#include <wctype.h>
{$req->args['code']}";
    $output = yield from getRun("/run/gcc?maxlines=$maxlines&flags=-Wno-implicit-function-declaration&flagsb=-lm", $code);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("tcl")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runTcl($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $maxlines = $config['codesand_maxlines'] ?? 10;
    $output = yield from getRun("/run/tcl?maxlines=$maxlines", $req->args['code']);
    sendOut($bot, $args->chan, $output);
}

#[Cmd("cpp", "g++")]
#[Syntax("<code>...")]
#[CallWrap("\Amp\asyncCall")]
function runGpp($args, \Irc\Client $bot, \knivey\cmdr\Request $req) {
    global $config;
    if(!($config['codesand'] ?? false)) {
        return;
    }
    $code = "#include <bits/stdc++.h>
    #define FMT_HEADER_ONLY
    #include <fmt/core.h>
    #include <fmt/format.h>
    #include <fmt/format-inl.h>
    
using namespace std;
{$req->args['code']}";
    $maxlines = $config['codesand_maxlines'] ?? 10;
    //$gccArgs = urlencode("-Wfatal-errors -std=c++17");
    $gccArgs = urlencode("-Wfatal-errors");
    $output = yield from getRun("/run/gpp?maxlines=$maxlines&flags=$gccArgs", $code);
    sendOut($bot, $args->chan, $output);
}

function sendOut($bot, $chan, $data) {
    foreach ($data as $line) {
        if(substr($line, 0, strlen("OUT: ")) == "OUT: ")
            $line = substr($line, strlen("OUT: "));
        if(substr($line, 0, strlen("ERR: ")) == "ERR: ")
            $line = "\x0304". substr($line, strlen("OUT: "));
        $bot->pm($chan, $line);
    }
}
