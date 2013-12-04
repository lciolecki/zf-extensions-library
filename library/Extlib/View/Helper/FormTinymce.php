<?php
/**
 * Extlib_View_Helper_FormTinyMCE - tinyMce view helper class
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (Mart)
 */
class Extlib_View_Helper_FormTinymce extends Zend_View_Helper_FormTextarea 
{
    const FOCUS_CLASS = 'focus';
    
    /**
     * $_tinyMcePath - path to tinyMce java script library
     * 
     * @var string 
     */
    static public $_tinyMcePath = '/js/tiny_mce/tiny_mce.js';
    	
    /**
     * $_locale - instance of Zend_Locale
     * 
     * @var Zend_Locale 
     */
    protected $_locale = null;
    
    /**
     * __construct() - instance of construct
     */
    public function __construct()
    {
        $this->_locale = new Zend_Locale();
    }

    public function formTinymce($name, $value = null, $attribs = null)
    {
        $this->view->headScript()->prependFile(self::$_tinyMcePath);

        $xhtml = '
            <script type="text/javascript">
                tinyMCE.init({

                    // General options
                    mode : "exact",
                    theme : "advanced",
                    language: "'. $this->_locale->getLanguage() .'", 
                    culture:"'. $this->_locale->getLanguage() .'",
                    elements: "'. $name .'",

                    setup: function(ed) {
                        ed.onClick.add(function(ed, evt) {
                            $("span#'. $name .'_parent").addClass("'. self::FOCUS_CLASS .'");
                        });
                    },

                    init_instance_callback: function(instance) {

                        var inEditor = false;;

                        $("tr.mceFirst").click(function() {
                            $("span#'. $name .'_parent").addClass("'. self::FOCUS_CLASS .'");
                            inEditor = true;
                        });

                        $("tr.mceLast").click(function() {
                            $("span#'. $name .'_parent").addClass("'. self::FOCUS_CLASS .'");
                            inEditor = true;
                        });

                        $("body").click(function() {
                            if (!inEditor) {
                                $("span#'. $name .'_parent").removeClass("'. self::FOCUS_CLASS .'");
                            } else {
                                inEditor = false;
                            }
                        });		
                    },

                    plugins: "safari,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                    theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,|,forecolor,backcolor,styleprops,|,fullscreen",
                    theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,charmap,emotions,media,advhr,|,print,|,sub,sup",
                    theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage,insertdate,inserttime,preview,|,anchor,image,cleanup,code,spellchecker",

                    theme_advanced_toolbar_location: "top",
                    theme_advanced_toolbar_align: "left",
                    theme_advanced_statusbar_location: "bottom",
                    theme_advanced_resizing: false,
                    extended_valid_elements: "hr[id|title|alt|class|width|size|noshade],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],a[class|name|href|target|title|onclick|rel]",
                    relative_urls: false,
                    remove_script_host: false,
                    debug: false,
                    entity_encoding: "raw",
                    file_browser_callback: "openKCFinder"
                });
                
                function openKCFinder(field_name, url, type, win) {
                    tinyMCE.activeEditor.windowManager.open({
                    file: "'. Extlib_View_Helper_Kcfinder::$_kcfinderPath . '?opener=tinymce&lang='. $this->_locale->getLanguage() . '&type=' . '" + type,
                    title: "' . $this->view->translate('Zarządzanie plikami') .' " + type,
                    width: 780,
                    height: 500,
                    resizable: "yes",
                    inline: true,
                    close_previous: "yes",
                    popup_css: true
                }, {
                    window: win,
                    input: field_name
                });

                return false;
            }
            </script>';

        $xhtml .= $this->formTextarea($name, $value, $attribs);

        return $xhtml;
    }							
}