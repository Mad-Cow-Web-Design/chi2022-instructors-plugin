<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Madcow_Instructors
 * @subpackage Madcow_Instructors/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Madcow_Instructors
 * @subpackage Madcow_Instructors/public
 * @author     Your Name <email@example.com>
 */

function instructor_map() {
    if($_POST["madcow-instructors-search-filter-form-submit"]) {
		if($_POST["madcow-instructors-country-list-filter"] && $_POST["madcow-instructors-country-list-filter"] !== "") { $last_country_filtered = $_POST["madcow-instructors-country-list-filter"]; }
		if($_POST["madcow-instructors-certification-list-filter"] && $_POST["madcow-instructors-certification-list-filter"] !== "") { $last_certification_filtered = $_POST["madcow-instructors-certification-list-filter"]; }
		if($_POST["madcow-instructors-level-list-filter"] && $_POST["madcow-instructors-level-list-filter"] !== "") { $last_level_filtered = $_POST["madcow-instructors-country_list_filter"]; }
		if($_POST["madcow-instructors-search"] && $_POST["madcow-instructors-search"] !== "") { $last_search = $_POST["madcow-instructors-search"]; }
	}
	$instructors = madcow_instructors_get_instructors($last_country_filtered, $last_certification_filtered, $last_level_filtered, $last_search);
    $html = '<div id="madcow-instructors-find-an-instructor" class="acf-map madcow-instructors-google-map" data-zoom="16">';
    foreach ( $instructors as $instructor ) :
        // Creating the var instructor_id to use with ACF Pro
        $instructor_id = 'user_'. esc_html( $instructor->ID );
        $instructor_name = $instructor->display_name;
		$instructor_nicename = $instructor->user_nicename;
        $location = get_field('location', $instructor_id);
		$certification_level = ucwords(get_field( 'certification_level', $instructor_id, false));
		$chirunning_certification = get_field( 'chirunning_certification', $instructor_id, false);
		$chiwalking_certification = get_field( 'chiwalking_certification', $instructor_id, false);
		$certification_date = get_field( 'certification_date', $instructor_id, false);
		$regional_director = get_field( 'regional_director', $instructor_id, false);
		$marker_icon = "";

		if($regional_director === "yes") {
			$marker_icon = esc_url( plugins_url('images/pin-master-instructor.png', __FILE__ ) );
		}
		else {
			if((isset($chiwalking_certification) && !isset($chirunning_certification)) || (($chiwalking_certification == "yes") && ($chirunning_certification == "no"))) {
				$marker_icon = esc_url( plugins_url('images/pin-chiwalking-instructor.png', __FILE__ ) );
			}
			else {
				switch ($certification_level) {
					case "Certified Instructor":
						$marker_icon = esc_url( plugins_url('images/pin-chirunning-chiwalking-instructor.png', __FILE__ ) );
						break;
					case "Senior Instructor":
						$marker_icon = esc_url( plugins_url('images/pin-senior-instructor.png', __FILE__ ) );
						break;
					case "Master Instructor":
						$marker_icon = esc_url( plugins_url('images/pin-regional-director.png', __FILE__ ) );
						break;
					default:
						$marker_icon = "";
				}
			}
		}

        if( $location['lat'] && $location['lng'] ) :
			$html .= '<div id="marker-' . $instructor_nicename . '" class="marker" data-lat="' . $location["lat"] . '" data-lng="' . $location["lng"] . '" data-marker="' . $marker_icon . '">';
			$html .= '<div id="' . $instructor_nicename . '" class="madcow-instructors-map-marker">';
			$html .= '<div id="' . $instructor_nicename . '-photo" class="madcow-instructors-map-marker-left">';
			$html .= get_avatar($instructor->ID, 96, '', $instructor_name, array());
			$html .= '</div>';
			$html .= '<div id="' . $instructor_nicename . '-details" class="madcow-instructors-map-marker-right">';
			$html .= '<h5><a href="/instructor/' . $instructor_nicename . '/" class="madcow-instructors-map-marker-name-link">' . $instructor_name . '</a></h5>';
			$html .= '<p>' . $certification_level . '</p>';
			$html .= '</div>';
			$html .= '<div style="clear:both"></div>';
			$html .= '<a href="' . home_url('/') . 'instructor/' . $instructor_nicename . '/" class="madcow-instructors-list-button madcow-instructors-infowindow-button"><span>VIEW PROFILE &amp; WORKSHOPS</span></a>';
			$html .= '</div>';
			$html .= '</div>';
        endif;
    endforeach;
    $html .= '</div><!-- end acf map -->';

    return $html;
}

