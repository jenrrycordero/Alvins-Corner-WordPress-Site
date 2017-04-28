<?php namespace Razz\Helpers;

class GravityFormsHelper
{

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    private function __construct()
    {

    }

    public function getForms()
    {
        $forms = \GFAPI::get_forms();

        $forms = array_filter($forms, function($form)
        {
            return $this->formHasEvent($form);
        });

        $forms = array_values($forms); //re-key

        return $forms;
    }

    public function formHasEvent($form)
    {
        return ($this->getFormEventField($form) !== false);
    }

    public function getFormEventField($form)
    {
        $form = $this->getFormFromInput($form);
        if(!$form) return false;

        foreach ($form['fields'] as $field)
        {
            if($field['type'] === 'rsvp_events')
            {
                return $field;
            }
        }

        return false;
    }

    public function findCampaignCode($campaign_id, $code_input)
    {
        $codes = get_post_meta($campaign_id, 'codes', true);

        foreach($codes as $code)
        {
            if($code['code'] != $code_input) continue;

            return $code;
        }

        return false;
    }

    public function getFormFieldSlugMap($form)
    {
        $form = $this->getFormFromInput($form);
        if(!$form) return false;

        $fields = [];
        foreach ($form['fields'] as $field)
        {
            if(!$field['adminLabel']) continue;
            $fields[$field['id']] = sanitize_title($this->getFieldAdminLabel($field));
        }

        return $fields;
    }

    public function getEntryEventID($entry)
    {
        $event_field = $this->getFormEventField($entry['form_id']);
        if(!$event_field) return false;

        return $entry[$event_field['id']];
    }

    public function getFormByID($form_id)
    {
        $form = \GFAPI::get_form($form_id);

        return ($form && $this->formHasEvent($form)) ? $form : false;
    }

    public function getFieldByID($form, $field_id)
    {
        $form = $this->getFormFromInput($form);
        if(!$form) return false;

        foreach ($form['fields'] as $field)
        {
            if($field['id'] === $field_id)
            {
                return $field;
            }
        }

        return false;
    }

    public function getFormCampaign($form)
    {
        $form = $this->getFormFromInput($form);
        if(!$form) return false;

        $events_field = $this->getFormEventField($form);

        return $events_field ? $events_field['field_campaign'] : false;
    }

    public function getCampaignForms($campaign_id)
    {
        $forms = $this->getForms();

        $forms = array_filter($forms, function($form) use($campaign_id)
        {
            return ($this->getFormCampaign($form['id']) == $campaign_id);
        });

        $forms = array_values($forms); //re-key

        return $forms;
    }

    public function getFieldAdminLabel($field)
    {
        return $field['adminLabel'] ? $field['adminLabel'] : $field['label'];
    }

    protected function getFormFromInput($form)
    {
        if(is_numeric($form))
        {
            return $this->getFormByID($form);
        }

        return $form;
    }
}