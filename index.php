<?php
  /**
   * making a chunk'ed response.
   *
   * 1. disable gzip compression (both PHP script and .htaccess).
   * 2. no buffering (PHP engine will not delay the PHP-script response untill the script will end), killing initial PHP buffer.
   * 3. PHP-engine language-initializing to UTF-8.
   * 3. pre-send content-headers.
   *
   * @author Elad Karako (icompile.eladkarako.com)
   * @link   http://icompile.eladkarako.com
   */
  error_reporting(E_STRICT);

  //settings - no gzip
//  @apache_setenv('no-gzip', 1);         //don't use it, it crashes nginx :) use the added to .htaccess   ::->>   SetEnvIf Request_URI .* no-gzip=1
  @ini_set("zlib.output_compression", 0);

  //settings - no buffering
  @ini_set('output_buffering', 0);
  @ini_set('implicit_flush', 0);
  @ob_implicit_flush(0);
  while (ob_get_level() > 0) @ob_end_clean(); //disable Apache's and nginx's (pre)initial-buffer.

  //language-engine configuration
  mb_language("uni");
  mb_internal_encoding('UTF-8');
  mb_http_input('UTF-8');
  mb_http_output('UTF-8');
  mb_regex_encoding('UTF-8');
  setlocale(LC_ALL, 'en_US.UTF-8');

  //language-content response-headers
  header('Charset: UTF-8', true);
  header('Content-Encoding: UTF-8', true);
  header('Content-Type: text/plain; charset=UTF-8', true);

  //bonus
  usleep(10 * 1000); //10 milliseconds
  date_default_timezone_set("Asia/Jerusalem");
  header('Access-Control-Allow-Origin: *', true, 200);

  //-------------------------------------------------------------------------------

  require_once('./fn.php');

  $output = str_repeat('a', 1024 * 4); //mock output


  $chunks = get_chunks($output);
  send_chunks($chunks);
?>
