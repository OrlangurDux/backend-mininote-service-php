<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\FileBag;

trait DecodeContentTrait {
    public function decodeContent(string $rawData): object{
        $files = array();
        $data = array();
        $obj = new \stdClass();
        // Fetch content and determine boundary
        $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));
        // Fetch and process each part
        $parts = array_slice(explode($boundary, $rawData), 1);
        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") {
                break;
            }
            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($rawHeaders, $content) = explode("\r\n\r\n", $part, 2);
            $content = substr($content, 0, strlen($content) - 2);
            // Parse the headers list
            $rawHeaders = explode("\r\n", $rawHeaders);
            $headers = array();
            foreach ($rawHeaders as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }
            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                preg_match('/^form-data; *name="([^"]+)"(; *filename="([^"]+)")?/', $headers['content-disposition'], $matches);
                $fieldName = $matches[1];
                $fileName = ($matches[3] ?? null);
                // If we have a file, save it. Otherwise, save the data.
                if ($fileName !== null) {
                    $localFileName = tempnam(sys_get_temp_dir(), 'sfy');
                    file_put_contents($localFileName, $content);

                    $arr = array(
                        'name' => $fileName,
                        'type' => $headers['content-type'],
                        'tmp_name' => $localFileName,
                        'error' => 0,
                        'size' => filesize($localFileName)
                    );

                    if(str_ends_with($fieldName, '[]')) {
                        $fieldName = substr($fieldName, 0, strlen($fieldName)-2);
                    }

                    if(array_key_exists($fieldName, $files)) {
                        $files[$fieldName][] = $arr;
                    } else {
                        $files[$fieldName] = array($arr);
                    }

                    // register a shutdown function to cleanup the temporary file
                    register_shutdown_function(function() use($localFileName) {
                        unlink($localFileName);
                    });
                } else {
                    parse_str($fieldName.'=__INPUT__', $parsedInput);
                    $dottedInput = Arr::dot($parsedInput);
                    $targetInput = Arr::add([], array_keys($dottedInput)[0], $content);

                    $data = array_merge_recursive($data, $targetInput);
                }
            }
        }
        $obj->attributes = new ParameterBag($data);
        $obj->files = new FileBag($files);
        return $obj;
    }
}
