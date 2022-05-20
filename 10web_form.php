<?php

/**
 * Plugin Name: 10web_form
 * Description: simple form for contacts and send our costumer meesage, show all in admin page
 */

// register jquery and style on initialization
add_action('init', 'registerScript');
function registerScript()
{
  wp_register_script('custom_script', plugins_url('js/scripts.js', __FILE__), array('jquery'), '2.5.1');
  wp_register_script('mobiscroll_script', plugins_url('js/mobiscroll.javascript.min.js', __FILE__), array('jquery'), '2.5.1');

  wp_register_style('new_style', plugins_url('css/style.css', __FILE__), false, '1.0.0', 'all');
  wp_register_style('mobiscroll_style', plugins_url('css/mobiscroll.javascript.min.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueueStyle');

function enqueueStyle()
{
  wp_enqueue_script('custom_script');
  wp_enqueue_script('mobiscroll_script');

  wp_enqueue_style('new_style');
  wp_enqueue_style('mobiscroll_style');
}

function scratchcodeCreateContactsMessagesTable()
{

  global $wpdb;

  $table_name = $wpdb->prefix . "contacts_messages";

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    first_name char(30) NOT NULL,
    last_name char(30) NOT NULL,
    email varchar(100) NOT NULL,
    gender char(30),
    selected_date datetime,
    files varchar(300) ,
    comments varchar(500) NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY id (id)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

add_action('init', 'scratchcodeCreateContactsMessagesTable');

function scratchcodeCreateContactsEventsTable()
{

  global $wpdb;

  $table_name = $wpdb->prefix . "contacts_events";

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    start_time datetime,
    end_time datetime,
    title varchar(100),
    descriptions varchar(500),
    allDay char(30),
    free char(30),
    color varchar(500) NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY id (id)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

add_action('init', 'scratchcodeCreateContactsEventsTable');


function contentContactShortCode()
{
  $contact_submited_message = isset($_POST['contact_submited_message']) ? $_POST['contact_submited_message'] : '';
  $contactsHtml =
    '<div class="contact-form">
      <form method="POST"  enctype="multipart/form-data">
        <div>
          <h1>Contact Us</h1>
        </div>
        <div id="left">
          <input type="text" class="contact-field" name="input_first_name" placeholder="First Name"
            required="yes" /> <label class="contact-field-required"></label><br />
          <input type="text" class="contact-field" name="input_last_name" placeholder="Last Name"
            required="yes" /> <label class="contact-field-required"></label><br />
          <input type="email" class="contact-field" name="input_email" placeholder="Email" required="yes" />
          <label class="contact-field-required"></label><br />
          <input placeholder="Set an event in calendar" class="contact-field" type="text" id="modal-btn310"
            name="input_date" readonly> <br />
          <select class="contact-field" name="input_gender">
            <option value="" selected disabled>Select your gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Non-binary">Non-binary</option>
            <option value="Transgender">Transgender</option>
            <option value="Intersex">Intersex</option>
            <option value="Other">Other</option>
            <option value="I perfer not to say">I perfer not to say</option>
          </select><br />

        </div>

        <div id="right">
          <input type="file" class="contact-field" name="input_file" /> <br />
          <textarea class="contact-field contact-textarea" name="input_comments"
            placeholder="Questions or Comments"></textarea> <label
            class="contact-field-required"></label><br />
          <input type="submit" class="contact-field submit-form" name="form_submit" onClick="(this.value=\'Submit\')" />
        </div>
        <br />
      </form>
      <p>'. $contact_submited_message .'</p>
    </div>';
  $modalHtml =
    '<div>
      <div class="modal310">
        <div class="modal-content">
          <span class="close-btn310">&times;</span>
          <div id="demo-add-delete-event"></div>

          <div id="demo-add-popup">
            <div class="mbsc-form-group">
              <label>
                Title
                <input mbsc-input id="event-title">
              </label>
              <label>
                Description
                <textarea mbsc-textarea id="event-desc"></textarea>
              </label>
            </div>
            <div class="mbsc-form-group">
              <label>
                All-day
                <input mbsc-switch id="event-all-day" type="checkbox" />
              </label>
              <label>
                Starts
                <input mbsc-input id="start-input" />
              </label>
              <label>
                Ends
                <input mbsc-input id="end-input" />
              </label>
              <div id="event-date"></div>
              <div id="event-color-picker" class="event-color-c">
                <div class="event-color-label">Color</div>
                <div id="event-color-cont">
                  <div id="event-color" class="event-color"></div>
                </div>
              </div>
              <label>
                Show as busy
                <input id="event-status-busy" mbsc-segmented type="radio" name="event-status"
                  value="busy">
              </label>
              <label>
                Show as free
                <input id="event-status-free" mbsc-segmented type="radio" name="event-status"
                  value="free">
              </label>
              <div class="mbsc-button-group">
                <button class="mbsc-button-block" id="event-delete" mbsc-button data-color="danger"
                  data-variant="outline">Delete event</button>
              </div>
            </div>
          </div>

          <div id="demo-event-color">
            <div class="crud-color-row">
              <div class="crud-color-c" data-value="#ffeb3c">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#ffeb3c"></div>
              </div>
              <div class="crud-color-c" data-value="#ff9900">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#ff9900"></div>
              </div>
              <div class="crud-color-c" data-value="#f44437">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#f44437"></div>
              </div>
              <div class="crud-color-c" data-value="#ea1e63">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#ea1e63"></div>
              </div>
              <div class="crud-color-c" data-value="#9c26b0">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#9c26b0"></div>
              </div>
            </div>
            <div class="crud-color-row">
              <div class="crud-color-c" data-value="#3f51b5">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#3f51b5"></div>
              </div>
              <div class="crud-color-c" data-value="">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"></div>
              </div>
              <div class="crud-color-c" data-value="#009788">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#009788"></div>
              </div>
              <div class="crud-color-c" data-value="#4baf4f">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#4baf4f"></div>
              </div>
              <div class="crud-color-c" data-value="#7e5d4e">
                <div class="crud-color mbsc-icon mbsc-font-icon mbsc-icon-material-check"
                  style="background:#7e5d4e"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>';

  return $contactsHtml . $modalHtml;
}

add_shortcode('10webContact', 'contentContactShortCode');

function saveInContactsMessagesTable()
{
  global $wpdb;
  if (isset($_POST['form_submit'])) {
    unset($_POST['form_submit']);

    $table = 'wp_contacts_messages';

    $firstName = sanitize_text_field($_POST['input_first_name']);
    $lastName = sanitize_text_field($_POST['input_last_name']);
    $email = sanitize_text_field($_POST['input_email']);
    $date = sanitize_text_field(isset($_POST['input_date']) ? $_POST['input_date'] : null);
    $gender = sanitize_text_field(isset($_POST['input_gender']) ? $_POST['input_gender'] : null);
    $comments = sanitize_text_field(isset($_POST['input_comments']) ? $_POST['input_comments'] : null);
    if(!file_exists($_FILES['input_file']['tmp_name']) || !is_uploaded_file($_FILES['input_file']['tmp_name'])) {
      $file = '';
    }else{
      $filename = $_FILES['input_file']['name'];
      $wpFiletype = wp_check_filetype(basename($filename), null);
      $wpUploadDir = wp_upload_dir();
      $fileSize = getimagesize($_FILES['input_file']['tmp_name']);
  
      if ($fileSize == FALSE) {
        echo 'The image was not uploaded';
      } else if (move_uploaded_file($_FILES['input_file']['tmp_name'], $wpUploadDir['path']  . '/' . $filename)) {
        $file = $wpUploadDir['path']  . '/' . $filename;
      } else {
        echo "Sorry, there was a problem uploading your file.";
      }
      unset($_FILES['input_file']);
    }


    $query = $wpdb->insert(
      $table,
      array(
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'gender' => $gender,
        'selected_date' => $date,
        'comments' => $comments,
        'created_at' => date("Y-m-d H:i:s"),
        'files' => $file,
      )
    );

    if ($query) {
      $response['error'] = "true";
      $_POST['contact_submited_message'] = 'Thank you for the submitted form!';
    } else {
      $response['error'] = "false";
      $_POST['contact_submited_message'] = '';
    }
  }
}
add_action('wp_head', 'saveInContactsMessagesTable');

function contentAdminShortCode()
{
  global $wpdb;
  $contacts_messages = $wpdb->get_results("SELECT * FROM wp_contacts_messages order by created_at Desc");

  $tableRow = '';
  $tableTitle = '
  <div class="row">
			<div class="col-md-12">
				<div class="table-wrap">
					<table class="table">
						<thead class="thead-primary">
							<tr>
								<th>#</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email Address</th>
								<th>Date</th>
								<th>Gender</th>
								<th>File</th>
								<th>Comments & Questions</th>
								<th>Postage Date</th>
							</tr>
						</thead>
						<tbody>';
  foreach ($contacts_messages as $index => $contacts_message) {
    $tableRow .= '
              <tr>
								<th scope="row">' . ($index + 1) . '</th>
								<td>' . $contacts_message->first_name . '</td>
								<td>' . $contacts_message->last_name . '</td>
								<td>' . $contacts_message->email . '</td>
								<td>' . $contacts_message->selected_date . '</td>
								<td>' . $contacts_message->gender . '</td>
								<td>' . $contacts_message->files . '</td>
								<td>' . $contacts_message->comments . '</td>
								<td>' . $contacts_message->created_at . '</td>
							</tr>
              ';
  }
  $tableFooter =
    '</tbody>
					</table>
				</div>
			</div>
		</div>
  ';

  return $tableTitle . $tableRow . $tableFooter;
}

add_shortcode('10webAdmin', 'contentAdminShortCode');