function madcow_instructors_show_map_legend() {
	echo '<div class="map-legend">';
		echo '<div class="running-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-chirunning-chiwalking-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIRUNNING</br>CERTIFIED INSTRUCTOR';
		echo '</div>';

		echo '<div class="running-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-senior-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIRUNNING</br>SENIOR INSTRUCTOR';
		echo '</div>';

		echo '<div class="running-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-regional-director.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIRUNNING</br>MASTER INSTRUCTOR';
		echo '</div>';

		echo '<div class="running-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-master-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHILIVING</br>REGIONAL DIRECTOR';
		echo '</div>';

		echo '<div class="walking-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-chiwalking-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIWALKING</br>CERTIFIED INSTRUCTOR';
		echo '</div>';
	echo '</div>';
}

function madcow_instructors_show_instructors_search_filter($show_country_list_filter = null, $show_certification_filter = null, $show_level_filter = null, $show_search = null) {
	//Grab values of previous form submission if applicable
	if($_POST["madcow-instructors-search-filter-form-submit"]) {
		$last_country_filtered = $_POST["madcow-instructors-country-list-filter"];
		$last_certification_filtered = $_POST["madcow-instructors-certification-list-filter"];
		$last_level_filtered = $_POST["madcow-instructors-level-list-filter"];
		$last_search = $_POST["madcow-instructors-search"];
	}

	//temporarily overriding default values, set up for shortcode parameters later on
	$show_country_list_filter = TRUE;
	$show_certification_filter = FALSE;
	$show_level_filter = FALSE;
	$show_search = TRUE;

	//Start form
	echo '<form id="madcow-instructors-search-filter-form" method="post" action="' . get_permalink() . '">';

	if($show_country_list_filter) {
		echo madcow_instructors_show_country_list_filter($last_country_filtered);
	}

	if($show_certification_filter) {
		echo madcow_instructors_show_certification_filter($last_certification_filtered);
	}

	if($show_level_filter) {
		echo madcow_instructors_show_level_filter($last_level_filtered);
	}

	if($show_search) {
		echo '<label for="madcow-instructors-search"></label><input type="text" id="madcow-instructors-search" name="madcow-instructors-search" placeholder="Search Certified Instructors" />';
	}

	echo '<input type="submit" id="madcow-instructors-search-filter-form-submit" name="madcow-instructors-search-filter-form-submit" value="Filter / Search" />';
	echo '</form>';
}

