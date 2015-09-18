<?php

function autoload($html)
{
    try {
        require_once dirname(__FILE__).'/lib/autoload.php';
    } catch (\Exception $e) {
        $html.= "<h2>Error:</h2>\n";
        $html.= "<textarea cols=\"100\" rows=\"5\" class=\"form-control\">\n";
        $html.= $e->getMessage()."\n";
        $html.= "</textarea>\n";
    }

    return $html;
}

function output($document, $time, $html)
{
    if ($document) {
        $html.= "<h1>".$document->getFormatter()->getTitle()."</h1>\n";

        // show some information
        $html.= "<h2>Information:</h2>\n";
        $html.= "<ul>\n";
        $html.= "<li>Filename: ".basename($document->getWriter()->getStorage()->getResult())."</li>\n";
        $html.= "<li>Memory usage:".(memory_get_peak_usage(true) / 1024 / 1024)." MB</li>\n";
        $html.= "<li>Time:".$time." second(s)</li>\n";
        $html.= "</ul>\n";

        // show a simple text box with the output
        $html.= "<h2>Result:</h2>\n";
        $html.= "<textarea cols=\"100\" rows=\"50\"  class=\"form-control\">\n";
        $html.= $document->getWriter()->getStorage()->getLogs()."\n";
        $html.= "</textarea>\n";
    } else {
        $html.= "<p>Export not performed, please review your code.</p>\n";
    }

    return $html;
}

function export($target, $setup = array(), $filename, $outDir)
{
    $html = "";
    try {
        // lets stop the time
        $start    = microtime(true);
        $logFile  = $outDir.'/log.txt';

        // créé dossier si existe pas
        if (!file_exists($outDir)) {
            try{
                $oldmask = umask(0);
                mkdir("$outDir", 0755);
                umask($oldmask);  
            }catch (\Exception $e) {
                $html.= "<h2>Error:</h2>\n";
                $html.= "<textarea cols=\"100\" rows=\"5\">\n";
                $html.= $e->getMessage()."\n";
                $html.= "</textarea>\n";
                return $html;
            }         
        }

        $bootstrap = new \MwbExporter\Bootstrap();
        $formatter = $bootstrap->getFormatter($target);
        $formatter->setup(array_merge(array(\MwbExporter\Formatter\Formatter::CFG_LOG_FILE => $logFile), $setup));
        $document  = $bootstrap->export($formatter, $filename, $outDir, 'file');

        // show the time needed to parse the mwb file
        $end = microtime(true);

        $html = output($document, $end - $start, $html);

        return $html;
    } catch (\Exception $e) {
        $html.= "<h2>Error:</h2>\n";
        $html.= "<textarea cols=\"100\" rows=\"5\">\n";
        $html.= $e->getMessage()."\n";
        $html.= "</textarea>\n";
        return $html;
    }
}
