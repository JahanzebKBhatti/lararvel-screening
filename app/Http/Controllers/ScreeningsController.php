<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Screening;

class ScreeningsController extends Controller
{
    public function screeningForm(Request $request) {
        return view('screening');
    }

    // Store Form data in database
    public function screeingFormSubmit(Request $request) {
        // Form validation

        $dob = ''; // MM/DD/YYYY
        if(!empty($request->input('dob_month')) && !empty($request->input('dob_month')) && !empty($request->input('dob_year'))) {
            $dob = [
                        $request->input('dob_month'),
                        $request->input('dob_day'),
                        $request->input('dob_year')
                    ];
            $dob = implode('/', $dob);
            $request->request->add(['dob' => $dob]);
        }

        // Setup generic rules using core laravel rules
        $validation_rules = [
            'firstname'             => 'required',
            'migraine_frequency'    => 'required'
        ];

        $validation_messages = [];

        // Setup custom validation for date
        $validation_rules['dob'] = [
            'required',
            function ($attribute, $value, $fail) {
                $date = explode('/', $value);
                if (checkdate($date[0], $date[1], $date[2])) {
                    $difference = strtotime($value) - strtotime("-18 years", time());
                    if ($difference > 0) {
                        $fail('Date of Birth indicates age less than 18 years.');
                    }
                } else {
                    $fail('Date of Birth given ('.$date['1'].'/'.$date[0].'/'.$date[2].') is not a valid date.');
                }
            }
        ];

        // Check for 'Daily' migraine
        if($request->input('migraine_frequency') == 'daily') {
            $validation_rules['daily_frequency'] = 'required';
            $validation_messages['daily_frequency.required'] = 'Please specify number of occurrences for daily migraines';
        }

        $this->validate($request, $validation_rules, $validation_messages);

        // If Request is valid
        $cohort = 'A';
        if ($request->input('migraine_frequency') == 'daily') {
            $cohort = 'B';
        } else {
            $request->request->set('daily_frequency', null);
        }

        $request->request->add(['cohort' => $cohort]);
        $request->request->set('dob', strtotime($request->input('dob'))); // Convert DOB into timestamp

        //  Store data in database
        Screening::create($request->all());

        return back()->with('success', 'Candidate "'.$request->input('firstname').'" is assigned to Cohort '.$cohort.'.');
    }
}
