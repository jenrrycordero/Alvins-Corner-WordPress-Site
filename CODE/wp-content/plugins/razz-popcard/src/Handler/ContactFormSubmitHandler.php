<?php


class ContactFormSubmitHandler
{

    function __construct()
    {
        add_action('rsvp_form_submitted', [$this, 'handleFormSubmission'], 10, 2);
    }

    public function handleFormSubmission($form_id, $data)
    {
        $integrations = get_posts([ //TODO: set campaign on form instead of setting form on campaign
            'post_type'         => 'popcard_integration',
            'post_status'       => 'publish',
            'meta_name'         => 'form_id',
            'meta_value'        => $form_id,
            'posts_per_page'    => 1,
        ]);

        if(count($integrations) < 1) return;

        /** @var WP_Post $integration */
        $integration = $integrations[0];

        $popcard = new PopcardIntegrationHandler($integration);
        $popcard->setData($data);

        $popcard->sendToPopcard();
    }
}