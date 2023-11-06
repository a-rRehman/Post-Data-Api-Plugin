<?php
/*
Plugin Name: Data Sending Plugin
Description: Send data to a remote API.
Version: 1.0
Author: Your Name
 */

// Define the function to handle form submission and send data to the API.
function send_data_to_api()
{
    if (isset($_POST['submit_data'])) {
        // Initialize an empty array to store employee data.
        $employees = [];

        // Check if employee data is provided in the form.
        if (isset($_POST['employee_id']) && isset($_POST['employee_name']) && isset($_POST['employee_salary'])) {
            // Capture user input for employee data.
            $employee_id = sanitize_text_field($_POST['employee_id']);
            $employee_name = sanitize_text_field($_POST['employee_name']);
            $employee_salary = sanitize_text_field($_POST['employee_salary']);

            // Create an employee array and add it to the employees array.
            $employee = [
                "id" => $employee_id,
                "name" => $employee_name,
                "salary" => $employee_salary,
            ];
            $employees[] = $employee;
        }

        // Define the API endpoint you want to send the data to.
        $api_url = 'http://localhost:3000/data';

        // Create the data array to send.
        $data = [
            "employees" => $employees,
        ];

        // Encode the data in the request body as JSON.
        $data_to_send = json_encode($data);

        $response = wp_remote_post($api_url, array(
            'body' => $data_to_send,
            'headers' => array('Content-Type' => 'application/json'),
        ));

        if (is_wp_error($response)) {
            return 'Failed to send data to the API. Please try again later.';
        }

        // Process the API response as needed.
        $body = wp_remote_retrieve_body($response);
        return 'API Response: ' . esc_html($body);
    }
}

// Update the form to capture employee data.
function data_sending_form_shortcode()
{
    $result = send_data_to_api();

    // Display the form and the result.
    $form = '<form method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '">
        <input type="text" name="employee_id" placeholder="Employee ID" />
        <input type="text" name="employee_name" placeholder="Employee Name" />
        <input type="text" name="employee_salary" placeholder="Employee Salary" />
        <input type="submit" name="submit_data" value="Submit" />
    </form>';

    return '<div class="data-sending-form">' . $form . $result . '</div>';
}
add_shortcode('data-sending-form', 'data_sending_form_shortcode');
