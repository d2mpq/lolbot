<?php
namespace scripts\bing;

use knivey\cmdr\attributes\CallWrap;
use knivey\cmdr\attributes\Cmd;
use knivey\cmdr\attributes\Options;
use knivey\cmdr\attributes\Syntax;

$ratelimit = 0;
$warned = false;

#[Cmd("bing")]
#[Syntax('<query>...')]
#[CallWrap("Amp\asyncCall")]
#[Options("--amt", "--result")]
function bing($args, \Irc\Client $bot, \knivey\cmdr\Request $req)
{
    global $config, $warned, $ratelimit;
    if(!isset($config['bingKey'])) {
        echo "bingKey not set in config\n";
        return;
    }
    if(!isset($config['bingEP'])) {
        echo "bingKbingEPey not set in config\n";
        return;
    }
    if(!isset($config['bingLang'])) {
        echo "bingLang not set in config\n";
        return;
    }

    if (time() < $ratelimit) {
        if(!$warned)
            $bot->pm($args->chan, "Whoa slow down!");
        $warned = true;
        return;
    }
    $warned = false;
    $ratelimit = time() + 2;

    $start = 1;
    $end = 1;
    if($req->args->getOpt("--amt")) {
        $end = $req->args->getOptVal("--amt");
        if($end < 1 || $end > 4) {
            $bot->pm($args->chan, "\2Bing:\2 --amt should be from 1 to 4");
            return;
        }
    }
    if($req->args->getOpt("--result")) {
        $start = $req->args->getOptVal("--result");
        if($start < 1 || $start > 11) { //default 10 returned
            $bot->pm($args->chan, "\2Bing:\2 --result should be from 1 to 10");
            return;
        }
        $end = $start;
    }
    $query = urlencode($req->args['query']);
    $url = $config['bingEP'] . "search?q=$query&mkt=$config[bingLang]&setLang=$config[bingLang]";
    try {
        $headers = ['Ocp-Apim-Subscription-Key' => $config['bingKey']];
        $body = yield async_get_contents($url, $headers);
        $j = json_decode($body, true);

        if (!array_key_exists('webPages', $j)) {
            $bot->pm($args->chan, "\2Bing:\2 No Results");
            return;
        }
        $results = number_format($j['webPages']['totalEstimatedMatches']);

        for ($i = ($start - 1); $i <= $end-1; $i++) {
            if(!isset($j['webPages']['value'][$i])) {
                $bot->pm($args->chan, "End of results :(");
                break;
            }
            $res = $j['webPages']['value'][$i];
            $bot->pm($args->chan, "\2Bing (\2$results Results\2):\2 $res[url] ($res[name]) -- $res[snippet]");
        }
    } catch (\async_get_exception $error) {
        echo $error;
        $bot->pm($args->chan, "\2Bing:\2 {$error->getIRCMsg()}");
    } catch (\Exception $error) {
        echo $error->getMessage();
        $bot->pm($args->chan, "\2Bing:\2 {$error->getMessage()}");
    }
}