<?php

namespace App\Helpers;

class View
{
    protected static $layout = null;

    public static function make($view, $data = [])
    {
        extract($data);

        $viewPath = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }

        // View içeriğini al
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // Layout varsa işle
        if (static::$layout) {
            $layoutPath = VIEW_PATH . '/layouts/' . static::$layout . '.php';

            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout not found: " . static::$layout);
            }

            // Layout'u işle
            ob_start();
            include $layoutPath;
            $finalContent = ob_get_clean();
            static::$layout = null;

            // Son içeriği döndür
            echo $finalContent;
            return;
        }

        // Layout yoksa direkt içeriği döndür
        echo $content;
    }

    public static function setLayout($layout)
    {
        static::$layout = $layout;
    }
}