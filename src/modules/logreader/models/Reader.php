<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\logreader\models;

use zabachok\logreader\models\LogLine;
use zabachok\logreader\models\Reader as BaseReader;

class Reader extends BaseReader
{

    private $body = '';
    private $index = 0;

    public function __construct($path, $filename)
    {
        $this->path = $path;
        $this->filename = $filename;
        $this->mtime = filemtime($this->path . '/' . $this->filename);
    }

    public function open()
    {
        if ($this->opened) return true;
        if (!file_exists($this->path . '/' . $this->filename)) return false;
        $this->handle = fopen($this->path . '/' . $this->filename, 'r');
        $s = fseek($this->handle, $this->length, SEEK_END);
        $this->seek();
        $this->opened = true;
        return true;
    }

    public function getRow($search = null)
    {
        if (!$this->open()) return null;
        $this->body = '';
        $i = 0;
        while ($i < 1000) {
            $i++;
            $line = fgets($this->handle);

            $data = $this->parseTitle($line);
            if ($data === false) {
                $this->body = $line . $this->body;
            } else {
                if ($this->isIgnore($data)) {
                    $this->body = $line . $this->body;
                    continue;
                }
                if ($search != null) {
                    if (stripos($data['text'], $search) !== false) {
                        //Found search
                        $this->body = $data['text'] . "\n" . $this->body;
                        $model = new LogLine();
                        $model->attributes = array_merge($data, [
                            'index' => $this->index++,
                            'text' => $this->body,
                            'firstLine' => $data['text'],
                        ]);
                        return $model;
                    }
                } else {
                    $this->body = $data['text'] . "\n" . $this->body;
                    $model = new LogLine();
                    $model->attributes = array_merge($data, [
                        'index' => $this->index++,
                        'text' => $this->body,
                        'firstLine' => $data['text'],
                    ]);
                    return $model;
                }
            }

            $this->seek();
        }

    }

}