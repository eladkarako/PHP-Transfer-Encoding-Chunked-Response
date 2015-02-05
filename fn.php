<?php

  /**
   * convert text, to chunks, with additional information (to be used in "send_chunks" function).
   *
   * @param string $output     - original text to be process.
   * @param int    $chunk_size - (default is 512 characters) set maximum amount of characters in single-chunk.
   *
   * @return array
   */
  function get_chunks($output = '', $chunk_size = 512) {
    $chunks = explode('|###|', chunk_split($output, $chunk_size, '|###|'));
    $chunks = array_map(function ($chunk) {
      return [
        //original text
        'content_text'                           => $chunk

        //size of original text (in characters)
        , 'content_char_count'                   => mb_strlen($chunk)
        , 'content_char_count_in_hexadecimal'    => dechex(mb_strlen($chunk))

        //size of original text (in bytes)
        , 'content_size_in_bytes'                => mb_strlen($chunk, '8bit')
        , 'content_size_in_bytes_in_hexadecimal' => dechex(mb_strlen($chunk, '8bit'))
      ];
    }, $chunks);

    return $chunks;
  }


  /**
   * sends out the header marking the response as chunked,
   * and start sending pre-ordered chunked, as given from get_chunks function.
   *
   * @param $chunks - associative array as given from get_chunks function
   *
   * @return bool - always returns true (to easier-concatenation of commands)
   */
  function send_chunks($chunks) {
    header("Transfer-Encoding: chunked", true);

    $size_of_original_content = 0;
    foreach ($chunks as $index => $chunk) $size_of_original_content += $chunk['content_size_in_bytes']; //just sum length without sending output yet.
    header("Content-Length: " . $size_of_original_content, true); //make sure

    foreach ($chunks as $index => $chunk) {
      $chunk = implode("\r\n", [
        $chunk['content_char_count_in_hexadecimal'],
        $chunk['content_text']
      ]);
      @ob_end_clean(); //make sure there is nothing but our output to be.

      echo $chunk;

      @ob_flush();
      @flush();
      usleep(10 * 1000); //10 milliseconds
    }

    return true;
  }


  //
  //  if (false === $prepare_output_as_chunked_transfer_encoding) return $output;
  //
  //  $sep = "\r\n";
  //  $output_chunked = '';
  //  foreach ($output as $index => $chunk) {
  //    $size = dechex(mb_strlen($chunk));
  //    $output_chunked .= $size . $sep . $chunk . $sep;
  //  }
  //
  //  return $output_chunked;
  //  }
  //
  //  $output = str_repeat('a', 512 * 4); //mock output
  //
  //  $output = get_chunks($output, 512);
  //
  //
  //  $chunks = get_chunks();
  //  $chunks = str_repeat('a', 512 * 4);
  //
  //
  //  //generate an orderly chunk-queue
  //  $chunks = call_user_func(function () { //512x4 = 2048
  //
  //    return array_merge($chunk, $chunk, $chunk, $chunk);
  //  });
  //
  //  //output the chunks
  //  foreach ($chunks as $chunk) {
  //    $size = dechex(mb_strlen($chunk));
  //    $sep = "\r\n";
  //
  //    echo $size . $sep . $chunk . $sep;
  //    @ob_flush();
  //    @flush();
  //    usleep(500 * 1000); //500 milliseconds
  //  }
  //
  //
  //  for ($i = 0; $i < ob_get_level(); $i++) ob_end_flush();
  //  ob_implicit_flush(1);
  //  flush();
  //
  //  function dump_chunk($chunk) {
  //    printf("%x\r\n%s\r\n", strlen($chunk), $chunk);
  //    flush();
  //  }
  //
  //  for (; ;) {
  //    $output = [];
  //    exec("/usr/games/fortune", $output);
  //    dump_chunk(implode("\n", $output));
  //    usleep(pow(2, 18));
  //  }
  //


  //  header("Transfer-Encoding: chunked", true);
  //
  //  function get_chunk($string) {
  //    return printf(
  //      "%x\r\n%s\r\n", mb_strlen($string), $string
  //    );
  //  }

  //
  //  function truffle_shuffle($body, $chunklen = 76, $end = "\r\n") {
  //    $chunk = chunk_split($body, $chunklen, "-=blender=-");
  //    $truffle = explode("-=blender=-", $chunk);
  //    $shuffle = shuffle($truffle);
  //    $huknc = implode($end, $shuffle);
  //
  //    return $huknc;
  //  }
  //

?>
