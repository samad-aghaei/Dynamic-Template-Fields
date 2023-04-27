<?php

$regex = '~
(?:/(?P<api>api)+(?=\/.*\?|$|\/|\/\?))?
(?:/(?P<version>v[0-9]+)+(?=\/.*\?|$|\/|\/\?))?
(?:/(?P<controller>[\p{L}\p{N}\p{Mn}\p{Pd}_]+)+(?=\/.*\?|$|\/|\/\?))?
(?:/(?P<action>[\p{L}\p{N}\p{Mn}\p{Pd}_]+)+(?=\/.*\?|$|\/|\/\?))?
(?:(?P<params>(?:\/[\p{L}\p{N}\p{Mn}\p{Pd}_]+)+)+(?=\/.*\?|$|\/|\/\?))?
(?:/(?P<filename>[\p{L}\p{N}\p{Mn}\p{Pd}_]+)\.(?P<type>[a-z0-9]{2,5}))?
(?:/(?P<query>[\p{L}\p{N}\p{Mn}\p{Pd}_]+)(?=\?))?
(?:(?:\?|\/\?)?(?P<query_string>[\p{L}\p{N}\p{Mn}\p{Pd}_\=\&]+(?=$)))?
~ux';

preg_match( $regex, urldecode( rtrim( $_SERVER['REQUEST_URI'], '/' ) ), $matches );
