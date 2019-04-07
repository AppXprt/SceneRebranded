<?php

require_once  SC_DIR_LIB . 'oembed' . DS. 'oembed.php';

class UTIL_HttpResource
{

    /**
     *
     * @param string $url
     * @return SC_HttpResource
     */
    public static function getContents( $url, $timeout = 20 )
    {
        $context = stream_context_create( array(
            'http'=>array(
                'timeout' => $timeout,
                'header' => "User-Agent: Scene Content Fetcher\r\n"
            )
        ));

        return file_get_contents($url, false, $context);
    }

    /**
     *
     * @param string $url
     * @return array
     */
    public static function getOEmbed( $url )
    {
        return OEmbed::parse($url);
    }
}