function madcow_instructors_show_instructors_list($country_list_filter = "", $certification_filter = "", $level_filter = "", $search_query = "") {
	//if search/filter form post
	if($_POST["madcow-instructors-search-filter-form-submit"]) {
		$country_list_filter = $_POST["madcow-instructors-country-list-filter"];
		$certification_filter = $_POST["madcow-instructors-certification_filter"];
		$level_filter = $_POST["madcow-instructors-level-list-filter"];
		$search_query = $_POST["madcow-instructors-search"];
/* 		echo "Checking form values:<br />";
		echo $country_list_filter . "<br />";
		echo $certification_filter . "<br />";
		echo $level_filter . "<br />";
		echo $search_query . "<br />"; */
	}
	$instructors = madcow_instructors_get_instructors($country_list_filter, $certification_filter, $level_filter, $search_query);

	//results count
	$num_results = count($instructors);

	$html = '<div id="madcow-instructors-instructor-count" class=""><em>Showing ';
	$html .= $num_results . ' ';
	if($num_results == 1) {
		$html .= 'instructor';
	}
	else {
		$html .= 'instructors';
	}
	$html .= '</em></div>';

	$html .= '<div id="madcow-instructors-instructor-list" class="">';

	foreach ( $instructors as $instructor ) :
		$instructor_id = 'user_'. esc_html( $instructor->ID );
        $instructor_name = $instructor->display_name;
		$instructor_nicename = $instructor->user_nicename;
        $instructor_photo = get_field('source_photo', $instructor_id);
        $location = get_field('location', $instructor_id);
		$certification_level = ucwords(get_field( 'certification_level', $instructor_id, false));
		$chirunning_certification = get_field( 'chirunning_certification', $instructor_id, false);
		$chiwalking_certification = get_field( 'chiwalking_certification', $instructor_id, false);
		$certification_date = get_field( 'certification_date', $instructor_id, false);
		$regional_director = get_field( 'regional_director', $instructor_id, false);

		$html .= '<div class="madcow-instructor-list-item">';
		$html .= '<article class="madcow-instructor-list-item-article">';
        if($instructor_photo) {
            $html .= '<img class="instructor-photo" src="' . $instructor_photo . '">';
        }
        else {
		    $html .= get_avatar($instructor->ID, 96, '', $instructor_name, array());
        }
		$html .= '<h5>' . $instructor_name . '</h5>';
		$html .= '<div class="certification-level">';
		if($chirunning_certification == "yes") {
			$html .= '<figure><img class="cert-level-icon" src="' . esc_url( plugins_url('images/chirunning-circle.svg', __FILE__ ) ) . '" alt="ChiRunning Certified" /></figure>';
		}
		if($chiwalking_certification == "yes") {
			$html .= '<figure><img class="cert-level-icon" src="' . esc_url( plugins_url('images/chiwalking-circle.svg', __FILE__ ) ) . '" alt="ChiWalking Certified" /></figure>';
		}
        $html .= $certification_level . '</div>';
		$html .= '<a href="' . home_url('/') . 'instructor/' . $instructor_nicename . '/" class="madcow-instructors-list-button"><span>VIEW PROFILE &amp; WORKSHOPS</span></a>';
        $html .= '</article>';
		$html .= '</div>';
	endforeach;

	$html .= '</div>';

    return $html;
}

//Helper functions

//Returns list of instructors based on filters and search query, defaults to returning full list without filters or search query
function madcow_instructors_get_instructors($country_list_filter = "", $certification_filter = "", $level_filter = "", $search_query = "") {
	/* Types of filters
		Certification - chirunning, chiwalking
		Level - certified_instructor, senior_instructor, master_instructor, regional_director
		Country - based on countries with instructors
		Search */

	$countries = get_all_countries();	
	
	//Get All Instructors
	$instructors = get_users( array( 'role__in' => array( 'instructor' ) ) );

	//Set up $temp array for holding filtered results
	$temp = array();	

	//Loop through all Instructors and search / filter
	foreach ( $instructors as $instructor ) :
		$instructor_id = 'user_'. esc_html( $instructor->ID );
		$location = get_field('location', $instructor_id);
		$instructor_data = get_userdata($instructor_id);
		$username = $instructor_data->user_login;
		$nicename = $instructor_data->user_nicename;
		$firstname = $instructor_data->first_name;
		$lastname = $instructor_data->last_name;
		$email = $instructor_data->user_email;
		$city = $location['city'];
		$state = $location['state'];

		//Check for username, nicename, firstname, lastname, email, city, state/province
		//This should have the state long and short names and it should also use strpos for comparison since we are not using any other wildcard method or regex
		if(($username == $search_query) || ($nicename == $search_query) || ($firstname == $search_query) || ($lastname == $search_query) || ($email == $search_query) || ($city == $search_query) || ($state == $search_query)) {
			$temp[] = $instructor;
		}
		
		//Check for Country long name and Country Short Name
		if($location['country'] || $location['country_short']) {
			//Find the short name of the country from the long name, helps to standardize data
			$key = array_search($location['country'], $countries);
			
			//Use strpos instead of regex for performance and in case the full name of the country has missing parts ie: United States / United States of America
			if((strpos($key,$country_list_filter) === 0) || (strpos($location['country'],$country_list_filter) === 0) || (strpos($search_query,$country_list_filter) === 0)) {
				$temp[] = $instructor;
			}
		}
	endforeach;
	$instructors = $temp;

	//Set up for later use if desired
	switch($certification_filter) {
		case "chirunning":
			break;
		case "chiwalking":
			break;
		default:
	}

	//Set up for later use if desired
	switch($level_filter) {
		case "certified_instructor":
			break;
		case "senior_instructor":
			break;
		case "master_instructor":
			break;
		case "regional_director":
			break;
		default:
	}

	return $instructors;
}

