<?php

/**
 * This is a thin script that shows how to use the Packal class to submit things to Packal.org using
 * through the Packal API. If you want a simple solution, then use the Packal Workflow. If you use your
 * own scripts to build clean versions of your workflows and want to implement automatic submissions to
 * Packal, then adapt this code for your build script.
 */

// Require the Packal file that contains the Packal class
require_once( 'packal.php' );

// You should probably never hardcode your username and password into the file. But we'll reuse these, so
// you need to set them. If you don't have a packal account, then go onto Packal.org and register one.
$username = 'my_packal_username';
$password = 'my_packal_password';

/**
 * Submit a Workflow Example
 *
 * If you adapt this for a custom build script, you'll probably want to have a dynamic way to pass the
 * filename and the version, but these are hardcoded here as a simple usage example. The each workflow
 * must have a workflow.ini in its root directory, and if you want to include screenshots, then they
 * must be in a folder in the root directory called 'screenshots' within the Alfredworkflow file. If
 * you want to include a "long description" (recommended), then you must have a `README.md` file in
 * the workflow root. You can override the location of the `README.md` file in your `workflow.ini` file,
 * and you can also override the location of the screenshots directory in your `workflow.ini` file.
 *
 * Upload a new version will fail if you try to upload a copy with the same version number. The version
 * passed in the params is for a quick rejection. If you pass the correct version in the params but
 * an old version in the `workflow.ini` file, then the submission will be accepted, but it will fail
 * later when the workflow submission is fully processed.
 */

// Full path to workflow file
$workflow_path = '/path/to/workflow.alfredworkflow';
// Needs to be a Semantic Version
$workflow_version = '1.0.0';

// Create an array for the workflow; each workflow needs the 'file' and 'version' params
$workflow = [
	'file' => $workflow_path,
	'version' => $workflow_version
];

// Instantiate a new Packal object of type 'workflow', next is the params, and the last two are the username and password
$packal = new Packal( 'workflow', $workflow, $username, $password );
// Execute the submission. The result is returned as JSON, so decode that and print it to the console
print_r( json_decode( $packal->execute(), true ) );

/**
 * Submit a Theme Example
 *
 * Screenshots of the theme will be generated automatically on the website
 */

// Get this by going to Alfred Preferences -> Appearance, and ctrl+click a theme, and Copy Theme URL.
$theme_uri = 'alfred://theme/searchForegroundColor=rgba(0,0,0,1.00)&resultSubtextFontSize=1&searchSelectionForegroundColor=rgba(0,0,0,1.00)&separatorColor=rgba(87,60,70,0.49)&resultSelectedBackgroundColor=rgba(0,0,0,0.06)&shortcutColor=rgba(179,179,179,1.00)&scrollbarColor=rgba(0,0,0,0.10)&imageStyle=0&resultSubtextFont=Geneva&background=rgba(147,255,255,0.98)&shortcutFontSize=2&searchFontSize=3&resultSubtextColor=rgba(208,153,153,1.00)&searchBackgroundColor=rgba(127,170,0,0.47)&name=Example%20Theme&resultTextFontSize=4&resultSelectedSubtextColor=rgba(224,57,65,1.00)&shortcutSelectedColor=rgba(166,166,166,1.00)&widthSize=4&border=rgba(0,166,0,1.00)&resultTextFont=Baskerville&resultTextColor=rgba(53,53,53,1.00)&cornerRoundness=0&searchFont=Helvetica%20Neue%20Light&searchPaddingSize=1&credits=Shawn%20Patrick%20Rice&searchSelectionBackgroundColor=rgba(178,215,255,1.00)&resultSelectedTextColor=rgba(0,0,0,1.00)&resultPaddingSize=3&shortcutFont=Menlo';
// This should be a string; markdown is accepted, but do not get too crazy
$theme_description = 'Just an example theme.';
// Comma separated values
$theme_tags = 'example theme,example,nothing to see here,ugly';
// This should match the theme name from the URI
$theme_name = 'Example Theme';

// Create an array that is the theme parameters. The four keys used here are necessary
$theme = [
	'uri' => $theme_uri,
	'description' => $theme_description,
	'tags' => $theme_tags,
	'name' = $theme_name
];

// Instantiate a new Packal object of type 'theme', next is the params, and the last two are the username and password
$packal = new Packal( 'theme', $theme, $username, $password );
// Execute the submission. The result is returned as JSON, so decode that and print it to the console
print_r( json_decode( $packal->execute(), true ) );

/**
 * Submit a Report Example
 *
 * Reports are for workflows and should not be bug reports (direct those to the workflow's author). Instead,
 * reports should be identify a version of a workflow that has things like a virus (while Packal.org runs
 * a virus scan on each uploaded workflow file, there is always the possibility of it missing a virus), malicious
 * code (i.e. `rm -fR /`) or other bits that are damaging. Basically, if the workflow causes harm to a user's
 * system, then it should be reported. Reports will be reviewed, and workflows that are deemed malicious
 * (intentionally or not) will be removed from Packal.org.
 *
 * You will probably never use this part of the Packal class, but it's there.
 */

// Find this on Packal.org as the revision id of the workflow
$report_workflow_revision_id = 589;
// Think of this as a subject header. Try to use 'Malicious Code' or 'Virus Detected'
$report_type = 'Malicious Code';
// Please be specific so we know what to look for
$report_message = 'This is a simple message that explains the problem with the stated workflow revision';

// Create an array that is the report parameters. The three keys used here are necessary
$report = [
	'workflow_revision_id' => $report_workflow_revision_id,
	'type' => $report_type,
	'message' => $report_message
];

// Instantiate a new Packal object of type 'report', next is the params, and the last two are the username and password
$packal = new Packal( 'report', $report, $username, $password );
// Execute the submission. The result is returned as JSON, so decode that and print it to the console
print_r( json_decode( $packal->execute(), true ) );