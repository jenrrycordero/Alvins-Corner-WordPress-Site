<?php

class PopcardIntegrationHandler
{
    private $dateformat = 'Y-m-d\TH:i:s';

    private $vendor_id = '9fc54bcc-201a-4b69-b3fc-5ff76f03c82b';

    private $source = 'GTMA Property Website';

    private $post;

    private $data;

    function __construct($post)
    {
        $this->post = $post;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getFieldAssociations()
    {
        $post = $this->getPost();
        $meta = get_post_meta($post->ID, 'popcard_fields', true);

        $result = [];

        foreach($meta as $key => $value)
        {
            $segments = explode('_', $key, 3);
            if(!$value || count($segments) !== 3 || $segments[0] !== 'pi') continue;
            $result[$segments[2]] = [
                'form_field'    => $value,
                'type'          => $segments[1],
            ];
        }

        return $result;
    }

    public function getFieldData()
    {
        $associations = $this->getFieldAssociations();
        $data = $this->getData();

        $debug = [$associations, $data];

        $result = [
            'prospect'      => [],
            'preferences'   => [],
        ];

        foreach($associations as $key => $association)
        {
            if(!array_key_exists($association['type'], $result) || !array_key_exists($association['form_field'], $data)) continue;

            $result[$association['type']][$key] = $data[$association['form_field']];
        }

        return $result;
    }


    public function getXML()
    {
        $post = $this->getPost();
        $property = get_post_meta($post->ID, 'popcard_property', true);
        $field_data = $this->getFieldData();

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true); //false for production
        $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('traffic'); //Required
                $xml->writeAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
                $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $xml->writeAttribute('transactiondatetime', date($this->dateformat));
                $xml->writeAttribute('contactdatetime', date($this->dateformat));
                $xml->startElement('trafficsource'); //Required
                    $xml->writeElement('vendorid', $this->vendor_id); //static
                    $xml->writeElement('sourcename', $this->source);
                    $xml->writeElement('propertyname', $property['name']);
                    $xml->writeElement('propertyid', $property['id']);
                $xml->endElement();
                $xml->startElement('prospect');
                    //$xml->writeAttribute('id', $pid);

                    foreach($field_data['prospect'] as $key=>$value)
                    {
                        if(!$value || empty($value)) continue;
                        $xml->writeElement($key, $value);
                    }
                $xml->endElement();
                $xml->startElement('prospectpreferences');
                    foreach($field_data['preferences'] as $key=>$value)
                    {
                        if(!$value || empty($value)) continue;
                        $xml->writeElement($key, $value);
                    }
                $xml->endElement();
            $xml->endElement();
        $xml->endDocument();
        return $xml->outputMemory(true);
    }

    public function sendToPopcard()
    {
        $xml = $this->getXML();

        $context = stream_context_create(array(
            'http'	=> array(
                'header'	=> "Content-type: application/x-www-form-urlencoded\r\n",
                'method'	=> 'POST',
                'content'	=> http_build_query(array(
                    'strRequestXML'	=> $xml
                ))
            )
        ));
        $url = 'http://interface.webservices.popcard.ltsolutions.com/service.asmx/InsertTraffic';
        $result = file_get_contents($url, false, $context);
        $xml_result = @simplexml_load_string($result);

        return $xml_result;
    }
}