function madcow_instructors_show_country_list_filter($last_country_filtered) {
	$countries = madcow_instructors_get_instructor_countries();

	$html = '<label for="madcow-instructors-country-list-filter"></label>';
	$html .= '<select name="madcow-instructors-country-list-filter" id="madcow-instructors-country-list-filter" class="madcow-instructors-filter-select madcow-instructors-countries-select">';

	$html .= '<option value="" ';
	if (!isset($last_country_filtered) || $last_country_filtered == "") {
		$html .= 'selected="selected"';
	}
	$html .= '>Search by Country / Region</option>';


	foreach($countries as $country=>$country_name) :
		$html .= '<option value="' . $country . '"';
		if($last_country_filtered == $country) {
			$html .= 'selected="selected"';
		}
		$html .= '>' . $country_name . '</option>';
	endforeach;

	$html .= '</select>';

	return $html;
}

function madcow_instructors_show_certification_filter($last_certification_filtered) {
	$html = '<label for="madcow-instructors-certification-list-filter">Country: </label>';
	$html .= '<select name="madcow-instructors-certification-list-filter" id="madcow-instructors-certification-list-filter" class="madcow-instructors-filter-select madcow-instructors-certification-select">';

	//options for certifications

	$html .= '</select>';

	return $html;
}

function madcow_instructors_show_level_filter($last_level_filtered) {
	$html = '<label for="madcow-instructors-level-list-filter">Country: </label>';
	$html .= '<select name="madcow-instructors-level-list-filter" id="madcow-instructors-level-list-filter" class="madcow-instructors-filter-select madcow-instructors-level-select">';

	//options for levels

	$html .= '</select>';

	return $html;
}

function madcow_instructors_get_instructor_countries() {
	$countries = get_all_countries();

	$instructor_countries = array();
 	$instructors = get_users( array( 'role__in' => array( 'instructor' ) ) );

    foreach ( $instructors as $instructor ) :
        $instructor_id = 'user_'. esc_html( $instructor->ID );
        $location = get_field('location', $instructor_id);

        if($location['country']) {
			$key = array_search($location['country'], $countries);
			if($key) {
				$instructor_countries[$key] = $location['country'];
			}
		}
	endforeach;

	asort($instructor_countries);
	return $instructor_countries;
}

