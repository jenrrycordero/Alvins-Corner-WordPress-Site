<?php
/**
 * Created by PhpStorm.
 * User: alian
 * Date: 04-29-2015
 * Time: 13:37
 */


add_action( 'wp_ajax_nopriv_model_print_pdf', 'razz_get_model_pdf' );
add_action( 'wp_ajax_model_print_pdf', 'razz_get_model_pdf' );

function razz_get_model_pdf()
{
    require_once( __DIR__ . '/tcpdf/tcpdf.php');


    //First we get the model info.
    $model_id = $_GET['model'];

    $query_args = array(
        'post_type' => 'razz-apt-model',
        'p' => $model_id
    );
    $query = new WP_Query( $query_args );

    if ( !$query->have_posts() )
    {
        ob_clean();
        _e("There is no model with that id.", 'razz-apt-model');
        wp_die();
    }
    else
    {

        $query->the_post();
        $title = get_the_title();


        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('RazzInteractive');
        $pdf->SetTitle("Model $title");
        $pdf->SetSubject("PDF Information of the model $title" );
        $pdf->SetKeywords("Model, $title, " . get_bloginfo("name") );


        // set default header data
        $pdf->SetHeaderData("", "", "Information of the Model $title", str_replace("http://", "", site_url() ) );

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -------------------------------------------------------------------

        // add a page
        $pdf->AddPage();

        // set JPEG quality
        $pdf->setJPEGQuality(75);

        //First we add the image for the model.
        $image = get_field('image');
        $img_path = get_attached_file( $image['id'] );

//        echo '<pre>';
//        var_dump( $image );
//        echo '</pre>';
//        exit;

        if ( $img_path && is_file($img_path) )
        {
            $img_info = getimagesize($img_path);
            $ratio = $img_info[0] / $img_info[1];

            $w = 180;
            $h = $w / $ratio;

            if ( $ratio < 1 && $h > 220)
            {
                //the image has a portrait orientation. we should control the height
                $h = 220;
                $w = $h * $ratio;
            }

            // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
            $pdf->Image($img_path, 0, 16, $w, $h, '', '', 'M', false, 300, 'C');
        }


        //Model info. ACF
        $beds = get_field('bed');
        $baths = get_field('bath');
        $sqft = get_field('sqft');

        if ( $beds )
            $pdf->writeHTMLCell(0,0,PDF_MARGIN_LEFT, $h+18, "<b>N. of Bedrooms $y: </b>" . $beds );

        if ( $baths )
            $pdf->writeHTMLCell(0,0,PDF_MARGIN_LEFT, $pdf->GetY() + $pdf->getLastH(), "<b>N. of Bathrooms: </b>" . $baths );

        if ( $sqft )
            $pdf->writeHTMLCell(0,0,PDF_MARGIN_LEFT, $pdf->GetY() + $pdf->getLastH(), "<b>Area of the model: </b>" . $sqft . "<sub>sqft</sub>" );


        $model_price_override = get_field('specific_price_override');
        $price = get_field('price');
        $option_display_price = get_field('display_price', "option");
        $option_price_label = get_field('price_override_label', "option");

        if ( $option_display_price )
        {
            if ( $option_price_label != "" )
            {
                $pdf_display_price = $option_price_label;
            }
            else if ( $model_price_override != '' )
            {
                $pdf_display_price = $model_price_override;
            }
            else
            {
                $pdf_display_price = ($price != "") ? $price . " $" :  "";
            }

            $pdf->writeHTMLCell(0,0,PDF_MARGIN_LEFT, $pdf->GetY() + $pdf->getLastH(), "<b>Price: </b>" . $pdf_display_price );
        }

        //Close and output PDF document

        //============================================================+
        // END OF FILE
        //============================================================+

        ob_clean();
        $pdf->Output('example_009.pdf', 'I');
        wp_die();
    }

    ob_clean();
    wp_die();
}