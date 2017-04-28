<?php

class ContactForm7Helper {
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
        add_action('wpcf7_submit', [$this, 'handleSubmit'], 10, 2);
        add_filter('popcard_integration_form_list', [$this, 'getFormList']);
    }

    public function getFormList($list)
    {
        $forms = get_posts([
            'post_type'         => 'wpcf7_contact_form',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'orderby'           => 'post_title',
            'order'             => 'ASC',
        ]);

        if(count($forms))
        {
            $form_list = [];

            foreach($forms as $form)
            {
                $form_list['cf7_' . $form->ID] = $form->post_title;
            }
            $list['Contact Form 7'] = $form_list;
        }

        return $list;
    }

    public function handleSubmit(WPCF7_ContactForm $form, $result)
    {
        $submission = WPCF7_Submission::get_instance();
        $form_id = 'cf7_' . $form->id();
        $form_data = [];

        foreach($submission->get_posted_data() as $key => $value)
        {
            if($key[0] === '_') continue;

            $form_data[$key] = $value;
        }

        do_action('rsvp_form_submitted', $form_id, $form_data);
    }
}