function get_all_countries() {
	$countries = array
	(
		'AF' => 'Afghanistan',
		'AX' => 'Aland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, Democratic Republic',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'Ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island & Mcdonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic Of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle Of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KR' => 'Korea',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States Of',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barthelemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin',
		'PM' => 'Saint Pierre And Miquelon',
		'VC' => 'Saint Vincent And Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia And Sandwich Isl.',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard And Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks And Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'WF' => 'Wallis And Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);
	
	return $countries;
}

/* Reverse GeoCoding */
/*
//Run on Profile save, check to see if there is a value being saved in ACF for country, if not - reverse geocode to get the country and save that
function madcow_instructors_get_instructor_address($lat,$lng)
{
    $country = array();
	//$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&sensor=false&key=AIzaSyCbUl_nRuqQqr3mNXHtD-Z8erSkvRlwMfM';
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&sensor=false&key=AIzaSyAUFQIb76kk-aNd6PaafnxkgM54RDIfZgE';
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if($status=="OK") {
        //Get address from json data
        for ($j=0;$j<count($data->results[0]->address_components);$j++) {
            $cn=array($data->results[0]->address_components[$j]->types[0]);
            if(in_array("country", $cn)) {
                $country_short = $data->results[0]->address_components[$j]->short_name;
				$country_long = $data->results[0]->address_components[$j]->long_name;
            }
        }
    }
	$country[$country_short] = $country_long;
	return $country;
}

add_action( 'personal_options_update',  'madcow_instructors_update_instructor_country' );
add_action( 'edit_user_profile_update', 'madcow_instructors_update_instructor_country' );

function madcow_instructors_update_instructor_country($user_id) {
	$updated_location = $_POST['acf']['field_61b3e19d0c5ce'];
	$country = $updated_location['country'];
	$country_short = $updated_location['country_short'];
	$lat = $updated_location['lat'];
	$lng = $updated_location['lng'];

	if(!$user_id) {
		$user = wp_get_current_user();
		$instructor_id = 'user_' . $user->ID;
	}
	else {
		$instructor_id = 'user_'. $user_id;
	}

 	if($country == "" && $country_short == "") {
		if($lat !== "" && $lng !== "") {
			$reverse_geo_result = madcow_instructors_get_instructor_address($lat,$lng);
			$updated_location['country'] = $reverse_geo_result[0];
			$updated_location['country_short'] = array_keys($reverse_geo_result)[0];

			update_field('location', $updated_location , $instructor_id);
		}
	}
} */


/* WORKSHOPS MAP */

function workshop_map() {

	$workshops = madcow_instructors_get_workshops();
	
    $html = '<div id="madcow-instructors-find-an-instructor" class="acf-map madcow-instructors-google-map madcow-instructors-workshop-map" data-zoom="16">';
    foreach ( $workshops as $workshop ) :
        // Creating the var workshop_id to use with ACF Pro
        $workshop_id = esc_html( $workshop->ID );
		$workshop_slug = esc_html( $workshop->post_name );
		$workshop_name = get_field('name', $workshop_id);
        $location = get_field('location', $workshop_id);
		$types = get_the_terms( $workshop->ID, 'workshop_type');
		if ( ! empty( $types ) && ! is_wp_error( $types ) ) {
			$workshop_types = wp_list_pluck( $types, 'slug' );
		}

		$marker_icon = "";
		
		switch ($workshop_types[0]) {
			case "chirunning-clinic":
				$marker_icon = esc_url( plugins_url('images/pin-chirunning-chiwalking-instructor.png', __FILE__ ) );
				break;
			case "chirunning-tune-up":
				$marker_icon = esc_url( plugins_url('images/pin-chirunning-chiwalking-instructor.png', __FILE__ ) );
				break;
			case "chirunning-workshop":
				$marker_icon = esc_url( plugins_url('images/pin-chirunning-chiwalking-instructor.png', __FILE__ ) );
				break;
			case "chiwalk-run-workshop":
				$marker_icon = esc_url( plugins_url('images/pin-master-instructor.png', __FILE__ ) );
				break;
			case "chiwalking-workshop":
				$marker_icon = esc_url( plugins_url('/images/pin-chiwalking-instructor.png', __FILE__ ) );
				break;
			default:
				$marker_icon = "";
		}

        if( $location['lat'] && $location['lng'] ) :
			$html .= '<div id="marker-' . $workshop_name . '" class="marker" data-lat="' . $location["lat"] . '" data-lng="' . $location["lng"] . '"';
			if($marker_icon) {
				$html .= ' data-marker="' . $marker_icon . '"';
			}
			$html .= '>';
			$html .= '<div id="' . $workshop_name . '" class="madcow-instructors-workshops-map-marker">';
			$html .= '<h5><a href="' . home_url('/') . 'workshops/' . $workshop_slug . '/" class="madcow-instructors-map-marker-name-link">' . $workshop_name . '</a></h5>';
			$html .= '<a href="' . home_url('/') . 'workshops/' . $workshop_slug . '/" class="madcow-instructors-list-button madcow-instructors-infowindow-button"><span>VIEW WORKSHOP</span></a>';
			$html .= '</div>';
			$html .= '</div>';
        endif;
    endforeach;
    $html .= '</div><!-- end acf map -->';
	
    return $html;
}

function madcow_workshops_show_map_legend() {
 	echo '<div class="map-legend">';
		echo '<div class="running-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-chirunning-chiwalking-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIRUNNING EVENTS';
		echo '</div>';

		echo '<div class="running-legend">';
			echo '&nbsp;';
		echo '</div>';

		echo '<div class="running-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-master-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIWALK-RUN EVENTS';
		echo '</div>';

		echo '<div class="running-legend">';
			echo '&nbsp;';
		echo '</div>';

		echo '<div class="walking-legend">';
			echo '<figure>';
			echo '<img src="' . esc_url( plugins_url('images/pin-chiwalking-instructor.png', __FILE__ ) ) . '" />';
			echo '</figure>';
			echo 'CHIWALKING EVENTS';
		echo '</div>';
	echo '</div>';
}

function madcow_instructors_get_workshops() {
	return get_posts(array('post_type' => 'workshops', 'numberposts' => -1));
}