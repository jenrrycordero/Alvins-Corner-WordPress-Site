<?php

class PopcardIntegrationPostMeta extends AdminPageFramework_MetaBox
{

	function __construct()
	{
		parent::__construct(null, 'Popcard Options', 'popcard_integration', 'normal', 'high');
	}

	public function setUp()
	{
        $forms = apply_filters('popcard_integration_form_list', []);

        $this->addSettingSections(
            [
                'section_id'        => 'popcard_property',
                'title'             => 'Property',
                'description'       => '',
            ],
            [
                'section_id'        => 'popcard_fields',
                'title'             => 'Field Association',
                'description'       => 'Enter a matching contact form field name for any fields you wish to include.',
            ]
        );

        $this->addSettingField([
            'title'     => 'Form',
            'field_id'  => 'form_id',
            'type'      => 'select',
            'label'    => $forms,
        ]);

        $this->addSettingFields(
            'popcard_property',
            [
                'title'     => 'Property Name',
                'field_id'  => 'name',
                'type'      => 'text',
            ],
            [
                'title'     => 'Property ID',
                'field_id'  => 'id',
                'type'      => 'text',
            ]
        );

        $popcard_field_settings = ['popcard_fields'];

        $prospect_fields = [
            'firstname'         => 'First Name',
            'middlename'        => 'Middle Name',
            'lastname'          => 'Last Name',
            'daytimephone'      => 'Phone (Daytime)',
            'eveningphone'      => 'Phone (Evening)',
            'cellphone'         => 'Phone (Mobile)',
            'emailaddress'      => 'Email',
            'comments'          => 'Comments',
        ];

        foreach($prospect_fields as $key => $value)
        {
            $popcard_field_settings[] = [
                'field_id'  => 'pi_prospect_' . $key,
                'title'     => $value,
                'type'      => 'text',
            ];
        }

        $preferences_fields = [
            'pricerangemin'             => 'Price Min',
            'pricerangemax'             => 'Price Max',
            'numberofoccupants'         => 'Number of Occupants',
            'dateneeded'                => 'Date Needed',
            'appointmentdate'           => 'Appointment Date',
            'appointmenttime'           => 'Appointment Time',
            'numberofbedsdesired'       => 'Desired Beds',
            'numberofbathsdesired'      => 'Desired Baths',
        ];

        foreach($preferences_fields as $key => $value)
        {
            $popcard_field_settings[] = [
                'field_id'  => 'pi_preferences_' . $key,
                'title'     => $value,
                'type'      => 'text',
            ];
        }

        call_user_func_array([$this, 'addSettingFields'], $popcard_field_settings);
	}
}