<?php

namespace App\Http\Helpers;

use App\Http\Helpers\CssMinify as CssMinify;

class CssMinify {

    // Function which actually compress
    // The CSS file
    public static function compress_css($buffer) {
        /* remove comments */
        $buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);
        /* remove tabs, spaces, newlines, etc. */
        $arr = array("\r\n", "\r", "\n", "\t", "  ", "    ", "    ");
        $buffer = str_replace($arr, "", $buffer);
        return $buffer;
    }

    /**
     * Builds CSS cache file
     * @param array $cssFiles
     * @return string
     */
    public static function buildCache(array $cssFiles) {
        $cssString = "";
        foreach ($cssFiles as $cssFile) {
            $cssString .= file_get_contents($cssFile);
        }
        $cssString = CssMinify::compress_css($cssString);
        return $cssString;
    }

    /**
     * Writes the cache into file
     * @param string $cacheFilename
     * @param string $cssCompressed
     */
    public static function writeCache($cacheFilename, $cssCompressed) {
        try {
            $f = fopen($cacheFilename, "w");
            // lock file to prevent problems under high load
            flock($f, LOCK_EX);
            fwrite($f, $cssCompressed);
            fclose($f);            
        } catch (Exception $e) {
            \Log::info("Permission error occured. Error " . $e->getMessage());
        }
    }

    /**
     * Compare the modification time of cache file against the CSS files
     * @param type $cacheFilename
     * @param array $cssFiles
     * @return bool
     */
    public static function checkCacheIsOk($cacheFilename, array $cssFiles) {
        if (file_exists($cacheFilename)) {
            $lastCssModificatedAt = 0;
            foreach ($cssFiles as $cssFile) {
                $cssModificatedAt = filemtime($cssFile);
                if ($cssModificatedAt > $lastCssModificatedAt) {
                    $lastCssModificatedAt = $cssModificatedAt;
                }
            }

            if (filemtime($cacheFilename) >= $lastCssModificatedAt) {
                return true;
            }
        }
        return false;
    }

    public static function minifyCss() {
        $cssFiles = array('css/select2.min.css', 'css/bootstrap.min.css', 'css/font-awesome.css','css/font-awesome-animation.css', 'css/dataTables.bootstrap.css', 'js/jslider/css/jslider.round.plastic.css', 'css/AdminLTE.css', 'css/_all-skins.min.css', 'css/smart_wizard.css', 'css/datepicker3.css', 'css/bootstrap-switch.css');

        //$cssFiles = array('css/select2.min.css','css/bootstrap.min.css','css/dataTables.bootstrap.css','plugins/iCheck/all.css','js/jslider/css/jslider.round.plastic.css','css/AdminLTE.css','css/_all-skins.min.css');
        //$cssFiles = array('css/select2.min.css','css/bootstrap.min.css','css/ionicons.css','css/dataTables.bootstrap.css','plugins/iCheck/all.css','js/jslider/css/jslider.round.plastic.css','css/AdminLTE.css','css/_all-skins.min.css');
        // This can be changed whatever you want
        $cacheFilename = "css/" . md5('css_cache') . ".css";
        $cssCompressed = "";

        if (!CssMinify::checkCacheIsOk($cacheFilename, $cssFiles)) {
            $cssCompressed = CssMinify::buildCache($cssFiles);
            CssMinify::writeCache($cacheFilename, $cssCompressed);
        }
    }

    public static function minifyJs($jstype = 'common_js') {
        /* if($is == 1)
          $cssFiles = array('js/plugins/jQuery-2.1.4.min.js','js/bootstrap/bootstrap.min.js','js/datatables/jquery.dataTables.min.js','js/datatables/dataTables.search-highlight.js','js/datatables/dataTables.bootstrap.min.js','js/plugins/jquery.slimscroll.min.js','js/plugins/fastclick.min.js','js/selectbox/select2.full.min.js','plugins/iCheck/icheck.min.js','js/bootstrapValidator.js','plugins/input-mask/jquery.inputmask.js');
          elseif($is == 2)
          $cssFiles = array('js/animated-icons/livicons-1.4.js','js/animated-icons/json2.js','js/animated-icons/raphael.js','js/bootstrap-fileupload.js','js/jquery_print.js','js/jquery_datepicker.js','js/jquery-ui.js');
          elseif($is == 3)
          $cssFiles = array('js/jslider/js/jquery.dependClass-0.1.js','js/jslider/js/draggable-0.1.js','js/jslider/js/jquery.slider.js');
         */
        if ($jstype == 'common_js')
            $cssFiles = array('js/plugins/jQuery-2.1.4.min.js', 'js/bootstrap/bootstrap.min.js', 'js/plugins/jquery.slimscroll.min.js', 'js/plugins/fastclick.min.js', 'js/jquery_print.js', 'js/animated-icons/livicons-1.4.js', 'js/animated-icons/json2.js', 'js/animated-icons/raphael.js', 'js/jquery-ui.js', 'js/bootstrap-switch.js', 'js/jquery.mapkey.js', 'js/shortcutkey.js');
        elseif ($jstype == 'datatables_js')
            $cssFiles = array('js/datatables/jquery.dataTables.min.js', 'js/datatables/dataTables.search-highlight.js', 'js/datatables/dataTables.bootstrap.min.js');
        elseif ($jstype == 'form_js')
            $cssFiles = array('js/selectbox/select2.full.min.js', 'js/bootstrapValidator.js', 'plugins/iCheck/icheck.min.js', 'js/bootstrap-fileupload.js', 'plugins/input-mask/jquery.inputmask.js');
        elseif ($jstype == 'slider_js')
            $cssFiles = array('js/jslider/js/jquery.dependClass-0.1.js', 'js/jslider/js/draggable-0.1.js', 'js/jslider/js/jquery.slider.js');
        elseif ($jstype == 'app_js')
            $cssFiles = array('js/app.min.js');
        elseif ($jstype == 'function_js')
            $cssFiles = array('js/function.js');

        $cacheFilename = "js/" . md5($jstype) . ".js";
        $cssCompressed = "";

        if (!CssMinify::checkCacheIsOk($cacheFilename, $cssFiles)) {
            $cssCompressed = CssMinify::buildCache($cssFiles);
            CssMinify::writeCache($cacheFilename, $cssCompressed);
        }
    }

}
