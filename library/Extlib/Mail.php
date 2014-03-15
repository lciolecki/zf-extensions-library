<?php

namespace Extlib;

/**
 * Mailer class - change image for CID data.
 * 
 * @category    Extlib
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Lukasz Ciolecki (mart)
 */
class Mail extends \Zend_Mail
{

    /**
     * Sets the HTML body for the message
     *
     * @param  string    $html
     * @param  string    $charset
     * @param  string    $encoding
     * @return \Zend_Mail 
     */
    public function setBodyHtml($html, $charset = null, $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->setType(\Zend_Mime::MULTIPART_RELATED);

        $dom = new \DOMDocument(null, $this->getCharset());
        @$dom->loadHTML($html);

        $images = $dom->getElementsByTagName('img');
        for ($i = 0; $i < $images->length; $i++) {
            $img = $images->item($i);
            $url = $img->getAttribute('src');

            try {
                $client = new \Zend_Http_Client($url);
                $response = $client->request();

                if ($response->getStatus() === 200) {
                    $imageContent = $response->getBody();

                    $pathinfo = pathinfo($url);
                    $mimeType = $response->getHeader('Content-Type');

                    $cid = md5($pathinfo['filename']);
                    $html = str_replace($url, 'cid:' . $cid, $html);

                    $mime = new \Zend_Mime_Part($imageContent);
                    $mime->id = $cid;
                    $mime->type = $mimeType;
                    $mime->disposition = \Zend_Mime::DISPOSITION_INLINE;
                    $mime->encoding = \Zend_Mime::ENCODING_BASE64;
                    $mime->filename = $pathinfo['basename'];

                    $this->addAttachment($mime);
                }
            } catch (\Exception $exc) {
                //Error on client connect - bad url?
            }
        }

        return parent::setBodyHtml($html, $charset, $encoding);
    }

}
